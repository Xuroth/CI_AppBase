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
				case '0': //Not Verified
					$this->session->set_flashdata('info', 'You need to verify your account before you can login. If you haven\'t received the confirmation email, you can have us resend it.');
					$target = 'auth/login';
					break;
				
				case '1':	//Good
					$this->session->set_flashdata('success', 'Welcome back, {$user->username}!');
					$target = '/';
					$success = TRUE;
					break;

				case '2':	//Restricted/Locked out
					$this->session->set_flashdata('error', 'Your account is currently restricted. To login, you first need to reset your password.');
					$target = 'auth/forgot';
					break;

				case '3':	//Temp ban
					$this->session->set_flashdata('error', 'Your account is currently temporarily banned. It will be unbanned automatically once your ban expires.');
					$target = '/';
					break;

				case '4':	//Perma ban
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
			//Display form if not submitted
			$pageData = array(
				'title'		=>	'Forgot Password',
				'module'	=>	'auth',
				'page'		=>	'forgot'
			);

			$data['pageData'] = (object)$pageData;

			$this->load->view('layouts/layout', $data);
		}
		else
		{
			//Load Model: Auth
			$this->load->model('Auth_model');

			//Get User Data
			$userData = $this->Auth_model->getUserByEmail($this->input->post('email'));

			if ( $userData == NULL )
			{
				$this->session->set_flashdata('error', 'Unable to reset password for <i>'.$this->input->post('email')."</i>.");
				redirect('forgot');
			}

			//Check user status banned?
			if ( ( $userData->status == 3 ) OR ( $userData->status == 4 ) )
			{
				$this->session->set_flashdata('error', 'This account currently has a ban and cannot have it\'s password reset.');
				redirect('/');
			}
			//Generate token for reset
			$token = md5(rand());
			$this->Auth_model->setUserToken($userData->id, $token);

			$this->config->load('email');

			//Load Library: Email
			$this->load->library('email');

			//Email Setup
			$this->email->from($this->config->item('smtp_user'), SITE_TITLE.' Password Reset');
			$this->email->to($this->input->post('email'));
			$this->email->subject('Reset your password at '.SITE_TITLE);

			//Load Email template array
			$viewData = array(
				'code'	=>	$token,
				'link'	=>	base_url('reset/'.$token),
				'username'	=>	$userData->username,
				'email'			=>	$userData->email,
				'ipAddress'	=>	$this->input->ip_address()
			);

			$data['viewData'] = (object)$viewData;

			//Load the Email:Reset Template
			$message = $this->load->view('emails/auth/reset', $data, TRUE);

			$this->email->message($message);

			//Send Reset email
			if ( $this->email->send() == FALSE )
			{
				show_error($this->email->print_debugger());
			}
			else
			{
				$pageData = array(
					'title'		=>	'Email Sent',
					'module'	=>	'auth',
					'page'		=>	'forgotConfirm'
				);

				$data['pageData'] = (object)$pageData;

				$this->load->view('layouts/layout', $data);
			}

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

		//Check if error was returned instead of userData
		if ( !$this->Auth_model->activateUserAccount($code) )
		{
			$this->session->set_flashdata('error', $userData);
			redirect('/');
		}

		$this->session->set_flashdata('success', 'Your account has been verified! You may now login.');
		redirect('login');

	}

	public function reset($code = NULL)
	{
		//Verify code was supplied
		if ( $code == NULL )
		{
			$this->session->set_flashdata('error', 'Unable to reset password. Use the link supplied in the email that was sent to try again.');
			redirect('/');
		}

		//Load Library: Form Validation
		$this->load->library('form_validation');

		$validationRules = array(
			array(
				'field'				=>	'password',
				'label'				=>	'Password',
				'rules'				=>	'required|min_length[8]',
				'errors'			=>	array(
					'required'		=>	'You must provide a new %s.',
					'min_length'	=>	'Your {field} must be at least {param} characters'
				)
			),
			array(
				'field'				=>	'passwordConfirm',
				'label'				=>	'Confirm Password',
				'rules'				=>	'required|matches[password]',
				'errors'			=>	array(
					'required'	=>	'You must confirm your password.',
					'matches'		=>	'Your password confirmation needs to match the password you supplied'
				)
			)
		);

		$this->form_validation->set_rules($validationRules);

		if ( $this->form_validation->run() == FALSE )
		{
			//Show form (supply $token as hidden form field)
			$pageData = array(
				'title'		=>	'Reset Password',
				'module'	=>	'auth',
				'page'		=>	'resetPass'
			);

			$data['token']	= $code;

			$data['pageData'] = (object)$pageData;

			$this->load->view('layouts/layout', $data);
		}
		else
		{
			//Load Model: Auth
			$this->load->model('Auth_model');

			$userData = $this->Auth_model->getUserByCode($this->input->post('token'));

			if ( $userData == NULL )
			{
				$this->session->set_flashdata('error', 'Cannot update password! Please re-check the email for the correct link.');
				redirect('reset/'.$code);
			}

			$this->Auth_model->resetUserPassword($userData->id, $this->input->post('password'));

			$this->session->set_flashdata('success', 'You have successfully reset your password. Please login below with your new password.');
			redirect('login');

		}
	}

}