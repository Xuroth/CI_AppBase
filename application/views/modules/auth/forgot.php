<h1>Forgot Password</h1>

<?php

$this->load->helper('form');

$formAttributes = array(
	'class'		=>	'form-horizontal',
	'id'			=>	'forgotPasswordForm'
);

echo form_open('auth/forgot', $formAttributes);

$data = array(
	'id'			=>	'email',
	'name'		=>	'email',
	'value'		=>	set_value('email'),
	'placeholder'	=>	'Email Address',
	'max_length'	=>	'50',
	'class'				=>	'form-control'
);

echo form_input($data);

$data	=	array(
	'name'	=>	'submit',
	'value'	=>	'Submit',
	'class'	=>	'btn btn-primary'
);

echo form_submit($data);

echo form_close();