<?php
defined('BASEPATH') OR exit('No direct script access allowed.');

class Auth_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function register($userData)
	{
		//Load Library: BCrypt
		$this->load->library('Bcrypt');

		$userData['password'] = $this->bcrypt->hash_password($userData['password']);

		if ( $this->db->insert('users', $userData) )
		{
			return TRUE;
		}
		return FALSE;
	}

	public function getUser($credentials)
	{
		$query = $this->db->select('users.id, users.username, users.password, users.status, users.role, users.forcePassReset, users.failedAttempts')
		  				  ->where('users.username', $credentials['username'])
						  ->from('users')
						  ->limit(1)
						  ->get();

		$result = $query->row(0);

		//Load Library: BCrypt
		$this->load->library('Bcrypt');

		//Verify submitted password
		if ( $this->bcrypt->check_password($credentials['password'], $result->password) )
		{
			//Check forced password reset flag
			if ( $result->forcePassReset == TRUE )
			{
				return 'You need to reset your password before you can login.';
			}

			return $result;
		}
		else
		{
			//Failed attempts handler
			$failedAttempts = $result->failedAttempts + 1;
			if ( $failedAttempts >= 3 )
			{
				//Set forced password reset flag
				$token = md5(rand());
				$data = array('failedAttempts' => $failedAttempts, 'status'	=> 2, 'forcePassReset' => TRUE, 'code' => $token);
				$lockUser = TRUE;
			}
			else
			{
				//Set failedAttempts
				$data = array('failedAttempts' => $failedAttempts);
				$lockUser = FALSE;
			}

			//Update user's data
			$this->db->where('id', $result->id);
			$this->db->update('users', $data);

			//Log failed login attempt
			$data = array(
				'userId'		=>	$result->id,
				'attemptedOn'	=>	date('Y-m-d H:i:s'),
				'adminCP'		=>	FALSE,
				'sourceIp'		=>	$this->input->ip_address()
			);

			$this->db->insert('failedLogins', $data);

			//Return failed validation message
			if ( $lockUser == TRUE )
			{
				$string = 'The credentials you entered are invalid. This account has been locked due to too many failed attempts.';
			}
			else
			{
				$string = 'The credentials you entered are invalid. Please check the information you submitted, and try again.';
			}

			return $string;
		}
	}

	public function getUserByEmail($email)
	{
		$query = $this->db->select('users.id, users.username, users.email, users.status')
											->where('users.email', $email)
											->from('users')
											->limit(1)
											->get();

		if ( $query->num_rows() == 1 )
		{
			return $query->row(0);
		}
		return NULL;
	}

	public function setUserToken($user, $token)
	{
		$userData = array(
			'code'	=>	$token
		);

		$this->db->where('id', $user);
		$this->db->update('users', $userData);

		return;
	}

	public function getUserByCode($code)
	{
		$query = $this->db->select('users.id')
											->where('code', $code)
											->limit(1)
											->from('users')
											->get();

		if ( $query->num_rows() == 1 )
		{
			return $query->row(0);
		}
		return NULL;
	}

	public function resetUserPassword($user, $password)
	{
		//Load Library: Bcrypt
		$this->load->library('bcrypt');

		$hashedPassword = $this->bcrypt->hash_password($password);

		$userData = array(
			'password'				=>	$hashedPassword,
			'forcePassReset'	=>	NULL,
			'failedAttempts'	=>	0,
			'code'						=>	NULL
		);

		$this->db->where('id', $user);
		$this->db->update('users', $userData);
		return;
	}

	public function activateUserAccount($code)
	{
		$userData = array(
			'status'	=>	'1',
			'code'		=>	NULL
		);

		$this->db->where('code', $code);
		$this->db->update('users', $userData);
		return;
	}
}