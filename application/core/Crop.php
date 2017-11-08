<?php

class Crop extends Paddock {
	
	public $crop_id;
	public $crop_zone_count;	
    public $crop_zone_sample_count;
	public $crop_plant_date;
	public $crop_bed_width;
	public $crop_bed_rows;
	public $crop_plant_spacing;
	public $crop_area;
	public $hectare_target_population;
	public $crop_target_population;	
	public $variety_id;
	public $variety_name;
	
	public function __construct($farm_id, $paddock_id, $crop_id) {
		
		// initialise parent class
		parent::__construct($farm_id, $paddock_id);
        
		// set the paddock_id and initialise paddock properties
        $this->crop_id = $crop_id;
        $this->setCropProperties();		
		
	}
	
	private function setCropProperties(){       
       
        $sql = "SELECT * FROM crop
                WHERE crop_id =:crop_id";
        
        $r = self::getCropPropertiesByID($sql);	
 
        // Crop properties

        $this->crop_zone_count = $r->crop_zone_count;
        $this->crop_zone_sample_count = $r->crop_zone_sample_count;
        $this->crop_plant_date = $r->crop_plant_date;
        $this->crop_bed_width = $r->crop_bed_width;
        $this->crop_bed_rows = $r->crop_bed_rows;
        $this->crop_plant_spacing = $r->crop_plant_spacing;		
        $this->crop_target_population = $r->crop_target_population;
		$this->crop_area = self::getPaddockArea();
		
        $this->hectare_target_population = round($this->crop_target_population/$this->crop_area);		
        $this->variety_id = $r->variety_id;
		$this->variety_name = ucwords((string)self::getVarietyNameByID($this->variety_id));
    }
	
	private function getCropPropertiesByID($sql){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        $stmt->execute(array(':crop_id' => $this->crop_id));
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();         
    } 
	
	private function getVarietyNameByID($variety_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT variety_name	FROM variety WHERE variety_id = :variety_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':variety_id' => $variety_id));

        return $query->fetch()->variety_name;
	}

		
	public function getPaddockArea(){
		return parent::getPaddockArea();
	}
}