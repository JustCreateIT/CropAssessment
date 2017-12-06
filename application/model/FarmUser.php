<?php

class FarmUser {
	
	private static $farm_user;
	// Farm & user information
	public $farm_id;
    public $farm_name;	
    public $farm_contact_firstname;
    public $farm_contact_lastname;	
    public $farm_email_address;
    public $farm_phone_number;
	private static $farm;

    public static function getFarmUser()
    {
        if (!self::$farm_user) {
            self::$farm_user = new FarmUser();
        }
        return self::$farm_user;
    }
	
	public static function getFarm($farm_id)
	{
		if (!self::$farm) {
			self::$farm = new Farm();
		}
		return self::$farm;
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

    private function getFarmPropertiesByID($sql){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        $stmt->execute(array(':farm_id' => $this->farm_id));
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();         
    } 
}
