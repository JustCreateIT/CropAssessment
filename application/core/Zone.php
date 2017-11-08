<?php

class Zone {
	
	//public $zone_id;
	public $zone_name;
	public $zone_paddock_percentage;

	public function __construct($id) {
		
		//$this->zone_id = $id;
        $this->setZoneProperties($id);			
	}
	
	private function setZoneProperties($id){       
       
        $sql = "SELECT zone_name, zone_paddock_percentage FROM zone
                WHERE zone_id = :zone_id";
        
        $r = self::getZonePropertiesByID($sql, $id);	
		
		// Zone properties
        $this->zone_name = $r->zone_name;
		$this->zone_paddock_percentage = $r->zone_paddock_percentage;
    }
	private function getZonePropertiesByID($sql, $id){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
		
		//var_dump($this->zone_id);
        //$stmt->execute(array(':zone_id' => $this->zone_id));
        $stmt->execute(array(':zone_id' => $id));		
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();          
    } 
}
