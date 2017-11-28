<?php

/**
 * Class DatabaseCommon
 *
 * Common methods shared between Models
 * Extend this the way you want.
 */
class DatabaseCommon
{
	public static function getFarmAndPaddocks()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT f.farm_id, f.farm_name, p.paddock_id, p.paddock_name		
		FROM 
			farm f, paddock p, farm_users fu
		WHERE
			f.farm_id = fu.farm_id AND 
			p.farm_id = fu.farm_id";	
		
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();			
	}
	
	/* Deprecated 2017/10/10
	public static function getPaddockBedwidth($paddock_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT paddock_bed_width	
				FROM paddock				
				WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_bed_width;
	}
	*/
	public static function getCropBedwidth($crop_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT crop_bed_width	
				FROM crop				
				WHERE crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));

        return $query->fetch()->crop_bed_width;
	}
	
	
	public static function buildJSONcollection($setup = null){
	
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$farms_rs = self::getFarmDetails();		
		$farms_array = array();
		$paddocks_array = array();
		$i = 0;
		foreach ($farms_rs as $farm_record){			
			
			$paddocks_rs = self::getPaddockByFarmID($farm_record->farm_id);
			//$farms['farms'][$farm_record->farm_id]['farm_id'] = $farm_record->farm_id;
			//$farms['farms'][$farm_record->farm_id]['farm_name'] = $farm_record->farm_name;
			//$farms['farms'][$farm_record->farm_id]['paddocks'] = (array)$paddock_rs;

				//$farm['paddocks'] = (array)$paddocks_rs;
			$j = 0;
		/*	
		echo '<pre>';
			print_r($paddocks_rs);
			
		echo '</pre>';
		*/	
			foreach ($paddocks_rs as $paddock_record){	
				
				$crop_rs = self::getCropsByPaddockID($paddock_record->paddock_id);
				$paddock['paddock_id'] = $paddock_record->paddock_id;
				$paddock['paddock_name'] = $paddock_record->paddock_name;
				// we should always have a google maps defined paddock area so use as default
				if ($paddock_record->paddock_google_area > 0){
					$paddock['paddock_area'] = $paddock_record->paddock_google_area;
				} else {
					$paddock['paddock_area'] = $paddock_record->paddock_area;
				}
				
				if (count($crop_rs) > 0  || $setup == true ){
					$paddock['crops'] = (array)$crop_rs;
					$paddocks_array[$j] = $paddock;				
				}
				$j++;		
			}			
			if(count($paddocks_array) >0 || $setup == true ){
				$farm['farm_id'] = $farm_record->farm_id;
				$farm['farm_name'] = $farm_record->farm_name;
				$farm['paddocks'] = (array)$paddocks_array;
				$paddocks_array = null;
				$farm_array['farm'] = $farm;
				$farms_array[$i] = $farm_array;
				$i++;				
			}
		} 

/*
		echo '<pre>';			
			print_r($farms_array);
		echo '</pre>';
*/			
		return json_encode((array)$farms_array);		
	}
	
	public static function getFarmDetails()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT f.farm_id, f.farm_name		
		FROM 
			farm f, farm_users fu
		WHERE
			f.farm_id = fu.farm_id
			AND fu.user_id = :user_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));

        // fetchAll() is the PDO method that gets all result rows
		
		
        return $query->fetchAll();			
	}
	
	
	public static function getFarms()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT f.farm_id, f.farm_name		
		FROM 
			farm f";	
		
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
		
		
        return $query->fetchAll();			
	}
	
		public static function getLinkedFarmUsers()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT farm_users.user_id, farm_users.farm_id, farm.farm_name		
			FROM 
				farm
			INNER JOIN
				farm_users ON farm.farm_id = farm_users.farm_id
			ORDER BY 
				farm_users.user_id";	
		
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
		
		
        return $query->fetchAll();			
	}

	
	/**
	 * Are there samples available for this farm?
	 * Don't care which paddock it is or if they've all been entered at this point?
	 * @return object 
	 */
	public static function getSampleDetails()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT COUNT( s.sample_id ) AS samples_exist
				FROM sample s, farm_users fu
				WHERE s.farm_id = fu.farm_id
				AND fu.user_id = :user_id";
	
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));

		return ((int)$query->fetch()->samples_exist > 0 ? true : false);			
	}
	
	public static function getPaddockByFarmID($farm_id)
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT 
					paddock_id, paddock_name, paddock_area, paddock_google_area		
				FROM 
					paddock 
				WHERE
					farm_id = :farm_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));
		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();			
	}
	
		public static function getCropsByPaddockID($paddock_id)
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT 
					crop_id, crop_plant_date		
				FROM 
					crop 
				WHERE
					paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));
		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();			
	}

		
	public static function getPaddockDetails()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        //$sql = "SELECT p.paddock_id, p.paddock_name, p.paddock_area, p.paddock_google_area
		$sql = "SELECT p.paddock_id, p.paddock_name, p.paddock_google_area as paddock_area		
				FROM 
				paddock p, farm_users fu
				WHERE
				p.farm_id = fu.farm_id
				AND fu.user_id = :user_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));
		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();			
	}
	
	public static function getCropDetails()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM 
				crop c, farm_users fu
				WHERE
				c.farm_id = fu.farm_id
				AND fu.user_id = :user_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));
		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();			
	}

	
	public static function getVarietyNameByID($variety_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT variety_name	FROM variety WHERE variety_id = :variety_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':variety_id' => $variety_id));

        return $query->fetch()->variety_name;
	}
	
	public static function getVarietyIDByName($variety_name){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT variety_id FROM variety WHERE variety_name = :variety_name";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':variety_name' => $variety_name));

        return $query->fetch()->variety_id;
	}
	
	
	public static function getGrowthStageNameByID($growth_stage_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT growth_stage_name FROM growth_stage WHERE growth_stage_id = :growth_stage_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':growth_stage_id' => $growth_stage_id));

        return $query->fetch()->growth_stage_name;		
	}
	
	public static function getGrowthStageIDByName($growth_stage_name){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT growth_stage_id FROM growth_stage WHERE growth_stage_name = :growth_stage_name";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':growth_stage_name' => $growth_stage_name));

        return $query->fetch()->growth_stage_id;	
	}	
	
	public static function getSampleDate($farm_id, $paddock_id, $growth_stage_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT sample_date 
			FROM sample 
			WHERE 
			farm_id = :farm_id AND 
			paddock_id = :paddock_id AND
			growth_stage_id = :growth_stage_id
			ORDER BY sample_id DESC
			LIMIT 1";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, 
				':paddock_id' => $paddock_id, 
				':growth_stage_id' => $growth_stage_id));

		return $query->fetch()->sample_date;		
		
	}
	
	public static function getPaddockPlantDate($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_plant_date 
			FROM paddock 
			WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_plant_date;		
	}
	
	public static function getCropPlantDate($crop_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT crop_plant_date 
			FROM crop 
			WHERE crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));

        return $query->fetch()->crop_plant_date;			
	}
	
		public static function getCropPlantDates($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_id, crop_plant_date 
			FROM crop 
			WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        return $query->fetchAll();		
	}
	
	public static function calculateDaysPostPlanting($paddock_plant_date, $sample_date){
		
		$plant_date = new DateTime($paddock_plant_date);
		$sample = new DateTime($sample_date);
		$interval = $plant_date->diff($sample);
		return $interval->format('%R%a days');
	}
	
	public static function getFarmNameByID($farm_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT farm_name FROM farm WHERE farm_id = :farm_id";		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));

		return $query->fetch()->farm_name;
	}		

	public static function getPaddockNameByID($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT paddock_name FROM paddock WHERE paddock_id = :paddock_id";		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

		return $query->fetch()->paddock_name;
	}	
	
	/* Deprecated 2017/10/10 
	public static function getPaddockZones($farm_id, $paddock_id)
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT zone_id, zone_name 
			FROM zone 
			WHERE 
			farm_id = :farm_id 
			AND paddock_id = :paddock_id";			
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

		$i = 1;
		$paddock_zones = array();
		foreach ($query->fetchAll() as $result) {			
			$paddock_zones[$i] = new stdClass();
            $paddock_zones[$i]->zone_id = $result->zone_id;
			$paddock_zones[$i]->zone_name = !empty($result->zone_name)? $result->zone_name: Statistics::getCharFromNumber($i);
			$i++;
		}
		return $paddock_zones;		
	}	
	
	public static function getPaddockZoneCount($farm_id, $paddock_id)
	{
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_zone_count FROM paddock WHERE farm_id = :farm_id AND paddock_id = :paddock_id";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

        // fetch() is the PDO method that gets a single result row
        return $query->fetch()->paddock_zone_count;			
	}
	*/
	
	public static function getCropZones($crop_id)
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT zone_id, zone_name 
			FROM zone 
			WHERE 
			crop_id = :crop_id";			
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));

		$i = 1;
		$crop_zones = array();
		foreach ($query->fetchAll() as $result) {			
			$crop_zones[$i] = new stdClass();
            $crop_zones[$i]->zone_id = $result->zone_id;
			$crop_zones[$i]->zone_name = !empty($result->zone_name)? $result->zone_name: Statistics::getCharFromNumber($i);
			$i++;
		}
		return $crop_zones;		
	}

	public static function getCropZoneCount($crop_id)
	{
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_zone_count FROM crop WHERE crop_id = :crop_id";
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));

        // fetch() is the PDO method that gets a single result row
        return $query->fetch()->crop_zone_count;			
	}		
	
	/*
	public static function getZoneSamples($farm_id, $paddock_id)
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_zone_sample_count
			FROM paddock 
			WHERE 
			farm_id = :farm_id 
			AND paddock_id = :paddock_id";
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

        $sample_count = $query->fetch();
		$zone_samples = array();
		
		for($i=0;$i<$sample_count->paddock_zone_sample_count;$i++){
			$zone_samples[$i] = new stdClass();
			$zone_samples[$i]->sample_id = ($i+1);						
			//$zone_samples[$i] = ['sample_id' => ($i+1)];
		}
		return $zone_samples;			
	}
	 */
	 
	//public static function getZoneSamples($paddock_id)
	public static function getZoneSamples($crop_id)	
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_zone_sample_count
			FROM crop 
			WHERE 
			crop_id = :crop_id";
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));

        $sample_count = $query->fetch();
		$zone_samples = array();
		
		for($i=0;$i<$sample_count->crop_zone_sample_count;$i++){
			$zone_samples[$i] = new stdClass();
			$zone_samples[$i]->sample_id = ($i+1);						
			//$zone_samples[$i] = ['sample_id' => ($i+1)];
		}
		return $zone_samples;			
	}	 
	
	public static function cropSamplesExist($crop_id, $growth_stage_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT zone_sample_plot_id
				FROM sample
				WHERE crop_id = :crop_id
				AND growth_stage_id = :growth_stage_id";
				
		$query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id, ':growth_stage_id' => $growth_stage_id));	

		if ($query->rowCount() == 0) {
            return false;
        }
        return true;		
	}

	public static function zoneSamplesExist($farm_id, $paddock_id, $crop_id, $zone_id, $zone_sample_plot_id, $growth_stage_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT zone_sample_plot_id
				FROM sample
				WHERE 
				farm_id = :farm_id
				AND paddock_id = :paddock_id
				AND crop_id = :crop_id
				AND zone_id = :zone_id
				AND zone_sample_plot_id = :zone_sample_plot_id
				AND growth_stage_id = :growth_stage_id";
				
		$query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id, ':crop_id' => $crop_id, 
						':zone_id' => $zone_id, ':zone_sample_plot_id' => $zone_sample_plot_id, ':growth_stage_id' => $growth_stage_id));	

		if ($query->rowCount() == 0) {
            return false;
        }
        return true;		
	}

	public static function zoneMeanLeafNumberExist($zone_id, $growth_stage_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT mean_leaf_number
				FROM leaf_number
				WHERE 
				zone_id = :zone_id
				AND growth_stage_id = :growth_stage_id";
				
		$query = $database->prepare($sql);
        $query->execute(array(':zone_id' => $zone_id,':growth_stage_id' => $growth_stage_id));	

		if ($query->rowCount() == 0) {
            return false;
        }
        return true;		
	}	
	
	/* Deprecated 2017/10/10 
	public static function getPaddockZoneSampleCountByPaddockID($farm_id, $paddock_id){

		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_zone_sample_count FROM paddock WHERE farm_id = :farm_id AND paddock_id = :paddock_id";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

        // fetch() is the PDO method that gets a single result row
        return $query->fetch()->paddock_zone_sample_count;			
	}
	*/
	public static function getCropZoneSampleCountByCropID($crop_id){

		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_zone_sample_count FROM crop WHERE crop_id = :crop_id";
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));

        // fetch() is the PDO method that gets a single result row
        return $query->fetch()->crop_zone_sample_count;			
	}	

	public static function getPaddockSamplesByGrowthStage(){

	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT s.paddock_id, p.paddock_name
				FROM sample s, farm_users fu, paddock p
				WHERE s.farm_id = fu.farm_id
				AND s.paddock_id = p.paddock_id
				GROUP BY s.paddock_id";
	
		
        $query = $database->prepare($sql);
        $query->execute();	

		$data = array();
		// fetchAll() is the PDO method that gets all result rows
		foreach ($query->fetchAll() as $paddocks) {
			$sql = "SELECT growth_stage_id FROM sample WHERE paddock_id = :paddock_id GROUP BY growth_stage_id";		
			$query = $database->prepare($sql);
			$query->execute(array(':paddock_id' => $paddocks->paddock_id));
			$data[$paddocks->paddock_id] = new stdClass();
			$data[$paddocks->paddock_id]->paddock_name = $paddocks->paddock_name;			
			$data[$paddocks->paddock_id]->paddock_id = $paddocks->paddock_id;
			$growth_stages = array();
			foreach ($query->fetchAll() as $stages) {
				$growth_stages[] = $stages->growth_stage_id;			
			}
			$data[$paddocks->paddock_id]->growth_stage_id = $growth_stages;
		}			
		
		/*
		echo '<pre>';
				print_r($data);
		echo '</pre>';
		*/
		return $data;		
	}
	
	
		public static function getCropSamplesByGrowthStage(){

	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT p.paddock_name, p.paddock_id, c.crop_id
				FROM paddock p, crop c
				WHERE p.paddock_id = c.paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute();	

		$data = array();
		// fetchAll() is the PDO method that gets all result rows
		foreach ($query->fetchAll() as $crops) {
			$sql = "SELECT growth_stage_id FROM sample 
				WHERE paddock_id = :paddock_id 
				AND crop_id = :crop_id
				GROUP BY growth_stage_id";		
			$query = $database->prepare($sql);
			$query->execute(array(':paddock_id' => $crops->paddock_id, ':crop_id' => $crops->crop_id));

			$growth_stages = array();
			foreach ($query->fetchAll() as $stages) {
				$growth_stages[] = $stages->growth_stage_id;			
			}
			if ( count($growth_stages) > 0 ) {
				$data[$crops->crop_id] = new stdClass();
				$data[$crops->crop_id]->paddock_name = $crops->paddock_name;			
				$data[$crops->crop_id]->paddock_id = $crops->paddock_id;
				$data[$crops->crop_id]->crop_id = $crops->crop_id;
				$data[$crops->crop_id]->growth_stage_id = $growth_stages;			
			}
		}			
		
		/*
		echo '<pre>';
				print_r($data);
		echo '</pre>';
		*/
		return $data;		
	}
	
	
	/* deprecated 2017/10/11
	public static function getTargetPaddockPopulation($farm_id, $paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_target_population FROM paddock WHERE farm_id = :farm_id AND paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_target_population;		
	}	
	*/
	public static function getTargetCropPopulation($crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_target_population FROM crop WHERE crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));

        return $query->fetch()->crop_target_population;		
	}	
	
	/* deprecated 2017/10/11
	public static function getTargetPaddockYield($target_paddock_population, $planting_date){
		
		// returns yield in tonnes/hectare 
		
		// create associative array with detailed information about a specified date
		$d = date_parse($planting_date);
		// minimum optimal bulb weight = 117 or 130 grams (data provided by Pland & Food Research)		
		//$minimum_optimal_bulb_weight = $d['month'] < 8 ? 130 : 117; // grams
		$minimum_optimal_bulb_weight = $d['month'] < 8 ? Config::get('OPTIMAL_BULB_WEIGHT_GRAMS_PRE_AUGUST') : Config::get('OPTIMAL_BULB_WEIGHT_GRAMS_POST_JULY'); // grams
		$paddock_yield = ($target_paddock_population*$minimum_optimal_bulb_weight)/1000000;

        return $paddock_yield;		
	}
	*/
		public static function getTargetYield($target_population, $planting_date){
		
		/* returns yield in tonnes/hectare */
		
		// create associative array with detailed information about a specified date
		$d = date_parse($planting_date);
		// minimum optimal bulb weight = 117 or 130 grams (data provided by Pland & Food Research)		
		//$minimum_optimal_bulb_weight = $d['month'] < 8 ? 130 : 117; // grams
		$minimum_optimal_bulb_weight = $d['month'] < 8 ? Config::get('OPTIMAL_BULB_WEIGHT_GRAMS_PRE_AUGUST') : Config::get('OPTIMAL_BULB_WEIGHT_GRAMS_POST_JULY'); // grams
		$target_yield = ($target_population*$minimum_optimal_bulb_weight)/1000000;

        return $target_yield;		
	}
	
	
	/*
	public static function getPaddockArea($farm_id, $paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_area FROM paddock WHERE farm_id = :farm_id AND paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_area;
	}
	*/	
	/*
	public static function getTargetPaddockTonnesHectare($target_paddock_yield, $paddock_area){
		
		return ($target_paddock_yield/$paddock_area);			
	}
	 * */
	
	public static function getTargetTonnesHectare($target_yield, $area){
		
		return ($target_yield/$area);			
	}
	
	
	public static function getAccountTypeByUserID($user_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT a.account_name 
				FROM account_type a
				INNER JOIN users u
				ON a.account_type=u.user_account_type
				WHERE u.user_id = :user_id";
		
        $query = $database->prepare($sql);
        $query->execute(array( ':user_id' => $user_id ));

        return $query->fetch()->account_name;	
	}

    public static function getAccountTypes(){
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT account_type, account_name FROM account_type";
        $query = $database->prepare($sql);
        $query->execute();

        $account_types = array();

        foreach ($query->fetchAll() as $account_type) {
            $account_types[$account_type->account_type] = new stdClass();
            $account_types[$account_type->account_type]->account_type = $account_type->account_type;
            $account_types[$account_type->account_type]->account_name = $account_type->account_name;
        }

        return $account_types;
    }		
	
}
