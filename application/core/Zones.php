<?php

class Zones {
	
	//private $zones_paddock_id;	
	public $zones = array();

	public function __construct($paddock_id) {

		//$this->zones_paddock_id = $paddock_id;
		$this->setZonesProperties($paddock_id);
	}
	
	private function setZonesProperties($paddock_id){       
       
        $sql = "SELECT zone_id FROM zone
                WHERE paddock_id =:paddock_id";
        
        $r = self::getZonesByPaddockID($sql, $paddock_id);	

		
		foreach ($r as $z) {
			//$zone_id = $z->zone_id;
			//$this->zone = new Zone($this->zone_id);
			//$this->zone = new Zone($z->zone_id);			
			//$this->zones[] = $this->zone;	
			$this->zones[] = new Zone($z->zone_id);	
		}
    }
	
	private function getZonesByPaddockID($sql, $paddock_id){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        //$stmt->execute(array(':paddock_id' => $this->zones_paddock_id));
		$stmt->execute(array(':paddock_id' => $paddock_id));		
        
        // fetchAll() is the PDO method that gets multiple results
        return $stmt->fetchAll();         
    } 	
}
