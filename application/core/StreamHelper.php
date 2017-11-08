<?php

class StreamHelper {

	public function __construct() {
	}
	
	
	public static function postStream($data,$url){
		
		//$data = array('name' => 'Hagrid', 'age' => '36');
		$data_string = json_encode($data);

		$result = file_get_contents($url, null, stream_context_create(array(		
		'http' => array(
		'method' => 'POST',
		'header' => 'Content-Type: application/json' . "\r\n" . 
		'Content-Length: ' . strlen($data_string) . "\r\n",
		'content' => $data_string,
		),
		)));
		
		return $result;
	}

}
