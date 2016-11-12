<?php
	//Initialize the HTML helper
	$this->load->helper('html');
?>
<html>
	<head>
		<?php $this->load->view('layouts/partials/head'); ?>
	</head>
	<body>
		<?php $this->load->view('layouts/partials/navigation'); ?>
		<?php 
			if ( isset( $pageData->module ) )
			{
				$this->load->view('modules/'.$pageData->module.'/'.$pageData->page);
			}
		?>
		<?php $this->load->view('layouts/partials/scripts'); ?>
	</body>
</html>
