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
|    @ Version: v1.0 Alpha 1
|    @ Version Int: 1.0.0101
|    @ Version Num: 100101
|    @ Build: 0001
#======================================================
|    | A5 Core Class
#======================================================
*/

#=============================
# Define Our Paths
#=============================

define( "CORE_PATH", dirname( __FILE__ ) ."/" );
define( "CACHE_PATH", CORE_PATH ."cache/" );

#=============================
# Start Our Main Class
#=============================

class iftcore {

	var $cache				= array();
	var $new_cache			= array();

	#=======================================
	# @ Core Function
	# Boots up the core.
	#=======================================

	function iftcore()
	{
		#=============================
		# Load Cache Data
		#=============================

		$this->load_cache();
	}

	#=======================================
	# @ Load Cache Data
	# Loads the flat-file cached data.
	#=======================================

	function load_cache()
	{
		if ( ! is_dir( CACHE_PATH ) )
		{
			$this->core_die("Cache directory not found.");
		}

		if ( $handle = opendir( CACHE_PATH ) )
		{
			while ( $cache = readdir( $handle ) )
			{
				if ( preg_match( "#^(.+?)\.IFT$#", $cache, $name ) )
				{
					$raw_data = file_get_contents( CACHE_PATH . $cache );

					$data = unserialize( base64_decode( $raw_data ) );

					$this->cache[ base64_decode( $name[1] ) ] = $data;
				}
			}
		}
		else
		{
			$this->core_die("Could not open the cache directory for reading.");
		}
	}

	#=======================================
	# @ CHMOD Cache
	# CHMODs the cache files to 0777.
	#=======================================

	function chmod_cache()
	{
		if ( ! is_dir( CACHE_PATH ) )
		{
			$this->core_die("Cache directory not found.");
		}

		if ( $handle = opendir( CACHE_PATH ) )
		{
			while ( $cache = readdir( $handle ) )
			{
				if ( preg_match( "#^(.+?)\.IFT$#", $cache, $name ) )
				{
					@chmod( CACHE_PATH . $cache, 0777 );
				}
			}
		}
		else
		{
			$this->core_die("Could not open the cache directory for reading.");
		}
	}

	#=======================================
	# @ Load Database Module
	# Loads a database module.
	#=======================================

	function load_db_module($mname)
	{
		if ( ! file_exists( CORE_PATH ."databases/db_". $mname .".php" ) )
		{
			$this->core_die("The DB Module you tried to activate could not be found.");
		}

		require_once CORE_PATH ."databases/db_". $mname .".php";

		$mname_class = "db_". $mname;

		$this->db = new $mname_class();
		$this->db->core =& $this;
	}

	#=======================================
	# @ Load Module
	# Loads a regular module.
	#=======================================

	function load_module($mname)
	{
		if ( ! $this->$mname )
		{
			if ( ! file_exists( CORE_PATH ."modules/mod_". $mname .".php" ) )
			{
				$this->core_die("The Module you tried to activate could not be found.");
			}

			require_once CORE_PATH ."modules/mod_". $mname .".php";

			$mname_class = "mod_". $mname;

			$this->$mname = new $mname_class();
			$this->$mname->core =& $this;
		}
	}

	#=======================================
	# @ Add Cache Data
	# Addes data to the cache array.
	#=======================================

	function add_cache($name, $data, $delete_first=0)
	{
		#=============================
		# Are We An Array?
		#=============================

		if ( is_array( $data ) )
		{
			#=============================
			# Reserved Names
			#=============================

			if ( $name == 'cdate' )
			{
				$this->core_die("You cannot use the name 'cdate' for your cache arrays as it is reserved for A5 Core use only.");
			}

			#=============================
			# Do We Exist Already?
			#=============================

			if ( $this->cache[ $name ] )
			{
				if ( $delete_first )
				{
					$this->new_cache[ $name ] = $data;
				}
				else
				{
					$this->new_cache[ $name ] = array_merge( $this->cache[ $name ], $data );
				}
			}
			else
			{
				$this->new_cache[ $name ] = $data;
			}

			#=============================
			# Cache Date
			#=============================

			$this->new_cache['cdate'][ $name ] = time();
		}
		elseif ( $delete_first )
		{
			$this->new_cache[ $name ] = array();
			$this->new_cache['cdate'][ $name ] = time();
		}
	}

	#=======================================
	# @ Write Cache Data
	# Writes the cache data to the flat-files.
	#=======================================

	function write_cache()
	{
		#=============================
		# Write Each Cache Array
		#=============================

		if ( $this->new_cache['cdate'] && is_array( $this->cache['cdate'] ) )
		{
			$this->new_cache['cdate'] = array_merge( $this->cache['cdate'], $this->new_cache['cdate'] );
		}

		if ( $this->new_cache )
		{
			while ( list( $name, $raw_data ) = each( $this->new_cache ) )
			{
				#=============================
				# Prepare
				#=============================

				$cache_file = CACHE_PATH . base64_encode( $name ) .".IFT";

				$data = base64_encode( serialize( $raw_data ) );

				#=============================
				# Write :D *mmwhahaha*
				#=============================

				if ( $handle = @fopen( $cache_file, "w" ) )
				{
					fwrite( $handle, $data );
					fclose( $handle );

					@chmod( $cache_file, 0777 );
				}
				else
				{
					$this->core_die("Could not write to the cache file '". $cache_file ."'.");
				}
			}
		}
	}

	#=======================================
	# @ Clear Cache
	# Removes all the cache files in the
	# cache directory.
	#=======================================

	function clear_cache()
	{
		while ( list( $name, $data ) = each( $this->cache ) )
		{
			$cache_file = CACHE_PATH . base64_encode( $name ) .".IFT";

			if ( file_exists( $cache_file ) )
			{
				@unlink( $cache_file );
			}
		}
	}

	#=======================================
	# @ Shut Down
	# Runs final shutdown queries.
	#=======================================

	function shut_down_q()
	{
		if ( $this->db->allow_shutdown )
		{
			if ( is_array( $this->db->shutdown_queries ) )
			{
				while ( list( , $q ) = each( $this->db->shutdown_queries ) )
				{
					$this->db->query( $q );
				}
			}
		}
	}

	#=======================================
	# @ Shut Down
	# Runs final processes such as writing
	# to cache files.
	#=======================================

	function shut_down($redirect="")
	{
		#=============================
		# Write Cache Files
		#=============================

		$this->write_cache();

		#=============================
		# Redirect
		#=============================

		if ( $redirect )
		{
			header('Refresh: 1; URL='. $redirect);
		}
	}

	#=======================================
	# @ Core Die
	# Oh no! The A5 Core had a boo-boo.
	#=======================================

	function core_die($error="")
	{
		print "There has been a fatal error with the A5 Core.";
		print "<br /><br />--------------<br /><br />";
		print $error;

		exit();
	}

}

?>