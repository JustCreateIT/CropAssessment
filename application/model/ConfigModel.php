<?php

/**
 * ConfigModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) model
 * Can edit (read/update) existing farms and paddocks and crops
 * Can delete (read/delete) existing crops (if no samples exist) and paddocks (if no crops exist) and farms (if no paddocks exist)
 */
class ConfigModel
{

	/**
     * Get all paddocks related to a particular farm
     * @return array an array with several objects (the results)
     */
    public static function getPaddocksByFarmID($farm_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_id, paddock_name, paddock_address, paddock_area, paddock_google_area
		FROM paddock WHERE farm_id = :farm_id";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }	
	
    public static function getCropsByPaddockID($paddock_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_id FROM crop WHERE paddock_id = :paddock_id";
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }	
	
	public static function getVarietyData(){
	    $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT variety_id, variety_name FROM variety";
        $query = $database->prepare($sql);
        $query->execute();		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();	
	}
	
    /**
     * Get a single paddock
     * @param int $paddock_id id of the specific paddock
     * @return object a single object (the result row)
     */
    public static function getPaddock($paddock_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_name, paddock_address, paddock_area 
			FROM paddock 
			WHERE paddock_id = :paddock_id";
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }	

    /**
     * Get a single farm
     * @param int $farm_id id of the specific farm
     * @return object a single object (the result row)
     */
    public static function getFarm($farm_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT farm_id, farm_name, farm_contact_firstname, farm_contact_lastname, farm_email_address, farm_phone_number 
			FROM farm 
			WHERE farm_id = :farm_id";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }
	
    /**
     * Get all farms related to a single user 
     * @param int $user_id id of the specific user
     * @return object an array of object (the result rows)
     */
    public static function getFarmsByUserID($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT f.farm_id, f.farm_name, f.farm_contact_firstname, f.farm_contact_lastname, f.farm_email_address, f.farm_phone_number 
				FROM farm f
				INNER JOIN farm_users u
				ON f.farm_id=u.farm_id
				WHERE u.user_id = :user_id";

        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));

        // fetchAll() is the PDO method that gets all results
        return $query->fetchAll();
    }	

    /**
     * Update an existing Farm
     * @param int $farm_id id of the specific farm
     * @param string $farm_name, $farm_contact_firstname, $farm_contact_lastname 
	 * $farm_email_address, $farm_phone_number new details of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public static function updateFarm($farm_name, $farm_contact_firstname, 
		$farm_contact_lastname, $farm_email_address, $farm_phone_number, $farm_id)
    {
        if ( !$farm_id ) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE farm SET 
			farm_name = :farm_name,
			farm_contact_firstname = :farm_contact_firstname,
			farm_contact_lastname = :farm_contact_lastname,
			farm_email_address = :farm_email_address,
			farm_phone_number = :farm_phone_number 
			WHERE farm_id = :farm_id";
			
        $query = $database->prepare($sql);
		
        $query->execute(array(':farm_name' => $farm_name, 
			':farm_contact_firstname' => $farm_contact_firstname, 
			':farm_contact_lastname' => $farm_contact_lastname, 
			':farm_email_address' => $farm_email_address,
			':farm_phone_number' => $farm_phone_number,
			':farm_id' => $farm_id));

        if ($query->rowCount() == 1) {
			
			Session::add('feedback_positive', Text::get('FEEDBACK_FARM_EDITING_SUCCESSFUL'));
            return true;
        }
        return false;
    }
	
    /**
     * Update an existing Paddock
     * @param int $paddock_id id of the specific paddock
     * @param string $farm_name, $farm_contact_firstname, $farm_contact_lastname 
	 * $farm_email_address, $farm_phone_number new details of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public static function updatePaddockByID($paddock_id, $paddock_name, $paddock_address, $paddock_area)
    {
        if ( !$paddock_id ) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE paddock SET 
			paddock_name = :paddock_name,
			paddock_address = :paddock_address,
			paddock_area = :paddock_area 
			WHERE paddock_id = :paddock_id";
			
        $query = $database->prepare($sql);
		
        $query->execute(array(':paddock_name' => $paddock_name, ':paddock_address' => $paddock_address, 
			':paddock_area' => $paddock_area, ':paddock_id' => $paddock_id));

        if ($query->rowCount() == 1) {
			Session::add('feedback_positive', Text::get('FEEDBACK_PADDOCK_EDITING_SUCCESSFUL'));
            return true;
        }		
        return false;
    }

    /**
     * Update an existing Crop
     * @param int $crop_id id of the specific crop
     * @param string $farm_name, $farm_contact_firstname, $farm_contact_lastname 
	 * $farm_email_address, $farm_phone_number new details of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public static function updateCropByID($crop_id, $crop_zone_count, $crop_zone_sample_count, 
				$crop_plant_date, $crop_bed_width, $crop_bed_rows, $crop_plant_spacing,
				$crop_target_population, $variety_id)
    {
		
		if ( !$crop_id ) {			
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE crop SET 
			crop_plant_date = :crop_plant_date,			
			crop_bed_width = :crop_bed_width,
			crop_bed_rows = :crop_bed_rows,
			crop_plant_spacing = :crop_plant_spacing,
			crop_target_population = :crop_target_population,
			crop_zone_count = :crop_zone_count,
			crop_zone_sample_count = :crop_zone_sample_count,
			variety_id = :variety_id  
			WHERE crop_id = :crop_id";
        $query = $database->prepare($sql);
        $query->execute(array(':crop_plant_date' => $crop_plant_date, ':crop_bed_width' => $crop_bed_width,
								':crop_bed_rows' => $crop_bed_rows, ':crop_plant_spacing' => $crop_plant_spacing, 
								':crop_target_population' => $crop_target_population, 
								':crop_zone_count' => $crop_zone_count, ':crop_zone_sample_count' => $crop_zone_sample_count, 
								':variety_id' => $variety_id, ':crop_id' => $crop_id));

        if ($query->rowCount() == 1) {
			Session::add('feedback_positive', Text::get('FEEDBACK_CROP_EDITING_SUCCESSFUL'));
            return true;
        }		
        return false;
    }	
	
	/**
     * Delete a specific farm
     * @param int $farm_id id of the farm
     * @return bool feedback (was the item deleted properly ?)
     */
	public static function deleteFarm($farm_id){
		$paddocks = self::getPaddocksByFarmID($farm_id);
		$flag = true;
		foreach ($paddocks as $paddock){
			$flag = false;
			if(self::deletePaddock($paddock->paddock_id)){
				$flag = true;
			}
		}
		if($flag==true){	
			if(self::deleteFarmUserByFarmID($farm_id)){
				if(self::deleteFarmByFarmID($farm_id)){				
					Session::add('feedback_positive', Text::get('FEEDBACK_FARM_DELETION_SUCCESSFUL'));
					// farm data removed so update session variable
					Session::set('user_farms', DatabaseCommon::getFarmDetails());
					return true;
				}				
			}
		}
		Session::add('feedback_negative', Text::get('FEEDBACK_FARM_DELETION_FAILED'));
		return false;
	}

    /**
     * Delete a specific paddock
     * @param int $paddock_id id of the paddock
     * @return bool feedback (was the item deleted properly ?)
     */	
	
	public static function deletePaddock($paddock_id){
		$crops = self::getCropsByPaddockID($paddock_id);
		$flag = true;
		foreach ($crops as $crop){
			$flag = false;
			if(self::deleteCrop($crop->crop_id)){
				$flag = true;
			}
		}
		if($flag==true){			
			if(self::deletePaddockByPaddockID($paddock_id)){
				Session::add('feedback_positive', Text::get('FEEDBACK_PADDOCK_DELETION_SUCCESSFUL'));
				// paddock data removed so update session variable
				Session::set('user_paddocks', DatabaseCommon::getPaddockDetails());
				return true;
			}
		}
		Session::add('feedback_negative', Text::get('FEEDBACK_PADDOCK_DELETION_FAILED'));
		return false;
	}
	
	/**
	* Delete a specific crop
	* @param int $fcrop_id id of the crop
	* @return bool feedback (was the item deleted properly ?)
	*/
	
	public static function deleteCrop($crop_id){		
		$zones = self::getCropZones($crop_id);
		$flag = true;
		foreach ($zones as $zone){
			$flag = false;
			if(self::deleteSamples($zone->zone_id)){				
				if(self::deleteLeafNumber($zone->zone_id)){
					if(self::deleteYieldEstimate($zone->zone_id)){
						// sample data removed so update session variable
						Session::set('user_reports', DatabaseCommon::getSampleDetails());						
						$flag = true;
					}					
				}				
			}			
		}
		if($flag==true){			
			if(self::deleteZonesByCropID($crop_id)){
				if(self::deleteCropByCropID($crop_id)){
					Session::add('feedback_positive', Text::get('FEEDBACK_CROP_DELETION_SUCCESSFUL'));
					// reset user session variable
					Session::set('user_crops', DatabaseCommon::getCropDetails());
					return true;					
				}
			}
		}
		Session::add('feedback_negative', Text::get('FEEDBACK_CROP_DELETION_FAILED'));
		return false;
	}
	
	private static function deleteFarmUserByFarmID($farm_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM farm_users WHERE farm_id = :farm_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':farm_id' => $farm_id));
			if (Config::get('DEBUG_LOG')) {	
				Session::add('_debug_', 'deleteFarmUser('.$farm_id.')');
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;		
	}	
	
	private static function deleteFarmByFarmID($farm_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM farm WHERE farm_id=:farm_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':farm_id' => $farm_id));
			if (Config::get('DEBUG_LOG')) {	
				Session::add('_debug_', 'deleteFarm('.$farm_id.')');		
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;		
	}	
	
	private static function deletePaddockByPaddockID($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM paddock WHERE paddock_id = :paddock_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':paddock_id' => $paddock_id));			
			if (Config::get('DEBUG_LOG')) {					
				Session::add('_debug_', 'deletePaddock('.$paddock_id.')');
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;		
	}
	
	private static function deleteCropByCropID($crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM crop WHERE crop_id = :crop_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':crop_id' => $crop_id));
			if (Config::get('DEBUG_LOG')) {	
				Session::add('_debug_', 'deleteCrop('.$crop_id.')');		
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;		
	}
	
	private static function deleteZonesByCropID($crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM zone WHERE crop_id = :crop_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':crop_id' => $crop_id));			
			if (Config::get('DEBUG_LOG')) {
				Session::add('_debug_', 'deleteZones('.$crop_id.')');			
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;		
	}
	
	private static function deleteSamples($zone_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM sample WHERE zone_id = :zone_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':zone_id' => $zone_id));
			if (Config::get('DEBUG_LOG')) {
				Session::add('_debug_', 'deleteSamples('.$zone_id.')');			
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;
	}
	
	private static function deleteLeafNumber($zone_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM leaf_number WHERE zone_id = :zone_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':zone_id' => $zone_id));
			if (Config::get('DEBUG_LOG')) {
				Session::add('_debug_', 'deleteLeafNumber('.$zone_id.')');	
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;
	}
	
	private static function deleteYieldEstimate($zone_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM yield WHERE zone_id = :zone_id";
		$query = $database->prepare($sql);				
		try {
			$query->execute(array(':zone_id' => $zone_id));
			if (Config::get('DEBUG_LOG')) {					
				Session::add('_debug_', 'deleteYieldEstimate('.$zone_id.')');		
			}
			return true;
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		return false;
	}
	
	private static function getCropZones($crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT zone_id FROM zone WHERE crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));
		
		return $query->fetchAll();
	}
	

	public static function getPaddockGooglePlaceID($paddock_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_google_place_id FROM paddock WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_google_place_id;		
		
	}
	
	public static function farmsExist($user_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT farm_id FROM farm_users WHERE user_id = :user_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));
		if ($query->rowCount() >= 1) {
			if (Config::get('DEBUG_LOG')) {			
				Session::add('_debug_', 'farmsExist(true)');
			}
			return true;
		} else {
			return false;
		}
					
	}	
	
	public static function paddocksExist($farm_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_id FROM paddock WHERE farm_id = :farm_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));
		if ($query->rowCount() == 1) {
			if (Config::get('DEBUG_LOG')) {			
				Session::add('_debug_', 'paddocksExist(true)');
			}
			return true;
		}
		return false;			
	}
	
	public static function cropsExist($crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_id FROM crop WHERE crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));
		if ($query->rowCount() == 1) {
			if (Config::get('DEBUG_LOG')) {			
				Session::add('_debug_', 'cropsExist(true)');
			}
			return true;
		}
		return false;			
	}	
	
	public static function getPaddockPolygonPathByID($paddock_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_google_latlong_paths FROM paddock WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_google_latlong_paths;		
		
	}
	
	public static function addFarmUser($farm_id, $user_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "INSERT INTO farm_users 
			(farm_id, user_id) 
			VALUES 
			(:farm_id, :user_id)";
		$query = $database->prepare($sql);
		$query->execute(array( ':farm_id' => $farm_id, ':user_id' => $user_id ));			
		if ($query->rowCount() == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function farmUserExists($farm_id, $user_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT farm_id, user_id FROM farm_users 
			WHERE farm_id = :farm_id AND user_id = :user_id";
		$query = $database->prepare($sql);
		$query->execute(array( ':farm_id' => $farm_id, ':user_id' => $user_id ));			
		if ($query->rowCount() == 1) {
			return true;
		} else {
			return false;
		}		
	}

}