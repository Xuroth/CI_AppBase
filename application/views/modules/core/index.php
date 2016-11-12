<h1>Home</h1>
<?php
if($this->session->userdata('authenticated'))
{
	echo 'You are logged in!';
}
