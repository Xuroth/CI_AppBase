<h1>Login</h1>

<?php

$this->load->helper('form');

$formAttributes = array(
	'class'	=>	'form-horizontal',
	'id'	=>	'loginForm'
);

echo form_open('user/login', $formAttributes);

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
	'name'			=>	'password',
	'id'			=>	'password',
	'placeholder'	=>	'Password',
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

var_dump($this->session->flashdata());