<?php

class GitHub
{
	private $base = 'https://api.github.com/';
	private $debug = false;
	private $response = null;

	public function __construct($method, $args = array())
	{
		if(!method_exists($this, $method)) throw new Exception('Invalid method');
		$this->response = call_user_func_array( array($this, $method), $args);
	}
	
	public function getResponse()
	{
		return $this->response;
	}
	


	protected function request($path)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->base.$path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		$response = @json_decode(trim(curl_exec($ch)));
		$info = curl_getinfo($ch);
		if($this->debug)
		{
			print_r($info);
			exit;
		}
		curl_close($ch);
		if($info['http_code'] != '200') throw new Exception( empty($response->message) ? 'GitHub Error: '.$info['http_code'] : $response->message );
		
		return $response;
	}
}
?>