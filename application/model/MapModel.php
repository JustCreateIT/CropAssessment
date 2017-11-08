<?php

/**
 * Class MapModel
 *
 * This class contains everything that is related to mapping farms and paddocks.
 */
class MapModel
{

	public static function getPaddockGooglePlaceID($paddock_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_google_place_id FROM paddock WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_google_place_id;		
		
	}
	
	public static function getPaddockPolygonPathByID($paddock_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT paddock_google_latlong_paths FROM paddock WHERE paddock_id = :paddock_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':paddock_id' => $paddock_id));

        return $query->fetch()->paddock_google_latlong_paths;		
		
	}
	
}
