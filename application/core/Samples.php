<?php

class Samples {
	
	//private $growth_stage_id;
	//public $growth_stage_name;
	public $samples = array();

	public function __construct($growth_stage_id, $crop_id) {

		//$this->growth_stage_id = $growth_stage_id;
		$this->setSampleProperties($growth_stage_id, $crop_id);
	}
	
	private function setSampleProperties($growth_stage_id, $crop_id){       

   /*    
        $sql = "SELECT s.sample_id, g.growth_stage_name 
				FROM sample s, growth_stage g
                WHERE g.growth_stage_id =:growth_stage_id";
   */    
        $sql = "SELECT sample_id 
				FROM sample 
				WHERE growth_stage_id =:growth_stage_id
				AND crop_id =:crop_id";				
	
        
        $result = self::getSamplesByGrowthStageID($sql, $growth_stage_id, $crop_id);
		
		foreach ($result as $sample) {
			//$this->growth_stage_name = $sample->growth_stage_name;
			$this->samples[] = new Sample($sample->sample_id);
		}
    }
	
	private function getSamplesByGrowthStageID($sql, $growth_stage_id, $crop_id){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        //$stmt->execute(array(':growth_stage_id' => $this->growth_stage_id));
        $stmt->execute(array(':growth_stage_id' => $growth_stage_id, ':crop_id' => $crop_id));		
        
        // fetchAll() is the PDO method that gets multiple results
        return $stmt->fetchAll();         
    } 	
}
