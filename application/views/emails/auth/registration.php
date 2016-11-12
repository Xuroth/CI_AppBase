<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php echo SITE_TITLE . ' - New Account Registration' ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0" />
</head>
<body>
	<div style="color: #12bdac">
		Welcome to <?php echo SITE_TITLE ?>, <?php echo $viewData->username ?>!
	</div>
	<div>
		You have created a new account, here is the confimation link: <a href="<?php echo $viewData->link ?>">Confirm</a>
	</div>
	<div>
		If the link doesn't work, try copying the below and pasting it in a broswer window:<br>
		<?php echo $viewData->link; ?><br>
		<br>
		If that also does not work, go to <a href="<?php echo base_url('confirmRegistration') ?>"><?php echo base_url('confirmregistration'); ?></a> and input the code below: <br>
		<br>
		<?php echo $viewData->code; ?>
	</div>
	<div>
		If you did not register for an account at <?php echo SITE_TITLE ?>, just disregard this message. Someone likely typed an incorrect email while registering. The IP Address used during registration was: <?php echo $viewData->ipAddress ?>
	</div>
</body>
</html>