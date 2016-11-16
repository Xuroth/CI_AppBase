<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php echo SITE_TITLE . ' - Password Reset' ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0" />
</head>
<body>
	<div style="color: #12bdac">
		Hello, <?php echo $viewData->username ?>!
	</div>
	<div>
		We just received a request to change your password. Here is the confimation link: <a href="<?php echo $viewData->link ?>">Reset</a>
	</div>
	<div>
		If the link doesn't work, try copying the below and pasting it in a broswer window:<br>
		<?php echo $viewData->link; ?><br>
	</div>
	<div>
		If you did not register for an account at <?php echo SITE_TITLE ?>, just disregard this message. Your password will only be updated if you use the link in this email and set a new one. The IP Address used during request was: <?php echo $viewData->ipAddress ?>
	</div>
</body>
</html>