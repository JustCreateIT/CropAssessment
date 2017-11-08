<?php

/**
 * EditModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) model
 * Can edit (read/update) existing farms and paddocks
 * Can delete (read/delete) existing paddocks (if no samples exist) and farms (if no paddocks exist)
 */
class EditModel
{
/**
     * Get all paddocks related to a particular farm
     * @return array an array with several objects (the results)
     */
    public static function getPaddocksByFarmID($farm_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_name, paddock_address, paddock_area, paddock_zone_count, paddock_zone_sample_count, paddock_google_area, 
				paddock_plant_date, paddock_bed_width, paddock_bed_rows, paddock_plant_spacing, paddock_target_population, variety_id
		FROM paddock WHERE farm_id = :farm_id";
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id));

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

        $sql = "SELECT paddock_name, paddock_address, paddock_area, paddock_zone_count, paddock_zone_sample_count, 
				paddock_plant_date, paddock_bed_width, paddock_bed_rows, paddock_plant_spacing, paddock_target_population, variety_id 
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

        $sql = "SELECT DISTINCT f.farm_id, f.farm_name, f.farm_contact_firstname, 
			f.farm_contact_lastname, f.farm_email_address, f.farm_phone_number 
			FROM farm f, farm_users u
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
		/*	
		Session::add('feedback_positive', 
		':farm_id=>'.$farm_id.
		' :farm_name=>'.$farm_name.
		' :farm_contact_firstname=>'.$farm_contact_firstname. 
		' :farm_contact_lastname=>'.$farm_contact_lastname. 
		' :farm_email_address=>'.$farm_email_address.
		' :farm_phone_number=>'.$farm_phone_number);
		*/
			
        $query = $database->prepare($sql);
		
        $query->execute(array(':farm_name' => $farm_name, 
			':farm_contact_firstname' => $farm_contact_firstname, 
			':farm_contact_lastname' => $farm_contact_lastname, 
			':farm_email_address' => $farm_email_address,
			':farm_phone_number' => $farm_phone_number,
			':farm_id' => $farm_id));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_FARM_EDIT_FAILED'));
        return false;
    }
	
    /**
     * Update an existing Farm
     * @param int $farm_id id of the specific farm
     * @param string $farm_name, $farm_contact_firstname, $farm_contact_lastname 
	 * $farm_email_address, $farm_phone_number new details of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public static function updatePaddock($paddock_id, $paddock_name, $paddock_address, $paddock_area, 
		$paddock_zone_count, $paddock_zone_sample_count, $paddock_bed_width, $paddock_bed_rows, $paddock_plant_spacing,
		$paddock_target_population, $variety_id)
    {
        if ( !$paddock_id ) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE farm SET 
			paddock_name = :paddock_name,
			paddock_address = :paddock_address,
			paddock_area = :paddock_area,
			paddock_zone_count = :paddock_zone_count,
			paddock_zone_sample_count = :paddock_zone_sample_count,
			paddock_bed_width = :paddock_bed_width,
			paddock_bed_rows = :paddock_bed_rows,
			paddock_plant_spacing = :paddock_plant_spacing,
			paddock_target_population = :paddock_target_population,
			variety_id = :variety_id  
			WHERE paddock_id = :paddock_id";
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_name' => $paddock_name, ':paddock_address' => $paddock_address, 
			':paddock_area' => $paddock_area, ':paddock_zone_count' => $paddock_zone_count,
			':paddock_zone_sample_count' => $paddock_zone_sample_count,
			':paddock_bed_width' => $paddock_bed_width, ':paddock_bed_rows' => $paddock_bed_rows,
			':paddock_plant_spacing' => $paddock_plant_spacing, ':paddock_target_population' => $paddock_target_population, 
			':variety_id' => $variety_id, ':paddock_id' => $paddock_id));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_FARM_EDIT_FAILED'));
        return false;
    }	

    /**
     * Delete a specific farm or paddock
     * @param int $item_id id of either the farm or paddock
	 * @param int $type_id type_id of either the farm or paddock 
     * @return bool feedback (was the item deleted properly ?)
     */
    public static function deleteItem($item_id, $type_id)
    {
        if (!$item_id || !$type_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

		// If type_id = 1 (farm) then need to delete all paddocks=>zones=>samples=>yields prior to removing farm
		// should only be available to administrator user
		
        $sql = "DELETE FROM notes WHERE note_id = :note_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':note_id' => $note_id, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_DELETION_FAILED'));
        return false;
    }
	
	private function getTypeID($item){
		switch (strtolower($item)){
			case 'farm':
				$type = 1;
				break;
			case 'paddock':
				$type = 2;
				break;
			default:
				// To-Do 
				//trap error			
		}
		
		return $type;
	}
	
}