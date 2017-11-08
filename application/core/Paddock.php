<?php

class Paddock extends Farm {
	
	public $paddock_id;
	public $paddock_name;	
    public $paddock_address;
    public $paddock_area;
	public $paddock_longitude;
	public $paddock_latitude;
	public $paddock_google_place_id;
	public $paddock_google_latlong_paths;
	public $paddock_google_area;
    

	public function __construct($farm_id, $paddock_id) {
		
		// initialise parent class
		parent::__construct($farm_id);
        
		// set the paddock_id and initialise paddock properties
        $this->paddock_id = $paddock_id;
        $this->setPaddockProperties();		
		
	}
	
    private function setPaddockProperties(){       
       
        $sql = "SELECT * FROM paddock
                WHERE paddock_id =:paddock_id";
        
        $r = self::getPaddockPropertiesByID($sql);	
 
        // Paddock properties
        $this->paddock_name = $r->paddock_name;
        $this->paddock_address = $r->paddock_address;
        $this->paddock_area = $r->paddock_area;
        $this->paddock_longitude = $r->paddock_longitude;
        $this->paddock_latitude = $r->paddock_latitude;        
		$this->paddock_google_place_id = $r->paddock_google_place_id;
		$this->paddock_google_latlong_paths = $r->paddock_google_latlong_paths;
		$this->paddock_google_area = $r->paddock_google_area;		
        
    }
	
	private function getPaddockPropertiesByID($sql){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        $stmt->execute(array(':paddock_id' => $this->paddock_id));
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();         
    } 
	
	public function getPaddockArea(){
		return $this->paddock_area;
	}

}
