<?php

include_once('github.class.php');

class Gapi extends GitHub
{
	
	public function __construct($method, $args = array())
	{
		parent::__construct($method, $args);
	}



	protected function get_users($username)
	{
		if(!preg_match('/^[a-z0-9_-]{3,15}$/', $username)) throw new Exception('Invalid Username');
		return $this->request("users/$username");
	}


	protected function get_users_followers($username, $page = 1, $per_page = 20)
	{
		$page = intval($page);
		$per_page = intval($per_page);
		
		if(!preg_match('/^[a-z0-9_-]{3,15}$/', $username)) throw new Exception('Invalid Username');
		return $this->request("users/$username/followers?per_page=$per_page&page=$page");
	}
}


?>