<?php 
/*
$ curl -d @your_filename.json -H "Content-Type: application/json" -i "https://www.googleapis.com/geolocation/v1/geolocate?key=YOUR_API_KEY"

*/

class Curl{
	
	
	public static function curlPost($data,$url){
		//$data = array("name" => "Hagrid", "age" => "36");                                                                    
		$data_string = json_encode($data);                                                                                   
																															 
		//$ch = curl_init('http://api.local/rest/users');  

		$ch = curl_init($url);                                                                     
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
																															 
		$result = curl_exec($ch);
		
		return $result;
	}
	

	
}


?>