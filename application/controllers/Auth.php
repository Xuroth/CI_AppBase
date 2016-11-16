<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function login()
	{
		//Check if user is already logged in
		if ( $this->session->userdata('authenticated') )
		{
			redirect('/');
		}
		
		//Load Library: Form Validation
		$this->load->library('form_validation');

		//Set validation rules
		$validationRules = array(
			array(
				'field'		=>	'username',
				'label'		=>	'Username',
				'rules'		=>	'required',
				'errors'	=>	array(
					'required'	=>	'%s is required.'
				)
			),
			array(
				'field'		=>	'password',
				'label'		=>	'Password',
				'rules'		=>	'required',
				'errors'	=>	array(
					'required'	=>	'%s is required.'
				)
			)
		);

		$this->form_validation->set_rules($validationRules);

		//Check is form was submitted, or display login page
		if ( $this->form_validation->run() == FALSE )
		{
			$pageData = array(
				'title'		=>	'Login',
				'module'	=>	'auth',
				'page'		=>	'login'
			);

			$data['pageData'] = (object)$pageData;

			$this->load->view('layouts/layout', $data);
		}
		else
		{
			$credentials = array(
				'username'	=>	$this->input->post('username'),
				'password'	=>	$this->input->post('password')
			);

			//Load Model: Auth_model
			$this->load->model('Auth_model');

			$user = $this->Auth_model->getUser($credentials);

			//Check if user was validated
			if ( is_string($user) )
			{
				//Display message
				$this->session->set_flashdata('error', $user);
				redirect('auth/login', 'refresh');
			}

			//Validate user status
			switch ($user->status) {
				case '0':
					$this->session->set_flashdata('info', 'You need to verify your account before you can login. If you haven\'t received the confirmation email, you can have us resend it.');
					$target = 'auth/login';
					break;
				
				case '1':
					$this->session->set_flashdata('success', 'Welcome back, {$user->username}!');
					$target = '/';
					$success = TRUE;
					break;

				case '2':
					$this->session->set_flashdata('error', 'Your account is currently restricted. To login, you first need to reset your password.');
					$target = 'auth/forgot';
					break;

				case '3':
					$this->session->set_flashdata('error', 'Your account is currently temporarily banned. It will be unbanned automatically once your ban expires.');
					$target = '/';
					break;

				case '4':
					$this->session->set_flashdata('error', 'Your account has been banned.');
					$target = '/';
					break;
			}

			if ( (isset($success)) AND ($success == TRUE) )
			{
				//Session Data
				$userData = array(
					'authenticated'	=>	TRUE,
					'username'		=>	$user->username,
					'role'			=>	$user->role
				);

				$this->session->set_userdata($userData);

			}

			redirect($target);
		}
	}

	public function logout()
	{
		//Check if user is already logged in
		if ( $this->session->userdata('authenticated') )
		{
			//First unset userdata
			$userData = array(
				'authenticated',
				'username',
				'role',
			);

			$this->session->unset_userdata($userData);

			//Destroy Session
			$this->session->sess_destroy();

		}
		
		//Redirect to root.
		redirect('/');
		
	}

	public function register()
	{
		//Check if user is already logged in
		if ( $this->session->userdata('authenticated') )
		{
			redirect('/');
		}

		//Load Library: Form Validation
		$this->load->library('form_validation');

		//Set validation rules
		$validationRules = array(
			array(
				'field'		=>	'username',
				'label'		=>	'Username',
				'rules'		=>	'required|min_length[3]|max_length[20]|alpha_dash|is_unique[users.username]',
				'errors'	=>	array(
					'required'		=>	'You must provide a %s.',
					'min_length'	=>	'Your {field} must be at least {param} characters.',
					'max_length'	=>	'Your {field} must not be more than {param} characters.',
					'alpha_dash'	=>	'Your %s may only contain letters, numbers, dashes and underscores.',
					'is_unique'		=>	'This %s is already in use. Please try another.'
				),
			),
			array(
				'field'		=>	'email',
				'label'		=>	'Email Address',
				'rules'		=>	'required|valid_email|max_length[50]|is_unique[users.email]',
				'errors'	=>	array(
					'required'		=>	'You must provide an %s.',
					'valid_email'	=>	'Your %s must be in valid email format.',
					'max_length'	=>	'Your {field} must be less than {param} characters.',
					'is_unique'		=>	'There is already an account registered with this email.'

				),
			),
			array(
				'field'		=>	'emailConfirm',
				'label'		=>	'Confirm Email',
				'rules'		=>	'required|valid_email|matches[email]',
				'errors'	=>	array(
					'required'		=>	'You must confirm your email.',
					'valid_email'	=>	'Your email must be in valid email format.',
					'matches'		=>	'{field} must be the same as your Email'
				),
			),
			array(
				'field'		=>	'password',
				'label'		=>	'Password',
				'rules'		=>	'required|min_length[8]|differs[username]',
				'errors'	=>	array(
					'required'	=>	'You must provide a %s.',
					'min_length'	=>	'Your {field} must be at least {param} characters',
					'differs'		=>	'Your {field} must not be your username.'
				),
			),
			array(
				'field'		=>	'passwordConfirm',
				'label'		=>	'Confirm Password',
				'rules'		=>	'required|matches[password]',
				'errors'	=>	array(
					'required'	=>	'You must confirm your password.',
					'matches'	=>	'This must match your password exactly.'
				),
			),
		);

		$this->form_validation->set_rules($validationRules);

		//Check if form was submitted, or display login page
		if ($this->form_validation->run() == FALSE )
		{
			$pageData = array(
				'title'		=>	'Register',
				'module'	=>	'auth',
				'page'		=>	'register'
			);

			$data['pageData'] = (object)$pageData;

			$this->load->view('layouts/layout', $data);
		}
		else
		{
			//Create token code (email verification)
			$token = md5(rand());
			//Create userdata array
			$userData = array(
				'username'			=>	$this->input->post('username'),
				'password'			=>	$this->input->post('password'),
				'email'				=>	$this->input->post('email'),
				'code'				=>	$token,
				'status'			=>	'0',
				'role'				=>	'1',
				'forcePassReset'	=> FALSE,
				'failedAttempts'	=>	'0',
				'joinDate'			=>	date('Y-m-d H:i:s'),
				'lastLogin'			=>	NULL,
				'lastIp'			=>	$this->input->ip_address()
			);

			//Load Model: Auth
			$this->load->model('Auth_model');

			//Pass userdata to Auth_model
			if ( $this->Auth_model->register($userData) )
			{
				//Load Email Settings from config file
				$this->config->load('email');

				$this->load->library('email');

				//Email Setup
				$this->email->from($this->config->item('smtp_user'), SITE_TITLE.' Registration');
				$this->email->to($this->input->post('email'));
				$this->email->subject('Your new account at '.SITE_TITLE);

				//Load array for email generation
				$viewData = array(
					'code'	=>	$token,
					'link'	=>	base_url('confirm/'.$token),
					'username'	=>	$this->input->post('username'),
					'ipAddress'	=>	$this->input->ip_address()
				);

				$data['viewData'] = (object)$viewData;

				//Load the Email:Regsitration Template
				$message = $this->load->view('emails/auth/registration', $data, TRUE);

				$this->email->message($message);

				//Send the registration email
				if ( $this->email->send() == FALSE )
				{
					show_error($this->email->print_debugger());
				}

				//Direct to registration confirmation
				$pageData = array(
					'title'		=>	'Registered',
					'module'	=>	'auth',
					'page'		=>	'registered'
				);

				$content = array(
					'username'	=>	$this->input->post('username'),
					'email'			=>	$this->input->post('email')
				);

				$data['pageData'] = (object)$pageData;
				$data['content']	=	(object)$content;

				$this->load->view('layouts/layout', $data);

			}
			else
			{
				//Database Error - Inform user, redirect to registration form.
				$this->session->set_flashdata('error', 'There was a problem completing the registration. Please try again later.');
				redirect('auth/register', 'refresh');
			}


		}
	}

	public function forgot()
	{
		//Check if user is already logged in
		if ( $this->session->userdata('authenticated') )
		{
			redirect('/');
		}

		$this->load->library('form_validation');

		$validationRules = array(
			array(
				'field'		=>	'email',
				'label'		=>	'Email Address',
				'rules'		=>	'required|valid_email|max_length[50]',
				'errors'	=>	array(
					'required'		=>	'You must provide an %s.',
					'valid_email'	=>	'Your %s must be in valid email format.',
					'max_length'	=>	'Your {field} must be less than {param} characters.'
				)
			)
		);

		$this->form_validation->set_rules($validationRules);

		if ( $this->form_validation->run() == FALSE )
		{
			//Setup View
		}
		else
		{
			//Send email to confirm account. Show confirmation.
		}
	}

	public function confirm($code = NULL)
	{

		//Verify a code was supplied
		if ( $code == NULL )
		{
			$this->session->set_flashdata('error', 'Unable to confirm this account.');
			redirect('/');
		}

		//Check if user is already signed in.
		if ( $this->session->userdata('authenticated') )
		{
			redirect('/');
		}

		//Load Model: Auth
		$this->load->model('Auth_model');

		$userData = $this->Auth_model->activateUserAccount($code);

		//Check if error was returned instead of userData
		if ( is_string($userData) )
		{
			$this->session->set_flashdata('error', $userData);
			redirect('/');
		}

		$this->session->set_flashdata('success', 'Your account has been verified! You may now login.');
		redirect('login');

	}

}