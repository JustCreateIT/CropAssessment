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
	 /*
    public static function getPaddocksByFarmID($farm_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_id, paddock_name, paddock_address, paddock_area, paddock_zone_count, paddock_zone_sample_count,
				paddock_google_area, paddock_plant_date, paddock_bed_width, paddock_bed_rows, paddock_plant_spacing,
				paddock_target_population, variety_id
		FROM paddock WHERE farm_id = :farm_id";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));		

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }
	*/
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
	
    public static function getCropsByCropID($crop_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM crop WHERE crop_id = :crop_id";
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));		

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
     * Update an existing Farm
     * @param int $farm_id id of the specific farm
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
     * Update an existing Farm
     * @param int $farm_id id of the specific farm
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
		Session::add('feedback_negative', Text::get('FEEDBACK_CROP_EDITING_FAILED'));
        return false;
    }	
	
	
	/**
     * Delete a specific farm
     * @param int $farm_id id of the farm
     * @return bool feedback (was the item deleted properly ?)
     */
    public static function deleteFarmByID($farm_id)
    {
        if ( !$farm_id ) {
            return false;
        }
		
		// get array of paddock ids associated with current farm
		$paddock_ids = self::getFarmPaddockIDs($farm_id);		

		if(isset($paddock_ids)){
			foreach($paddock_ids as $p){
				// get array of crop ids associated with current farm->paddock
				$crop_ids = self::getFarmPaddockCropIDs($farm_id, $p->paddock_id);
				if(isset($crop_ids)){
					foreach($crop_ids as $c){
						// remove any associated crops first ...
						if (self::deleteCropByID($c->crop_id)) {
							// remove any associated paddocks first ...
							if (self::deletePaddockByID($p->paddock_id)) {
								if (self::farmUsersExist($farm_id)){
									if (Config::get('DEBUG_LOG')) {			
										Session::add('_debug_', 'farmUsersExist(true): now delete');
									}
									// remove any farm_users associated with current farm ...
									if (self::deleteFarmUsersByFarmID($farm_id)) {
										$database = DatabaseFactory::getFactory()->getConnection();
										$sql = "DELETE FROM farm WHERE farm_id = :farm_id";
										$query = $database->prepare($sql);
										try {
											$query->execute(array(':farm_id' => $farm_id));
											$rows = $query->rowCount();
											if ($rows > 0) {
												if (Config::get('DEBUG_LOG')) {			
													Session::add('_debug_', 'deleteFarmByID(true): rowcount='.print_r($rows, true));
												}
												// don't care so long as no errors
												Session::add('feedback_positive', Text::get('FEEDBACK_FARM_DELETION_SUCCESSFUL'));
												return true;
											}
										} catch (PDOException $e) {
											Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
										} catch (Exception $e) {
											Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
										}	
									}					
								}
							}
						}
					}
				}	
			}			
		}

		Session::add('feedback_negative', Text::get('FEEDBACK_FARM_DELETION_FAILED'));
		return false;	
    }
	
	private static function farmUsersExist($farm_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT * FROM farm_users WHERE farm_id = :farm_id";
		$query = $database->prepare($sql);
		
		try {
			$query->execute(array(':farm_id' => $farm_id));
			$rows = $query->rowCount();
			if ($rows > 0) {
				if (Config::get('DEBUG_LOG')) {			
					Session::add('_debug_', 'farmUsersExist(true): rowcount='.print_r($rows, true));
				}
				// don't care so long as no errors
				return true;
			}
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}
		// no farm_user records for the associated farm_id
		return false;
	}

	private static function deleteFarmUsersByFarmID($farm_id)	{
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "DELETE FROM farm_users WHERE farm_id = :farm_id";
		$query = $database->prepare($sql);

		try {
			$query->execute(array(':farm_id' => $farm_id));
			$rows = $query->rowCount();
			if ($rows > 0) {
				if (Config::get('DEBUG_LOG')) {			
					Session::add('_debug_', 'deleteFarmByID(true): rowcount='.print_r($rows, true));
				}
				// don't care so long as no errors
				return true;
			}
		} catch (PDOException $e) {
			Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
			Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}			 
		return false;
	}

    /**
     * Delete a specific paddock
     * @param int $paddock_id id of the paddock
     * @return bool feedback (was the item deleted properly ?)
     */
    public static function deletePaddockByID($paddock_id)
    {
        if ( !$paddock_id ) {
            return false;
        }
		// remove any associated zones first ...
		if (self::deletePaddockZones($paddock_id)) {
			// remove the defined paddock ...
			$database = DatabaseFactory::getFactory()->getConnection();
			$sql = "DELETE FROM paddock WHERE paddock_id = :paddock_id";
			$query = $database->prepare($sql);

			try {
				$query->execute(array(':paddock_id' => $paddock_id));
				$rows = $query->rowCount();
				if ($rows > 0) {
					if (Config::get('DEBUG_LOG')) {			
						Session::add('_debug_', 'deletePaddockByID(true): rowcount='.print_r($rows, true));
					}
					// don't care so long as no errors
					Session::add('feedback_positive', Text::get('FEEDBACK_PADDOCK_DELETION_SUCCESSFUL'));
					return true;
				}
			} catch (PDOException $e) {
				Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
			} catch (Exception $e) {
				Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
			}	
		}
		Session::add('feedback_negative', Text::get('FEEDBACK_PADDOCK_DELETION_FAILED'));
		return false;
    }
	
	private static function deletePaddockZones($paddock_id)
    {
		// remove all associated samples first ...
		if (self::deletePaddockSamples($paddock_id)) {
			// remove all associated zones ...
			$database = DatabaseFactory::getFactory()->getConnection();
			$sql = "DELETE FROM zone WHERE paddock_id = :paddock_id";
			$query = $database->prepare($sql);

			try {
				$query->execute(array(':paddock_id' => $paddock_id));
				$rows = $query->rowCount();
				if ($rows > 0) {
					if (Config::get('DEBUG_LOG')) {			
						Session::add('_debug_', 'deletePaddockZones(true): rowcount='.print_r($rows, true));
					}
					// don't care how many zones so long as no errors ...
					return true;
				}
			} catch (PDOException $e) {
				Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
			} catch (Exception $e) {
				Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
			}	
		}
		return false;	
	}	

	private static function deletePaddockSamples($paddock_id)
    {
		// remove any associated yield estimates first ...
		if (self::deletePaddockZoneYieldEstimates($paddock_id)) {			
			if (self::paddockHasSamples($paddock_id)){
				// remove all associated samples ...
				$database = DatabaseFactory::getFactory()->getConnection();
				$sql = "DELETE FROM sample WHERE paddock_id = :paddock_id";
				$query = $database->prepare($sql);				
				try {
					$query->execute(array(':paddock_id' => $paddock_id));
					$rows = $query->rowCount();
					if ($rows > 0) {
						if (Config::get('DEBUG_LOG')) {			
							Session::add('_debug_', 'deletePaddockSamples(true): rowcount='.print_r($rows, true));
						}
						// don't care so long as no errors
						return true;
					}
				} catch (PDOException $e) {
					Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
				} catch (Exception $e) {
					Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
				}					
			} else {
				return true;
			}
		}
		return false;
	}
	
	/**
     * Delete a specific paddock
     * @param int $paddock_id id of the paddock
     * @return bool feedback (was the item deleted properly ?)
     */
    public static function deleteCropByID($crop_id)
    {
        if ( !$crop_id ) {
            return false;
        }
		// remove any associated zones first ...
		if (self::deleteCropZones($crop_id)) {
			// remove the defined paddock ...
			$database = DatabaseFactory::getFactory()->getConnection();
			$sql = "DELETE FROM crop WHERE crop_id = :crop_id";
			$query = $database->prepare($sql);

			try {
				$query->execute(array(':crop_id' => $crop_id));
				$rows = $query->rowCount();
				if ($rows > 0) {
					if (Config::get('DEBUG_LOG')) {			
						Session::add('_debug_', 'deleteCropByID(true): rowcount='.print_r($rows, true));
					}
					// don't care so long as no errors
					Session::add('feedback_positive', Text::get('FEEDBACK_CROP_DELETION_SUCCESSFUL'));
					return true;
				}
			} catch (PDOException $e) {
				Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
			} catch (Exception $e) {
				Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
			}	
		}
		Session::add('feedback_negative', Text::get('FEEDBACK_CROP_DELETION_FAILED'));
		return false;
    }
	
	private static function deleteCropZones($crop_id)
    {
		// remove all associated samples first ...
		if (self::deleteCropSamples($crop_id)) {
			// remove all associated zones ...
			$database = DatabaseFactory::getFactory()->getConnection();
			$sql = "DELETE FROM zone WHERE crop_id = :crop_id";
			$query = $database->prepare($sql);

			try {
				$query->execute(array(':crop_id' => $crop_id));
				$rows = $query->rowCount();
				if ($rows > 0) {
					if (Config::get('DEBUG_LOG')) {			
						Session::add('_debug_', 'deleteCropZones(true): rowcount='.print_r($rows, true));
					}
					// don't care how many zones so long as no errors ...
					return true;
				}
			} catch (PDOException $e) {
				Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
			} catch (Exception $e) {
				Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
			}	
		}
		return false;	
	}

private static function deleteCropSamples($crop_id)
    {
		// remove any associated yield estimates first ...
		if (self::deleteCropZoneYieldEstimates($crop_id)) {			
			if (self::cropHasSamples($crop_id)){
				// remove all associated samples ...
				$database = DatabaseFactory::getFactory()->getConnection();
				$sql = "DELETE FROM sample WHERE crop_id = :crop_id";
				$query = $database->prepare($sql);				
				try {
					$query->execute(array(':crop_id' => $crop_id));
					$rows = $query->rowCount();
					if ($rows > 0) {
						if (Config::get('DEBUG_LOG')) {			
							Session::add('_debug_', 'deleteCropSamples(true): rowcount='.print_r($rows, true));
						}
						// don't care so long as no errors
						return true;
					}
				} catch (PDOException $e) {
					Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
				} catch (Exception $e) {
					Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
				}					
			} else {
				return true;
			}
		}
		return false;
	}	
	
	//Todo
	private static function deleteCropZoneYieldEstimates($crop_id)
    {
		// determine if yield estimates exist for the paddock ...
		if (self::cropHasYieldEstimates($crop_id)){
			// remove any associated yield estimates at paddock scale ...
			$database = DatabaseFactory::getFactory()->getConnection();
			$sql = "DELETE FROM yield WHERE crop_id = :crop_id";
			$query = $database->prepare($sql);
			try {
				$query->execute(array(':crop_id' => $crop_id));
				$rows = $query->rowCount();
				if ($rows > 0) {
					if (Config::get('DEBUG_LOG')) {			
						Session::add('_debug_', 'deleteCropZoneYieldEstimates(true): rowcount='.print_r($rows, true));
					}
					// don't care so long as no errors
					return true;
				}
			} catch (PDOException $e) {
				Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
			} catch (Exception $e) {
				Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
			}			
			return false;
		} else { 
			return true;
		}
	}
	
	//Todo
	private static function deletePaddockZoneYieldEstimates($paddock_id)
    {
		// determine if yield estimates exist for the paddock ...
		if (self::paddockHasYieldEstimates($paddock_id)){
			// remove any associated yield estimates at paddock scale ...
			$database = DatabaseFactory::getFactory()->getConnection();
			$sql = "DELETE FROM yield WHERE paddock_id = :paddock_id";
			$query = $database->prepare($sql);
			try {
				$query->execute(array(':paddock_id' => $paddock_id));
				$rows = $query->rowCount();
				if ($rows > 0) {
					if (Config::get('DEBUG_LOG')) {			
						Session::add('_debug_', 'deletePaddockZoneYieldEstimates(true): rowcount='.print_r($rows, true));
					}
					// don't care so long as no errors
					return true;
				}
			} catch (PDOException $e) {
				Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
			} catch (Exception $e) {
				Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
			}			
			return false;
		} else { 
			return true;
		}
	}
	
	private static function getFarmPaddockCropIDs($farm_id, $paddock_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_id FROM crop WHERE farm_id = :farm_id AND paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

        return $query->fetchAll();	
	}
	
	private static function getFarmPaddockIDs($farm_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_id FROM paddock WHERE farm_id = :farm_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));

        return $query->fetchAll();	
	}
	
	
	// Todo
	private static function paddockHasSamples($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT sample_id FROM sample WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));
		$rows = $query->rowCount();
        if ($rows > 0) {
			if (Config::get('DEBUG_LOG')) {			
				Session::add('_debug_', 'paddockHasSamples(true): rowcount='.print_r($rows, true));
			}
			// don't care how many as long as they exist...
			return true;
		}
		return false;
	}
	
	// Todo
	private static function cropHasSamples($crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT sample_id FROM sample WHERE crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));
		$rows = $query->rowCount();
        if ($rows > 0) {
			if (Config::get('DEBUG_LOG')) {			
				Session::add('_debug_', 'cropHasSamples(true): rowcount='.print_r($rows, true));
			}
			// don't care how many as long as they exist...
			return true;
		}
		return false;
	}	
	
			// Todo
	private static function cropHasYieldEstimates($crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT yield_estimate FROM yield WHERE crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':crop_id' => $crop_id));
		$rows = $query->rowCount();
        if ($rows > 0) {
			if (Config::get('DEBUG_LOG')) {			
				Session::add('_debug_', 'cropHasYieldEstimates(true): rowcount='.print_r($rows, true));
			}
			// don't care how many as long as they exist...
			return true;
		}
		return false;
	}
		// Todo
		private static function paddockHasYieldEstimates($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT yield_estimate FROM yield WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));
		$rows = $query->rowCount();
        if ($rows > 0) {
			if (Config::get('DEBUG_LOG')) {			
				Session::add('_debug_', 'paddockHasYieldEstimates(true): rowcount='.print_r($rows, true));
			}
			// don't care how many as long as they exist...
			return true;
		}
		return false;
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
	
	public static function cropsExist($paddock_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT crop_id FROM crop WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));
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