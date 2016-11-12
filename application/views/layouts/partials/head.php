<?php

	//Output Base Stylesheets
	$stylesheets = array(

		//Core: Bootstrap
		'Bootstrap v3.3.7'	=>	'bootstrap.min.css',

		//Fonts

		//Main

		//Global Plugins
	);
	
	foreach ($stylesheets as $comment => $stylesheet)
	{
		echo ' <!-- ' . $comment . ' -->' . link_tag(base_url('assets/css/'.$stylesheet));
	}
	
	//Check for any required CSS
	if ( isset( $pageData->stylesheets ) )
	{
		//Handler for defined stylesheets and CSS plugins
		foreach ( $pageData->stylesheets as $addonComment => $addonFile )
		{
			echo '<!-- AddOn: ' . $addonComment . ' -->' . link_tag( base_url( 'assets/css/addons/'.$addonFile ) );
		}
	}
	echo '<title>' . (isset($pageData->title)?$pageData->title:SITE_TITLE) . '</title>';

	//Link to override file
	echo '<!-- Master CSS (Override) -->' . link_tag( base_url( 'assets/css/master.css' ) );

	if ( isset( $pageData->css ) )
	{
		echo '<!-- Raw CSS -->' . $pageData->css;
	}
?>