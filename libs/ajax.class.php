<?php

include_once('gapi.class.php');

class Ajax
{
	private $res;

	public function __construct()
	{
		$this->res = null;
	}

	public function init()
	{
		if(empty($_POST['method'])) return;

		try
		{
			$gapi = new Gapi($this->post('method'), $this->post('args'));
			$this->res = $gapi->getResponse();
		} 
		catch(Exception $e)
		{
			$this->res = (object) array('_error'=> $e->getMessage());
		}

		$this->send();
	}


	private function post($name)
	{
		return isset($_POST[$name]) ? $_POST[$name] : null;
	}


	private function send()
	{
		header('HTTP/1.1 '. (isset($this->res->_error) ? '403 Forbidden' : '200 OK'));
		header('Content-Type: application/json');
		echo json_encode($this->res);
		exit;
	}
}
?>