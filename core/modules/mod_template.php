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
|    | Template Module
#======================================================
*/

class mod_template {

	var $config = array( 'use_variables' => 1, 'use_conditionals' => 1 );
	var $tpl_vars = array();
	var $literal = array();
	var $output = "";

	function config_set($key, $value)
	{
		$this->config[ $key ] = $value;
	}

	function set_var($key, $value)
	{
		$this->tpl_vars[ $key ] = $value;
	}

	function compile_variable($variable)
	{
		$variable = preg_replace( '/\$([a-zA-Z0-9_]+)/', '$this->tpl_vars[\'$1\']', $variable );

		$variable = '<?php echo '. $variable .' ?>';

		return $variable;
	}

	function parse_variable_txt($variable)
	{
		return preg_replace( '/\$([a-zA-Z0-9_]+)/', '$this->tpl_vars[\'$1\']', $variable );
	}

	function compile_if_open_tag($tag)
	{
		preg_match( '/([^\s]+) ?(.+)/s', $tag, $matches );

		$arg = $this->parse_variable_txt( $matches[2] );

		return '<?php if ( '. $arg .' ) { ?>';
	}

	function compile_elseif_tag($tag)
	{
		preg_match( '/([^\s]+) ?(.+)/s', $tag, $matches );

		$arg = $this->parse_variable_txt( $matches[2] );

		return '<?php } elseif ( '. $arg .' ) { ?>';
	}

	function compile_else_tag()
	{
		return '<?php } else { ?>';
	}

	function compile_if_close_tag()
	{
		return '<?php } ?>';
	}

	function compile_foreach_open_tag( $variable, $arg )
	{
		if ( $variable{0} == '$' )
		{
			$variable = $this->parse_variable_txt( $variable );
		}

		return '<?php foreach ( '. $variable .' as $this->tpl_vars[\''. substr( $arg, 1 ) .'\'] ) { ?>';
	}

	function compile_foreach_close_tag()
	{
		return '<?php } ?>';
	}

	function compile_include_tag( $template )
	{
		if ( $template{0} == '$' )
		{
			$template = $this->tpl_vars[ substr( $template, 1 ) ];
		}

		#return $this->compile_tpl( file_get_contents( $this->config['tpl_path'] . $template ) );
		return '<?php eval(\' ?>\'. $this->compile_tpl( file_get_contents( $this->config[\'tpl_path\'] .\''. $template .'\' ) ) .\'<?php \'); ?>';
	}

	function compile_literal_tag()
	{
		list (,$literal) = each($this->literal);

		return "<?php echo '" . str_replace("'", "\'", str_replace("\\", "\\\\", $literal)) . "'; ?>\n";
	}

	function compile_function( $func, $arg1='', $arg2='', $arg3='', $full='' )
	{
		switch ( $func )
		{
			case 'if':
				return $this->compile_if_open_tag( $full );
			break;
			case 'elseif':
				return $this->compile_elseif_tag( $full );
			break;
			case 'else':
				return $this->compile_else_tag();
			break;
			case '/if':
				return $this->compile_if_close_tag();
			break;
			case 'foreach':
				return $this->compile_foreach_open_tag( $arg1, $arg2 );
			break;
			case '/foreach':
				return $this->compile_foreach_close_tag();
			break;
			case 'include':
				return $this->compile_include_tag( $arg1 );
			break;
			case 'literal':
				return $this->compile_literal_tag();
			break;
		}
	}

	function compile_tag($tag)
	{
		preg_match( '/([^\s]+) ?([^\s]+)? ?([^\s]+)? ?([^\s]+)?/s', $tag, $matches );

		if ( $matches[1]{0} == '$' )
		{
			return $this->compile_variable( $matches[1] . $matches[2] . $matches[3] . $matches[4] );
		}

		return $this->compile_function( $matches[1], $matches[2], $matches[3], $matches[4], $tag );
	}

	function compile_tpl($temp_output)
	{
		// Strip Comments
		#$temp_output = preg_replace( '/\{\*.*?\*\}/se', "", $temp_output );

		// Remove PHP Tags
		$temp_output = preg_replace( '/(<\?(php|=|$)?)/i', '<?php echo \'$1\'?>', $temp_output );

		// Remove & Store Literal Blocks
		preg_match_all( '/\{literal\}(.*?)\{\/literal\}/s', $temp_output, $_matches );
		$this->literal = $_matches[1];
		$temp_output = preg_replace( '/\{literal\}(.*?)\{\/literal\}/s', '{literal}', $temp_output );

		preg_match_all( '/\{\s*(.*?)\s*\}/s', $temp_output, $matches );
		$tags = $matches[1];

		$text = preg_split( '/\{.*?\}/s', $temp_output );

		$tag_count = 0; // Initialize for Security

		foreach ( $tags as $tag_to_compile )
		{
			$compiled_tags[] = $this->compile_tag( $tag_to_compile );

			$tag_count ++;
		}

		$compiled_text = ''; // Initialize for Security

		for ( $i = 0; $i < $tag_count; $i++ )
		{
			$compiled_text .= $text[$i] . $compiled_tags[$i];
		}

		$compiled_text .= $text[$i];

		return $compiled_text;
	}

	function display($template)
	{
		$raw = file_get_contents( $this->config['tpl_path'] . $template );
		$to_eval = $this->compile_tpl( $raw );
		#die ( highlight_string( $to_eval ) );
		ob_start();

		header ('Content-type: text/html; charset=utf-8');

		eval(' ?>'. $to_eval .'<?php ');

		$output = ob_get_contents();
		ob_end_clean();

		print $output;
	}

}

?>