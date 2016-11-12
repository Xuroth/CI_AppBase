<?php

$scripts = array(

	//Core
	'jQuery v3.1.1'	=>	'jquery-3.1.1.js',
	'Bootstrap v3.3.7' => 'bootstrap.min.js'
);

foreach ( $scripts as $comment => $file )
{
	//Easy load all global scripts
	echo '<!-- ' . $comment . ' --> <script src="' . base_url('assets/js/') . $file . '"></script>';
}

//Check if any addon scripts are required
if ( isset($pageData->scripts) )
{
	foreach ( $pageData->scripts as $addonComment => $addonFile )
	{
		//Easy load any and all required local scripts (passed from controller)
		echo '<!-- AddOn: ' . $addonComment . ' --> <script src="' . base_url('assets/js/addons/') . $addonFile . '"></script>';
	}
}

//Check if any raw js is passed from controller
if ( isset($pageData->js) )
{
	echo '<!-- Raw JS --> <script type="text/javascript">' . $pageData->js . '</script>';
}