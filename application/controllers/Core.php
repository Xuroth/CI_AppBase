<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Core extends CI_Controller {

	public function __construct() {
		parent::__construct();

		//Check if user is already logged in
	}

	public function index() {
		
		$pageData = array(
			'title' => 'Home',
			'module' => 'core',
			'page' => 'index'
		);

		$data['pageData'] = (object)$pageData;

		$this->load->view('layouts/layout', $data);
	}
}