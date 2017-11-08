


<?php

class SelectedFarm {
	
	// Farm identifiers
	public $farm_id;
    public $paddock_id;	
    public $crop_id;
    public $variety_id	


	public function __construct() {
        // set the farm_id and initialise properties
        $this->farm_id = $farm_id;		
        $this->setFarmProperties();
    }
    
    private function setFarmProperties(){       
       
        $sql = "SELECT * FROM farm
                WHERE farm_id =:farm_id";
        
        $r = self::getFarmPropertiesByID($sql);			
 
        // Location and User properties
        $this->farm_name = $r->farm_name;
        $this->farm_contact_firstname= $r->farm_contact_firstname;
        $this->farm_contact_lastname = $r->farm_contact_lastname;
        $this->farm_email_address = $r->farm_email_address;
        $this->farm_phone_number = $r->farm_phone_number;
    }
	
    public static function array_get($array, $property, $default_value = null) {
        return isset($array[$property]) ? $array[$property] : $default_value;
    }   

    private function getCurrentFarm($sql){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        $stmt->execute(array(':farm_id' => $this->farm_id));
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();         
    } 

}
