<h1>Reset Password</h1>

<?php

$this->load->helper('form');

$formAttributes = array(
	'class'		=>	'form-horizontal',
	'id'			=>	'resetPasswordForm'
);

$hidden = array('token'	=>	$token);

echo form_open('auth/reset/'.$token, $formAttributes, $hidden);

$data = array(
	'name'			=>	'password',
	'id'				=>	'password',
	'placeholder'	=>	'Password',
	'class'			=>	'form-control'
);

echo form_password($data);

$data = array(
	'name'				=>	'passwordConfirm',
	'id'					=>	'passwordConfirm',
	'placeholder'	=>	'Confirm Password',
	'class'				=>	'form-control'
);

echo form_password($data);

$data = array(
	'name'		=>	'submit',
	'value'		=>	'Submit',
	'class'		=>	'btn btn-primary'
);

echo form_submit($data);

echo form_close();

var_dump($this->session->flashdata());