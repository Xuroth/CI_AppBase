<h1>Register</h1>

<?php

$this->load->helper('form');

$formAttributes = array(
	'class'	=>	'form-horizontal',
	'id'	=>	'registerForm'
);

echo form_open('auth/register', $formAttributes);

$data = array(
	'id'			=>	'username',
	'name'			=>	'username',
	'value'			=>	set_value('username'),
	'placeholder'	=>	'Username',
	'max_length'	=>	'20',
	'class'			=>	'form-control'
);

echo form_input($data);

$data = array(
	'name'			=>	'email',
	'id'			=>	'email',
	'value'			=>	set_value('email'),
	'placeholder'	=>	'Email Address',
	'max_length'	=>	'50',
	'class'			=>	'form-control'
);

echo form_input($data);

$data = array(
	'name'			=>	'emailConfirm',
	'id'			=>	'emailConfirm',
	'value'			=>	set_value('emailConfirm'),
	'placeholder'	=>	'Confirm Email',
	'max_length'	=>	'50',
	'class'			=>	'form-control'
);

echo form_input($data);

$data = array(
	'name'			=>	'password',
	'id'			=>	'password',
	'placeholder'	=>	'Password',
	'class'			=>	'form-control'
);

echo form_password($data);

$data = array(
	'name'			=>	'passwordConfirm',
	'id'			=>	'passwordConfirm',
	'placeholder'	=>	'Confirm Password',
	'class'			=>	'form-control'
);

echo form_password($data);

echo validation_errors('<span>', '</span><br>');

$data = array(
	'name'	=>	'submit',
	'value'	=>	'Submit',
	'class'	=>	'btn btn-primary'
);

echo form_submit($data);

echo form_close();