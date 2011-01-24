<?php

/*
#======================================================
|    ACCORD5 Core v1.0
|    =====================================
|    by someotherguy
|    (c) 2007 ACCORD5
|    http://www.accord5.com/
|    =====================================
|    Email: sog@accord5.com
#======================================================
|    @ Version: v1.0
|    @ Version Int: 1.0.0101
|    @ Version Num: 100101
|    @ Build: 0001
#======================================================
|    | Email Module
#======================================================
*/

class mod_email {

	var $config			= array( 'method' => 'native', 'smtp_host' => 'locahost', 'smtp_port' => 25, 'smtp_user' => '',
								 'smtp_pass' => '', 'smtp_encrypt' => 0, 'sendmail_custom' => '', 'from_email' => '',
								 'from_name' => '', 'use_html' => true );

	#=======================================
	# @ Initialize
	#=======================================
	
	function initialize($config)
	{
		$this->update_config( $config );
		
		require_once CORE_PATH .'includes/swift/Swift.php';
		
		if ( $this->config['method'] == 'native' )
		{
			require_once CORE_PATH .'includes/swift/Swift/Connection/NativeMail.php';
			
			$this->swift = new Swift( new Swift_Connection_NativeMail() );
		}
		elseif ( $this->config['method'] == 'smtp' )
		{
			require_once CORE_PATH .'includes/swift/Swift/Connection/SMTP.php';
			
			if ( $this->config['smtp_encrypt'] == 'ssl' )
			{
				$smtp = new Swift_Connection_SMTP( $this->config['smtp_host'], $this->config['smtp_port'], SWIFT_SMTP_ENC_SSL );
			}
			elseif ( $this->config['smtp_encrypt'] == 'tls' )
			{
				$smtp = new Swift_Connection_SMTP( $this->config['smtp_host'], $this->config['smtp_port'], SWIFT_SMTP_ENC_TLS );
			}
			else
			{
				$smtp = new Swift_Connection_SMTP( $this->config['smtp_host'], $this->config['smtp_port'] );
			}
			
			if ( $this->config['smtp_user'] ) $smtp->setUsername( $this->config['smtp_user'] );
			if ( $this->config['smtp_pass'] ) $smtp->setPassword( $this->config['smtp_pass'] );
			
			$this->swift = new Swift( $smtp );
		}
		elseif ( $this->config['method'] == 'sendmail' )
		{
			require_once CORE_PATH .'includes/swift/Swift/Connection/Sendmail.php';
			
			if ( $this->config['sendmail_custom'] )
			{
				$this->swift = new Swift( new Swift_Connection_NativeMail( $this->config['sendmail_custom'] ) );
			}
			else
			{
				$this->swift = new Swift( new Swift_Connection_NativeMail() );
			}
		}
	}

	#=======================================
	# @ Load Decorator
	#=======================================

	function load_decorator()
	{
		require_once CORE_PATH .'includes/swift/Swift/Plugin/Decorator.php';
	}

	#=======================================
	# @ Update Config
	#=======================================

	function update_config($config)
	{
		if ( ! is_array( $config ) )
		{
			$this->core->core_die("Variable passed to 'update_config' is not an array.");
		}

		while ( list( $bkey, $bval ) = each( $config ) )
		{
			$this->config[ $bkey ] = $bval;
		}
	}

	#=======================================
	# @ Validate Email
	#=======================================

	function validate_email($email)
	{
		if( ereg("^([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])*$", $email) )
		{
			return $email;
		}
		else
		{
			return false;
		}
	}

	#=======================================
	# @ Add Recipient
	#=======================================

	function add_recipient($to_add)
	{
		if ( ! $to_add ) return false;
		
		if ( ! $this->recipients ) $this->recipients = new Swift_RecipientList();

		if ( is_array( $to_add ) )
		{
			while ( list( , $recpnt ) = each( $to_add ) )
			{
				if ( $this->validate_email($recpnt) )
				{
					$this->recipients->addTo( $recpnt );
				}
			}
		}
		else
		{
			if ( $this->validate_email($to_add) )
			{
				$this->recipients->addTo( $to_add );
			}
		}
	}

	#=======================================
	# @ Set Subject
	#=======================================

	function set_subject($subject)
	{
		if ( ! $subject ) return false;
		
		$this->message = new Swift_Message( $subject );
	}

	#=======================================
	# @ Add Message
	#=======================================

	function add_message($to_add_msg, $type='')
	{
		if ( ! $to_add_msg ) return false;
		
		if ( $type )
		{
			$this->message->attach( new Swift_Message_Part( $to_add_msg, $type ) );
		}
		else
		{
			$this->message->attach( new Swift_Message_Part( $to_add_msg ) );
		}
	}

	#=======================================
	# @ Set Replacements
	#=======================================

	function set_replacements($replacements)
	{
		$this->swift->attachPlugin( new Swift_Plugin_Decorator( $replacements ), "decorator" );
	}

	#=======================================
	# @ Send Email
	#=======================================

	function send_email()
	{
		return $this->swift->batchSend( $this->message, $this->recipients, new Swift_Address( $this->config['from_email'], $this->config['from_name'] ) );
	}

	#=======================================
	# @ Flush
	#=======================================

	function flush()
	{
		$this->recipients->flush();
	}

}
?>