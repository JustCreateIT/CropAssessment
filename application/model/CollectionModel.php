<?php

/**
 * CollectionModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class CollectionModel
{

	public static function updateCollectionData(
		$zone_sample_plot_id, $date_collected, $sample_count, $sample_comment = null, $sample_ela_score = null, 
		$sample_bulb_weight = null,	$growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id)
    {        
		
        $database = DatabaseFactory::getFactory()->getConnection();		
			
        $sql = "UPDATE sample 
				SET 
					sample_date = :sample_date, 
					sample_count = :sample_count, 
					sample_comment = :sample_comment, 
					sample_ela_score = :sample_ela_score, 
					sample_bulb_weight = :sample_bulb_weight
				WHERE
					farm_id = :farm_id
					AND paddock_id = :paddock_id
					AND crop_id = :crop_id
					AND zone_id = :zone_id
					AND zone_sample_plot_id = :zone_sample_plot_id	
					AND growth_stage_id = :growth_stage_id";
		
        $query = $database->prepare($sql);
		
		$sample_date = date("Y-m-d", strtotime($date_collected));
		
		/*
		Session::add('feedback_negative', $zone_sample_plot_id.';'.$sample_date.';'.$sample_count.';'.$sample_comment.';'.$sample_ela_score.';'.$sample_bulb_weight.';'.$growth_stage_id.';'.$zone_id.';'.$paddock_id.';'.$farm_id);
		
		return false;
		*/
		try{
			$query->execute(array(				
				':sample_date' => $sample_date,
				':sample_count' => $sample_count,
				':sample_comment' => $sample_comment,
				':sample_ela_score' => $sample_ela_score,
				':sample_bulb_weight' => $sample_bulb_weight,
				':farm_id' => $farm_id,
				':paddock_id' => $paddock_id,
				':crop_id' => $crop_id,
				':zone_id' => $zone_id,
				':zone_sample_plot_id' => $zone_sample_plot_id,
				':growth_stage_id' => $growth_stage_id
				));

		} catch (PDOException $e) {
					Session::add('feedback_negative', 'PDOException: '.$e->getMessage());
		} catch (Exception $e) {
					Session::add('feedback_negative', 'General Exception: '.$e->getMessage());
		}			
		
		//Session::add('feedback_negative', $query->rowCount());
        if ($query->rowCount() == 1) {
			return true;
        } else {
			// default return
			return false;		
		}
    }
	
	public static function enterCollectionData(
		$zone_sample_plot_id, $date_collected, $sample_count, $sample_comment = null, $sample_ela_score = null, 
		$sample_bulb_weight = null,	$growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id)
    {        

        $database = DatabaseFactory::getFactory()->getConnection();			

        $sql = "INSERT INTO sample 
		(zone_sample_plot_id, sample_date, sample_count, sample_comment, sample_ela_score, sample_bulb_weight, 
		growth_stage_id, zone_id, crop_id, paddock_id, farm_id) 
		VALUES 
		(:zone_sample_plot_id, :sample_date, :sample_count, :sample_comment, :sample_ela_score, :sample_bulb_weight, 
		:growth_stage_id, :zone_id, :crop_id, :paddock_id, :farm_id)";
		
        $query = $database->prepare($sql);
		
		$sample_date = date("Y-m-d", strtotime($date_collected));
		
		try{
			$query->execute(array(
				':zone_sample_plot_id' => $zone_sample_plot_id,
				':sample_date' => $sample_date,
				':sample_count' => $sample_count,
				':sample_comment' => $sample_comment,
				':sample_ela_score' => $sample_ela_score,
				':sample_bulb_weight' => $sample_bulb_weight,
				':growth_stage_id' => $growth_stage_id,
				':zone_id' => $zone_id,
				':crop_id' => $crop_id,
				':paddock_id' => $paddock_id,
				':farm_id' => $farm_id
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
			return false;		
		}
    }
	
	public static function emailSampleImage(
		$zone_sample_plot_id, $date_collected, $sample_count, $sample_comment = null, $sample_ela_score = null, 
		$sample_bulb_weight = null,	$growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id, $key)
	{
		
		if (self::validateSampleImageFile($key) AND self::sendImageAsEmail(
		$zone_sample_plot_id, $date_collected, $sample_count, $sample_comment, $sample_ela_score, 
		$sample_bulb_weight, $growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id, $key)) {			
			Session::add('feedback_positive', $_FILES['sample_file']['name'][$key].' '.Text::get('FEEDBACK_SAMPLE_PLOT_EMAIL_SEND_SUCCESSFUL'));
			return true;
		}
		return false;
	}
	
	private static function sendImageAsEmail(
		$zone_sample_plot_id, $date_collected, $sample_count, $sample_comment = null, $sample_ela_score = null, 
		$sample_bulb_weight = null,	$growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id, $key) 
	{
		
		$farm_name = ucwords(DatabaseCommon::getFarmNameByID($farm_id));
		$paddock_name = ucwords(DatabaseCommon::getPaddockNameByID($paddock_id));
		$growth_stage_name = ucwords(DatabaseCommon::getGrowthStageNameByID($growth_stage_id));
			
		$subject = 'Image: '.$farm_name.'-'.$paddock_name.'-'.$growth_stage_name.'-Zone ID '.$zone_id.'-Plot ID '.$zone_sample_plot_id;
		
		$body = '<br>'.
				"Farm: ".$farm_name.'<br>'. 
				"Paddock: ".$paddock_name.'<br>'.
				"Growth Stage: ".$growth_stage_name.'<br>'.
				"Zone ID: ".$zone_id.'<br>'.
				"Zone Plot ID: ".$zone_sample_plot_id.'<br>'.
				"Date Collected: ".$date_collected.'<br>'.
				"Sample Count: ".$sample_count.'<br>';
		if (!empty($sample_ela_score)) {
			$body .= "Groundcover %: ".$sample_ela_score.'<br>';		
		}	
		if (!empty($sample_bulb_weight)) {
			$body .= "Bulb Weight: ".$sample_bulb_weight.'<br>';		
		}						
		$body .= "Comment: ".$sample_comment.'<br>';

        $to_email = Config::get('EMAIL_SAMPLE_IMAGE_SEND_TO_EMAIL'); // email address of mailbox to receive image files
		
        $from_email = Session::get('user_email'); // signed in user email address		
        $from_name = ucwords(Session::get('user_first_name')).' '.ucwords(Session::get('user_last_name')); // signed in username		

        $mail = new Mail; 
      
        $mail_sent = $mail->sendMail($to_email, $from_email, $from_name, $subject, $body, $key);

        if (!$mail_sent) {
            Session::add('feedback_negative', Text::get('FEEDBACK_SAMPLE_PLOT_EMAIL_SEND_FAILED') . $mail->getError() );
            return false;
        }        
		return true;
	}
	
	/**
	 * Checks if the image folder exists and is writable
	 *
	 * @return bool success status
	 */
	public static function isSampleImageFolderWritable()
	{
		if (is_dir(Config::get('SAMPLE_PLOT_IMAGE_PATH')) AND is_writable(Config::get('SAMPLE_PLOT_IMAGE_PATH'))) {
			return true;
		}

		Session::add('feedback_negative', Text::get('FEEDBACK_PATH_SAMPLE_PLOTS_FOLDER_DOES_NOT_EXIST_OR_NOT_WRITABLE'));
		return false;
	}

	/**
	 * Validates the image
	 * TODO totally decouple
	 *
	 * @return bool
	 */
	public static function validateSampleImageFile($key)
	{
		if (!isset($_FILES['sample_file']['name'][$key]) && empty($_FILES['sample_file']['name'][$key])) {
			Session::add('feedback_negative', Text::get('FEEDBACK_SAMPLE_PLOT_IMAGE_UPLOAD_FAILED'));
			return false;
		}

		if ($_FILES['sample_file']['size'][$key] > 5000000) {
			// if input file too big (>5MB)
			Session::add('feedback_negative', Text::get('FEEDBACK_SAMPLE_PLOT_UPLOAD_TOO_BIG'));
			return false;
		}

		$imageType = exif_imagetype($_FILES['sample_file']['tmp_name'][$key]);
		if (($imageType === IMAGETYPE_JPEG) || ($imageType === IMAGETYPE_PNG) || ($imageType === IMAGETYPE_GIF)) {
			return true;
		} else {			
			//Session::add('feedback_negative', 'IMAGE TYPE: '.print_r($imageType, true));
			//Session::add('feedback_negative', 'ERROR CODE: '.print_r($_FILES['sample_file']['error'], true));
			Session::add('feedback_negative', Text::get('FEEDBACK_SAMPLE_PLOT_IMAGE_UPLOAD_WRONG_TYPE'));
			return false;
		}

		/*
		// get the image width, height and mime type
		//$image_proportions = getimagesize($_FILES['sample_file']['tmp_name'][$key]);
		$image_proportions = getimagesize($_FILES["sample_file"]["tmp_name"][$key]);		

		if (!($image_proportions['mime'] == 'image/jpeg')) {
			Session::add('feedback_negative', 'MIME Type: '.print_r($image_proportions, true));
			Session::add('feedback_negative', Text::get('FEEDBACK_SAMPLE_PLOT_IMAGE_UPLOAD_WRONG_TYPE'));
			return false;
		}
		 */
		//Session::add('feedback_positive', print_r($image_proportions, true));
		//return true;
	}


    /**
     * Resize sample plot image (while keeping aspect ratio and cropping it off in a clean way).
     * Only works with gif, jpg and png file types. If you want to change this also have a look into
     * method validateImageFile() inside this model.
     *
     * TROUBLESHOOTING: You don't see the new image ? Press F5 or CTRL-F5 to refresh browser cache.
     *
     * @param string $source_image The location to the original raw image
     * @param string $destination The location to save the new image
     * @param int $final_width The desired width of the new image
     * @param int $final_height The desired height of the new image
     *
     * @return bool success state
     */
    public static function resizeSamplePlotImage($source_image, $destination_filename, $new_width, $new_height, $image_quality)
    {
        $imageData = getimagesize($source_image);
        $width = $imageData[0];
        $height = $imageData[1];
        $mimeType = $imageData['mime'];

        if (!$width || !$height) {
            return false;
        }

        switch ($mimeType) {
            case 'image/jpeg': $myImage = imagecreatefromjpeg($source_image); break;
            case 'image/png': $myImage = imagecreatefrompng($source_image); break;
            case 'image/gif': $myImage = imagecreatefromgif($source_image); break;
            default: return false;
        }
        // copying the part into thumbnail, maybe edit this for square avatars
        $thumb = imagecreatetruecolor($new_width, $new_height);
		
		
		// function imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {}		
		if (!imagecopyresampled($thumb, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width, $height)){
			Session::add('feedback_negative', 'imagecopyresampled'.imagecopyresampled);		
		}

        // add '.jpg' to file path, save it as a .jpg file with our $destination_filename parameter
        if (!imagejpeg($thumb, $destination_filename, $image_quality)){
			//Session::add('feedback_negative', 'imagejpeg failed; destination filename: '.$destination_filename);	
		}
        imagedestroy($thumb);		

        if (file_exists($destination_filename)) {
            return true;
        }
		/*
		Session::add('feedback_negative', 'Source image: '.$source_image);		
		Session::add('feedback_negative', 'Destination filename: '.$destination_filename);
		Session::add('feedback_negative', Text::get('FEEDBACK_SAMPLE_PLOT_IMAGE_RESIZE_FAILED'));
		 */
        return false;
    }
	
}
