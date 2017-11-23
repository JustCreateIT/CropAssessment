<?php

/**
 * SetupModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class SetupModel
{
    
    
	public static function getFarmsByUserID()
	{
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT farm_id FROM farm_users WHERE user_id = :user_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));
        
		$all_users_farms = array();
		// fetchAll() is the PDO method that gets all result rows
		foreach ($query->fetchAll() as $farm_ids) {
			$sql = "SELECT farm_name FROM farm WHERE farm_id = :farm_id";		
			$query = $database->prepare($sql);
			$query->execute(array(':farm_id' => $farm_ids->farm_id));
			foreach ($query->fetchAll() as $farms) {
				$all_users_farms[$farm_ids->farm_id] = new stdClass();
				$all_users_farms[$farm_ids->farm_id]->farm_id = $farm_ids->farm_id;
				$all_users_farms[$farm_ids->farm_id]->farm_name = $farms->farm_name;
			}
		}			
        return $all_users_farms;			
	}


	/**
     * Get a single farms properties
     * @param int $farm_id id of the specific note
     * @return object a single object (the result)
     */
    public static function getFarmByFarmID($farm_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM farm WHERE user_id = :user_id AND farm_id = :farm_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id'), ':farm_id' => $farm_id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    /**
     * Set a note (create a new one)
     * @param string $note_text note text that will be created
     * @return bool feedback (was the note created properly ?)
     */
    public static function createFarm(
		$farm_name, $farm_contact_firstname, $farm_contact_lastname, 
		$farm_email_address, $farm_phone_number
		)
    {
	
		if (!$farm_name || strlen($farm_name) == 0) {
            Session::add('feedback_negative', Text::get('FEEDBACK_FARM_CREATION_FAILED'));
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO farm 
		(farm_name, farm_contact_firstname, farm_contact_lastname, farm_email_address, farm_phone_number) 
		VALUES 
		(:farm_name, :farm_contact_firstname, :farm_contact_lastname, :farm_email_address, :farm_phone_number)";
        $query = $database->prepare($sql);
        $query->execute(array( 
			':farm_name' => $farm_name, 
			':farm_contact_firstname' => $farm_contact_firstname,
			':farm_contact_lastname' => $farm_contact_lastname,
			':farm_email_address' => $farm_email_address,
			':farm_phone_number' => $farm_phone_number			
			));
        if ($query->rowCount() == 1) {
			$farm_id = $database->lastInsertId();			
			$sql = "INSERT INTO farm_users 
				(farm_id, user_id) 
				VALUES 
				(:farm_id, :user_id)";
			$query = $database->prepare($sql);
			$query->execute(array( 
				':farm_id' => $farm_id, 
				':user_id' => Session::get('user_id')					
			));			
			if ($query->rowCount() == 1) {
				Session::set('farm_id', $farm_id);
				Session::add('feedback_positive', Text::get('FEEDBACK_FARM_CREATION_SUCCESSFUL'));
				return true;
			}
        }
        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_FARM_CREATION_FAILED'));
        return false;
    }


    /**
     * Update an existing farm details
     * @param int $note_id id of the specific note
     * @param string $note_text new text of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public static function updateFarm($farm_id)
    {
        if (!$farm_id || !$note_text) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE farm SET note_text = :note_text WHERE note_id = :note_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':note_id' => $note_id, ':note_text' => $note_text, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_FARM_EDITING_FAILED'));
        return false;
    }

    /**
     * Delete a specific farm
     * @param int $farm_id id of the farm
     * @return bool feedback (was the farm deleted properly ?)
     */
    public static function deleteFarm($farm_id)
    {
        if (!$farm_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM farm WHERE farm_id = :farm_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_FARM_DELETION_FAILED'));
        return false;
    }
	
	
/**
     * Insert a new row into the database (paddock table)
     * @param string $paddock_name - name of paddock as defined by user
     * @param string $paddock_address - location of paddock
     * @param float $paddock_area - total paddock area in hectares
     * @param string $paddock_longitude - GPS position
     * @param string $paddock_latitude - GPS position
     * @param integer $paddock_zone_count - number of measurement zones in the paddock
     * @param integer $paddock_zone_sample_count - number of sample plots per zone
	 * @param string $paddock_google_place_id - Google Maps unique place_id for reverse geocoding (coords -> address)
     * @return bool feedback (was the paddock created properly ?)
     */	
	public static function InsertIntoPaddockSession($farm_id, $paddock_name, $paddock_address, $paddock_area, 
		$paddock_longitude, $paddock_latitude, $paddock_zone_count, $paddock_zone_sample_count, $paddock_google_place_id)
	{	
		
		Session::set('farm_id', null);
		Session::set('farm_id', $farm_id);
		$paddock = array();
		$paddock[$farm_id] = new stdClass();
		$paddock[$farm_id]->farm_id = $farm_id;
		$paddock[$farm_id]->paddock_name = $paddock_name;
		$paddock[$farm_id]->paddock_address = $paddock_address;
		$paddock[$farm_id]->paddock_area = $paddock_area;
		$paddock[$farm_id]->paddock_longitude = $paddock_longitude;
		$paddock[$farm_id]->paddock_latitude = $paddock_latitude;
		$paddock[$farm_id]->paddock_zone_count = $paddock_zone_count;
		$paddock[$farm_id]->paddock_zone_sample_count = $paddock_zone_sample_count;	
		$paddock[$farm_id]->paddock_google_place_id = $paddock_google_place_id;		
		// Clear it out before populating so don't repeat data
		Session::set('paddock_form_input', null);			
		Session::set('paddock_form_input', $paddock);		
		//Session::add('feedback_positive', print_r($paddock, true));		
		return true;	
	}

	public static function InsertIntoPaddockMapSession($paddock_google_latlong_paths, $paddock_google_area)
	{	
		
		$farm_id = Session::get('farm_id');
		$paddock_polygon = array();
		$paddock_polygon[$farm_id] = new stdClass();
		$paddock_polygon[$farm_id]->farm_id = $farm_id;
		$paddock_polygon[$farm_id]->paddock_google_latlong_paths = $paddock_google_latlong_paths;
		$paddock_polygon[$farm_id]->paddock_google_area = $paddock_google_area;
		// Clear it out before populating so don't repeat data
		Session::set('paddock_map_form_input', null);	
		//Session::add('paddock', $paddock);
		//Session::add('feedback_positive', print_r($paddock_polygon, true));
		Session::set('paddock_map_form_input', $paddock_polygon);		
		return true;	
	}	


	public static function InsertIntoCropSession($farm_id, $paddock_id, $crop_plant_date, 
			$crop_bed_width, $crop_bed_rows, $crop_plant_spacing, $crop_target_population, 
			$crop_zone_count, $crop_zone_sample_count, $crop_sample_plot_width, $crop_variety_id)
	{	
		/*
		echo '<pre>';
		 	print_r($crop_zone_count);
		echo '</pre>';
		*/
		
		// Clear it out before populating so don't repeat data
		Session::set('crop_form_input', null);	
		
		$crop = array();
		$crop[$farm_id] = new stdClass();
		$crop[$farm_id]->farm_id = $farm_id;
		$crop[$farm_id]->crop_plant_date = $crop_plant_date;		
		$crop[$farm_id]->crop_bed_width = $crop_bed_width;
		$crop[$farm_id]->crop_bed_rows = $crop_bed_rows;
		$crop[$farm_id]->crop_plant_spacing = $crop_plant_spacing;
		$crop[$farm_id]->crop_target_population = $crop_target_population;
		$crop[$farm_id]->crop_zone_count = $crop_zone_count;
		$crop[$farm_id]->crop_zone_sample_count = $crop_zone_sample_count;
		$crop[$farm_id]->crop_sample_plot_width = $crop_sample_plot_width;		
		$crop[$farm_id]->paddock_id = $paddock_id;
		$crop[$farm_id]->crop_variety_id = $crop_variety_id;	

		//Session::add('paddock', $paddock);
		//Session::add('feedback_positive', print_r($crop, true));
		Session::set('crop_form_input', $crop);		
		return true;	
	}	
	
	public static function IteratePaddockZones($crop_zones)
	{			
		foreach ($crop_zones as $id => $zone){
			if (!self::InsertIntoZoneTable($zone->zone_name, $zone->zone_paddock_percentage)){
				return false;
			}
		}
		return true;
	}
	
	public static function IterateCropZones($crop_zones)
	{					
		foreach ($crop_zones as $id => $zone){				
			if (!self::InsertIntoZoneTable($zone->zone_name, $zone->zone_paddock_percentage)){
				return false;
			}
		}
		return true;
	}
	
	public static function InsertIntoZoneSession($zones)
	{	
		// Clear it out before populating so don't repeat data
		Session::set('zone_form_input', null);	
		//Session::add('paddock', $paddock);
		Session::set('zone_form_input', $zones);		
		return true;	
	}	
	
	
    /**
     * Insert a new row into the database (paddock table)
     * @param string $paddock_name - name of paddock as defined by user
     * @param string $paddock_address - location of paddock
     * @param float $paddock_area - total paddock area in hectares
     * @param string $paddock_longitude - GPS position
     * @param string $paddock_latitude - GPS position
     * @param integer $paddock_zone_count - number of measurement zones in the paddock
     * @param integer $paddock_zone_sample_count - number of sample plots per zone
	 * @param string $paddock_google_place_id - Unique Google place id for reverse geocoding (coords->address)
     * @return bool feedback (was the paddock created properly ?)
     */	
	public static function PaddockInsertTransaction()
	{
		
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "INSERT INTO paddock 
				(paddock_name, paddock_address, paddock_area, 
				paddock_longitude, paddock_latitude, 
				paddock_google_place_id, paddock_google_latlong_paths, paddock_google_area,
				farm_id) 
				VALUES 
				(:paddock_name, :paddock_address, :paddock_area, 
				:paddock_longitude, :paddock_latitude, 
				:paddock_google_place_id, :paddock_google_latlong_paths, :paddock_google_area,
				:farm_id)";

		// Retrieve the paddock form post session variables
		$farm_id = self::GetPaddockValueFromSession('farm_id');
		$paddock_name = self::GetPaddockValueFromSession('paddock_name');
		$paddock_address = self::GetPaddockValueFromSession('paddock_address');
		$paddock_area = self::GetPaddockValueFromSession('paddock_area');
		$paddock_longitude = self::GetPaddockValueFromSession('paddock_longitude');
		$paddock_latitude = self::GetPaddockValueFromSession('paddock_latitude');
		$paddock_google_place_id = self::GetPaddockValueFromSession('paddock_google_place_id');	
		// Retrive the paddock polygon information form post session variables
		$paddock_google_latlong_paths = unserialize(self::GetPaddockMapValueFromSession('paddock_google_latlong_paths'));
		$paddock_google_area = self::GetPaddockMapValueFromSession('paddock_google_area');

	
		//Session::add('feedback_positive','In paddock insert: Farm ID='.$farm_id);
		//Session::add('feedback_positive','In paddock insert: Farm ID crop='.$farm_id_crop);		
	
		try {				
			$query = $database->prepare($sql);
			$query->execute(array(
				':paddock_name' => $paddock_name,
				':paddock_address' => $paddock_address,
				':paddock_area' => $paddock_area,
				':paddock_longitude' => $paddock_longitude,
				':paddock_latitude' => $paddock_latitude,
				':paddock_google_place_id' => $paddock_google_place_id,
				':paddock_google_latlong_paths' => json_encode($paddock_google_latlong_paths),
				':paddock_google_area' => $paddock_google_area,
				':farm_id' => $farm_id
				));

		} catch (PDOException $e) {
					Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
					Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}	
		
		if ($query->rowCount() == 1) {
			$paddock_id = $database->lastInsertId();
			Session::set('paddock_id', null);		
			Session::set('paddock_id', $paddock_id);
			Session::set('paddock_form_input', null);
			Session::set('paddock_map_form_input', null);
			
			Session::add('feedback_positive', Text::get('FEEDBACK_PADDOCK_CREATION_SUCCESSFUL'));
			return true;		
		} else {
			// Rollback transaction
			self::DeleteFromPaddockByFarmID($farm_id);			
			Session::add('feedback_negative', Text::get('FEEDBACK_PADDOCK_CREATION_FAILED').' [PADDOCK ROLLBACK]');
			//Session::add('feedback_negative', $query->rowCount());
			return false;		
		}			
	}
	
	
	/**
     * Insert a new row into the database (crop table)
     * @param date $crop_plant_date - date of planting as defined by user
     * @param decimal (4,2) $crop_bed_width - width of each bed (metres)
     * @param tinyint(3) $crop_bed_rows - number of plant rows within a bed
     * @param smallint(5) $crop_plant_spacing - how far plants are along each row of bed (millimetres)
	 * @param int(10) $crop_target_population - optimum population for crop based on paddock area and defined spacing
     * @param tinyint(3) $crop_zone_count - how many management zones are defined for the crop
     * @param tinyint(3) $crop_zone_sample_count - number of measurement points within each zone
	 * @param decimal(5,2) $crop_sample_plot_width - width of the plot sample along the bed/row
     * @param mediumint(9) $paddock_id - paddock identifier
     * @param mediumint(8) $farm_id - farm identifier
     * @param tinyint(3) $variety_id - variety identifier
     * @return bool feedback (was the crop created properly ?)
     */	
	public static function CropInsertTransaction()
	{
		
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "INSERT INTO crop 
				(crop_plant_date, crop_bed_width, crop_bed_rows, 
				crop_plant_spacing, crop_target_population, 
				crop_zone_count, crop_zone_sample_count, crop_sample_plot_width,
				paddock_id, farm_id, variety_id) 
				VALUES 
				(:crop_plant_date, :crop_bed_width, :crop_bed_rows, 
				:crop_plant_spacing, :crop_target_population, 
				:crop_zone_count, :crop_zone_sample_count, :crop_sample_plot_width,
				:paddock_id, :farm_id, :variety_id)";

		// Retrieve the crop form post session variables
		$farm_id = self::GetCropValueFromSession('farm_id');
		$paddock_id = self::GetCropValueFromSession('paddock_id');
		$crop_plant_date = self::GetCropValueFromSession('crop_plant_date');
		$crop_bed_width = self::GetCropValueFromSession('crop_bed_width');
		$crop_bed_rows = self::GetCropValueFromSession('crop_bed_rows');
		$crop_plant_spacing = self::GetCropValueFromSession('crop_plant_spacing');
		$crop_target_population = self::GetCropValueFromSession('crop_target_population');
		$crop_zone_count = self::GetCropValueFromSession('crop_zone_count');
		$crop_zone_sample_count = self::GetCropValueFromSession('crop_zone_sample_count');
		$crop_sample_plot_width = self::GetCropValueFromSession('crop_sample_plot_width');
		$crop_variety_id = self::GetCropValueFromSession('crop_variety_id');
		
		try {				
			$query = $database->prepare($sql);
			$query->execute(array(
				':crop_plant_date' => $crop_plant_date,
				':crop_bed_width' => $crop_bed_width,
				':crop_bed_rows' => $crop_bed_rows,
				':crop_plant_spacing' => $crop_plant_spacing,
				':crop_target_population' => $crop_target_population,
				':crop_zone_count' => $crop_zone_count,
				':crop_zone_sample_count' => $crop_zone_sample_count,
				':crop_sample_plot_width' => $crop_sample_plot_width,
				':paddock_id' => $paddock_id,
				':farm_id' => $farm_id,				
				':variety_id' => $crop_variety_id
				));

		} catch (PDOException $e) {
					Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
					Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}	
		
		if ($query->rowCount() == 1) {
			$crop_id = $database->lastInsertId();
			Session::set('crop_id', null);		
			Session::set('crop_id', $crop_id);		
			// Retrieve the zone form session variables
			$crop_zones = Session::get('zone_form_input');
			
			//Session::add('feedback_negative', var_dump($crop_zones));
			
			// Iterate through each zone (name and percentage) and add to zone table
			if(self::IterateCropZones($crop_zones)){
				Session::add('feedback_positive', Text::get('FEEDBACK_CROP_CREATION_SUCCESSFUL'));
				// Clear input form session variables
				Session::set('crop_form_input', null);
				Session::set('zone_form_input', null);
				return true;
			} else {
				// Rollback transaction
				self::DeleteFromCropByPaddockID($paddock_id);
				self::DeleteFromZoneByCropID($crop_id);
				Session::add('feedback_negative', Text::get('FEEDBACK_CROP_CREATION_FAILED').' [CROP->ZONE ROLLBACK]');
				return false;
			}								
		} else {
			// Rollback transaction
			self::DeleteFromCropByPaddockID($paddock_id);			
			Session::add('feedback_negative', Text::get('FEEDBACK_CROP_CREATION_FAILED').' [CROP ROLLBACK]');
			//Session::add('feedback_negative', $query->rowCount());
			return false;		
		}			
	}
	
	/* rollback method */
	public static function DeleteFromPaddockByFarmID($farm_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("DELETE FROM paddock WHERE farm_id = :farm_id");
        $query->execute(array(':farm_id' => $farm_id));			
	}
	
	/* rollback method */
	public static function DeleteFromCropByPaddockID($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("DELETE FROM crop WHERE paddock_id = :paddock_id");
        $query->execute(array(':paddock_id' => $paddock_id));			
	}
	
	/* rollback method */	
	public static function DeleteFromZoneByCropID($crop_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("DELETE FROM zone WHERE crop_id = :crop_id");
        $query->execute(array(':crop_id' => $crop_id));				
	}	

	
	/* rollback method */	
	public static function DeleteFromZoneByFarmIDAndPaddockID($farm_id, $paddock_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("DELETE FROM zone WHERE farm_id = :farm_id AND paddock_id = :paddock_id");
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));				
	}	

	/* Admin method */	
	public static function DeleteFromPaddockByFarmIDAndPaddockID($farm_id, $paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("DELETE FROM paddock WHERE farm_id = :farm_id AND paddock_id = :paddock_id");
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));			
	}	
	
	public static function GetPaddockValueFromSession($value)
	{	
		$array_paddock = Session::get('paddock_form_input');
		if (isset($array_paddock)) {
			foreach ($array_paddock as $paddock_object => $paddock) {
				$farm_id = $paddock->farm_id;
				$paddock_name = $paddock->paddock_name;
				$paddock_address = $paddock->paddock_address;
				$paddock_area = $paddock->paddock_area;
				$paddock_longitude = $paddock->paddock_longitude;
				$paddock_latitude = $paddock->paddock_latitude;
				$paddock_zone_count = $paddock->paddock_zone_count;
				$paddock_zone_sample_count = $paddock->paddock_zone_sample_count;
				$paddock_google_place_id = $paddock->paddock_google_place_id;
			}
			switch ($value) {
				case 'farm_id':
					return $farm_id;
					break;			
				case 'paddock_name':
					return $paddock_name;
					break;
				case 'paddock_address':
					return $paddock_address;
					break;
				case 'paddock_area':
					return $paddock_area;
					break;
				case 'paddock_longitude':
					return $paddock_longitude;
					break;
				case 'paddock_latitude':
					return $paddock_latitude;
					break;
				case 'paddock_zone_count':
					return $paddock_zone_count;
					break;
				case 'paddock_zone_sample_count':
					return $paddock_zone_sample_count;
					break;
				case 'paddock_google_place_id':
					return $paddock_google_place_id;
					break;		
				default:
				// To-do (trap error)		
			}
		}
		return null;
	}
	
	public static function GetPaddockMapValueFromSession($value)
	{	
		$array_paddock_map = Session::get('paddock_map_form_input');
		if (isset($array_paddock_map)) {
			foreach ($array_paddock_map as $paddock_map_object => $paddock_map) {
				$farm_id = $paddock_map->farm_id;
				$paddock_google_latlong_paths = $paddock_map->paddock_google_latlong_paths;
				$paddock_google_area = $paddock_map->paddock_google_area;
			}
			switch ($value) {
				case 'farm_id':
					return $farm_id;
					break;			
				case 'paddock_google_latlong_paths':
					return $paddock_google_latlong_paths;
					break;
				case 'paddock_google_area':
					return $paddock_google_area;
					break;		
				default:
				// To-do (trap error)		
			}
		}
		return null;
	}	

	public static function GetCropValueFromSession($value)
	{	
		$array_crop = Session::get('crop_form_input');
		if (isset($array_crop)) {
			foreach ($array_crop as $crop_object => $crop) {
				$farm_id_crop = $crop->farm_id;
				$crop_plant_date = $crop->crop_plant_date;
				$crop_bed_width = $crop->crop_bed_width;
				$crop_bed_rows = $crop->crop_bed_rows;
				$crop_plant_spacing = $crop->crop_plant_spacing;
				$crop_target_population = $crop->crop_target_population;
				$crop_zone_count = $crop->crop_zone_count;
				$crop_zone_sample_count = $crop->crop_zone_sample_count;
				$crop_sample_plot_width = $crop->crop_sample_plot_width;
				$crop_paddock_id = $crop->paddock_id;
				$crop_variety_id = $crop->crop_variety_id;
			}
			switch ($value) {
				case 'farm_id':
					return $farm_id_crop;
					break;			
				case 'crop_plant_date':
					return $crop_plant_date;
					break;
				case 'crop_bed_width':
					return $crop_bed_width;
					break;
				case 'crop_bed_rows':
					return $crop_bed_rows;
					break;
				case 'crop_plant_spacing':
					return $crop_plant_spacing;
					break;
				case 'crop_target_population':
					return $crop_target_population;
					break;
				case 'crop_zone_count':
					return $crop_zone_count;
					break;
				case 'crop_zone_sample_count':
					return $crop_zone_sample_count;
					break;
				case 'crop_sample_plot_width':
					return $crop_sample_plot_width;
					break;						
				case 'paddock_id':
					return $crop_paddock_id;
					break;					
				case 'crop_variety_id':
					return $crop_variety_id;
					break;
				default:
				// To-do (trap error)					
			}
		}
		return null;
	}

    /**
     * Update an existing row in the database (paddock table) with the crop specific information
     * @param date $paddock_plant_date - date of planting
     * @param float $paddock_bed_width - width of each bed in metres
     * @param integer $paddock_bed_rows - number of rows per bed
     * @param integer $paddock_plant_spacing - inter-row plant spacing in millimetres
     * @param integer $paddock_target_population - expected target population 
     * @param integer $paddock_variety_id - foreign key id from variety table
     * @return bool feedback (was the paddock created properly ?)
     */
	 /*
	public static function UpdateIntoPaddockTable($paddock_plant_date, $paddock_bed_width, 
			$paddock_bed_rows, $paddock_plant_spacing, $paddock_target_population, $paddock_variety_id)
	{
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "UPDATE paddock
				SET
				(paddock_name, paddock_address, paddock_area, paddock_longitude, 
				paddock_latitude, paddock_zone_count, paddock_zone_sample_count, paddock_google_place_id
				paddock_plant_date, paddock_bed_width, paddock_bed_rows, paddock_plant_spacing,
				paddock_target_population, farm_id, variety_id) 
				VALUES 
				(:paddock_name, :paddock_address, :paddock_area, :paddock_longitude, 
				:paddock_latitude, :paddock_zone_count, :paddock_zone_sample_count, :paddock_google_place_id
				:paddock_plant_date, :paddock_bed_width, :paddock_bed_rows, 
				:paddock_plant_spacing, :paddock_target_population, :farm_id, :variety_id)
				WHERE
				farm_id = :farm_id AND paddock_id = :paddock_id
				LIMIT 1";
		/*
		$sql = "UPDATE paddock
				SET
				paddock_plant_date = :paddock_plant_date,
				paddock_bed_width = :paddock_bed_width,
				paddock_bed_rows = :paddock_bed_rows,
				paddock_plant_spacing = :paddock_plant_spacing,
				paddock_target_population = :paddock_target_population, 
				variety_id = :variety_id
				WHERE
				farm_id = :farm_id AND paddock_id = :paddock_id
				LIMIT 1";
		*/
		/*
		try{
			$query = $database->prepare($sql);
			$query->execute(array(
				':paddock_plant_date' => $paddock_plant_date,
				':paddock_bed_width' => $paddock_bed_width,
				':paddock_bed_rows' => $paddock_bed_rows,
				':paddock_plant_spacing' => $paddock_plant_spacing,
				':paddock_target_population' => $paddock_target_population,
				':variety_id' => $paddock_variety_id,
				':farm_id' => Session::get('farm_id'),
				':paddock_id' => Session::get('paddock_id')
				));	
		} catch (PDOException $e) {
					Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
					Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}	
		if ($query->rowCount() == 1) {			
			return true;						
		} else {
			// default return
			//self::rollbackPaddockInsert();
			Session::add('feedback_negative', Text::get('FEEDBACK_PADDOCK_CREATION_FAILED'));
			return false;		
		}			
	}
	*/
	
	public static function InsertIntoZoneTable($zone_name, $zone_percentage)
	{
		
		$farm_id = self::GetCropValueFromSession('farm_id');
		$paddock_id = self::GetCropValueFromSession('paddock_id');
		$crop_id = Session::get('crop_id');
		
		if (isset($farm_id) && isset($paddock_id) && isset($crop_id)){
			
			$database = DatabaseFactory::getFactory()->getConnection();
			
			$sql = "INSERT INTO zone 
					(zone_name, zone_paddock_percentage, crop_id, paddock_id, farm_id) 
					VALUES 
					(:zone_name, :zone_paddock_percentage, :crop_id, :paddock_id, :farm_id)";
			$query = $database->prepare($sql);
			$query->execute(array(
				':zone_name' => $zone_name,			
				':zone_paddock_percentage' => $zone_percentage,
				':crop_id' => $crop_id,
				':paddock_id' => $paddock_id,				
				':farm_id' => $farm_id
				));
			if ($query->rowCount() == 1) {								
				return true;
			} 	
		}
		// default return
		return false;		
	}
	
		public static function getPaddocksByFarmID($farm_id){
			
		//$farm_id = Session::get('farm_id');
		Session::add('feedback_negative','In getPaddocksByFarmID: Farm ID='.$farm_id);
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_id, paddock_name FROM paddock WHERE farm_id = :farm_id";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();	
	}

	
	public static function getCropVarieties(){
		
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT variety_id, variety_name FROM variety";
        $query = $database->prepare($sql);
        $query->execute();

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();	
	}
}
