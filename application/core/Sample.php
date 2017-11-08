<?php

class Sample {
	
	//private $sample_id;
	public $sample_date;
	public $sample_count;
	public $sample_comment;	
	public $sample_ela_score;
	public $sample_bulb_weight;

	public function __construct($id) {
		
		//$this->sample_id = $id;
        $this->setSampleProperties($id);			
	}
	
	private function setSampleProperties($id){       
       
        $sql = "SELECT sample_date, sample_count, sample_comment, sample_ela_score, sample_bulb_weight
				FROM sample
                WHERE sample_id = :sample_id";
        
        $r = self::getSamplePropertiesByID($sql, $id);	
		
		// Sample properties
        $this->sample_date = $r->sample_date;
		$this->sample_count = $r->sample_count;
        $this->sample_comment = $r->sample_comment;
		$this->sample_ela_score = $r->sample_ela_score;
        $this->sample_bulb_weight = $r->sample_bulb_weight;
		
    }
	private function getSamplePropertiesByID($sql, $id){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
		
		//var_dump($this->sample_id);
        //$stmt->execute(array(':sample_id' => $this->sample_id));
        $stmt->execute(array(':sample_id' => $id));		
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();          
    } 
}
