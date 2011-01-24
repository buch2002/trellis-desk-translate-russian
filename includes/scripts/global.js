function get_by_id(id)
{
	element = null;

	if ( document.getElementById )
	{
		element = document.getElementById(id);
	}
	else if ( document.all )
	{
		element = document.all[id];
	}
	else if ( document.layers )
	{
		element = document.layers[id];
	}

	return element;
}

function clear_value(myid,deflt)
{
	if ( deflt )
	{
		if ( deflt == myid.value )
		{
			myid.value='';
		}
	}
	else
	{
		myid.value='';
	}
}

function reset_value(myid,deflt)
{
	if ( myid.value == '' )
	{
		myid.value = deflt;
	}
}

function show_hide(name, nocookie)
{
	div1 = null;

	div1 = get_by_id(name);

	if ( div1 )
	{
	 	if ( div1.style.display == 'none' )
	 	{
	 		div1.style.display = '';

	 		if ( ! nocookie ) set_cookie( 'hdsh_'+name, 1, 365 );
	 	}
	 	else
	 	{
	 		div1.style.display = 'none';

	 		if ( ! nocookie ) set_cookie( 'hdsh_'+name, 0, 365 );
	 	}
	}
}

function set_hide(name)
{
	div1 = null;

	div1 = get_by_id(name);

	if ( div1 )
	{
	 	if ( div1.style.display == 'none' )
	 	{
	 		set_cookie( 'hdsh_'+name, 1, 365 );
	 	}
	 	else
	 	{
	 		set_cookie( 'hdsh_'+name, 0, 365 );
	 	}
	}
}

function set_cookie(name,value,days)
{
	if ( value == 0 || value == "" )
	{
		days = -1;
		value == "";
	}

 	if ( days )
 	{
		date = new Date();
		date.setTime( date.getTime() + ( days*24*60*60*1000 ) );
		expires = "; expires="+date.toGMTString();
	}
	else
	{
		expires = "";
	}

	document.cookie = name+"="+escape(value)+expires+"; path=/";
}

function read_cookie(name)
{
	cookie = document.cookie;
	ind = cookie.indexOf( name );

	if ( ind == -1 || name == "" )
	{
		return "";
	}

	ind1 = cookie.indexOf( ';', ind );

	if ( ind1 == -1 )
	{
		ind1=cookie.length;
	}

	return unescape( cookie.substring( ind+name.length+1, ind1 ) );
}

function load_show_hide(to_load)
{
	each_load = to_load.split(',');

	for ( i=0; i < each_load.length; i++ )
	{
		sh_id = each_load[i];
		sh_value = read_cookie( 'hdsh_'+sh_id );

		if ( sh_value )
		{
			div1 = get_by_id( sh_id );

			if ( div1 )
			{
				div1.style.display = '';
			}
		}
	}
}

function tdcheck_all(ourform)
{	
	for ( i=0; i < ourform.elements.length; i++ )
	{
		e = ourform.elements[i];
		
		if ( ( e.name != 'check_all' ) && ( e.type == 'checkbox' ) )
		{
			e.checked = ourform.check_all.checked;
		}
	}
}