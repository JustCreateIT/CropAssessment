<?php

/**
 * ReportsModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class ReportsModel
{	

	public static function buildAssessmentReport($farm_id, $paddock_id, $crop_id, $growth_stage_id)
    {
        
		/*
		echo '<pre>';
		echo var_dump("farm_id=".$farm_id);
		echo var_dump("paddock_id=".$paddock_id);
		echo var_dump("crop_id=".$crop_id);
		echo var_dump("growth_stage_id=".$growth_stage_id);
		echo '<pre>';	
		*/
		
		/* At the specific growth stage get all sample counts for each zone; */

		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT 
				AVG(s.sample_count) as sample_plant_average,
				AVG(s.sample_ela_score) as ground_cover_average, 
				AVG(s.sample_bulb_weight) as sample_bulb_average, s.zone_id,
				z.zone_name, z.zone_paddock_percentage, 
				p.paddock_area, 
				c.crop_plant_date, c.crop_bed_width, c.crop_bed_rows, 
				c.crop_plant_spacing, c.crop_target_population 
			FROM 
				sample s, zone z, paddock p, crop c
			WHERE				
				s.growth_stage_id = :growth_stage_id AND
				p.paddock_id = :paddock_id AND
				s.zone_id = z.zone_id AND
				s.crop_id = z.crop_id AND
				s.paddock_id = p.paddock_id AND
				c.paddock_id = p.paddock_id AND
				s.farm_id = z.farm_id				
			GROUP BY 
				s.zone_id	
			ORDER BY 
				z.zone_id";
		
        $query = $database->prepare($sql);
        //$query->execute(array(':growth_stage_id' => $growth_stage_id,':paddock_id' => $paddock_id, ':crop_id' => $crop_id));
		$query->execute(array(':growth_stage_id' => $growth_stage_id,':paddock_id' => $paddock_id));
		
		//$zone_count = DatabaseCommon::getPaddockZoneCount($farm_id, $paddock_id);
		$zone_count = DatabaseCommon::getCropZoneCount($crop_id);		
		$plot_width = Config::get('SAMPLE_PLOT_WIDTH');		
        $zone_sample_counts = array();
		$i = 1;
		$report_weighted_sum = 0;

		
        foreach ($query->fetchAll() as $result) {			
			
		/* object(stdClass)#48 (12) {
			  ["sample_plant_average"]=>
			  string(7) "93.7500"
			  ["ground_cover_average"]=>
			  string(9) "12.500000"
			  ["sample_bulb_average"]=>
			  string(9) "0.0000000"
			  ["zone_id"]=>
			  string(2) "51"
			  ["zone_name"]=>
			  string(11) "Mustard Dry"
			  ["zone_paddock_percentage"]=>
			  string(2) "18"
			  ["paddock_area"]=>
			  string(4) "1.35"
			  ["paddock_plant_date"]=>
			  string(10) "2016-09-07"
			  ["paddock_bed_width"]=>
			  string(4) "1.82"
			  ["paddock_bed_rows"]=>
			  string(1) "8"
			  ["paddock_plant_spacing"]=>
			  string(2) "72"
			  ["paddock_target_population"]=>
			  string(6) "783000"
			} */					
			$zone_sample_counts[$result->zone_id] = new stdClass();
            $zone_sample_counts[$result->zone_id]->zone_id = $result->zone_id;
			$plot_population_average = $result->sample_plant_average;
			$crop_plant_date = $result->crop_plant_date;
			$crop_bed_width = $result->crop_bed_width;
			$crop_bed_rows = $result->crop_bed_rows;
			$crop_plant_spacing = $result->crop_plant_spacing;			
			$plants_per_sqm = self::unitPerSquareMeter($plot_population_average, $crop_bed_width, $plot_width);
			
			/*		
		echo '<pre>';
		echo var_dump("plants_per_sqm=".$plants_per_sqm);
		echo var_dump("crop_bed_width=".$crop_bed_width);
		echo var_dump("plot_population_average=".$plot_population_average);		
		echo '<pre>';
			*/
			
			$zone_sample_counts[$result->zone_id]->sample_plant_average = $plants_per_sqm;			 
			$zone_sample_counts[$result->zone_id]->ground_cover_average = $result->ground_cover_average;
			$zone_sample_weight_kg_sqm = self::unitPerSquareMeter($result->sample_bulb_average, $crop_bed_width, $plot_width);			
			$zone_sample_counts[$result->zone_id]->sample_average_weight = $zone_sample_weight_kg_sqm;
			$zone_sample_counts[$result->zone_id]->zone_name = !empty($result->zone_name)? $result->zone_name: Statistics::getCharFromNumber($i);
            $zone_sample_counts[$result->zone_id]->zone_paddock_percentage = $result->zone_paddock_percentage;
			$zone_sample_counts[$result->zone_id]->paddock_area = $result->paddock_area;
			$zone_area = ($result->zone_paddock_percentage/100)*$result->paddock_area;
			$zone_sample_counts[$result->zone_id]->zone_area = $zone_area;
			$zone_target_population = ($result->zone_paddock_percentage/100)*$result->crop_target_population;
			$zone_sample_counts[$result->zone_id]->zone_target_population = $zone_target_population;
			$zone_population_estimate = self::zonePopulationEstimate($plot_population_average, $zone_area, $crop_bed_width);
			$zone_sample_counts[$result->zone_id]->zone_population_estimate = $zone_population_estimate;
			$zone_sample_counts[$result->zone_id]->zone_difference = self::zonePopulationDifference($zone_target_population,$zone_population_estimate);	
			
			// Initialise default yield values
			// This will ensure that we have a full set of results for the charts even if they are zero values
			for ($i=2;$i<=5;$i++) {
				if (!self::zoneYieldEstimatesExist($farm_id, $paddock_id, $crop_id, $result->zone_id, $i)) {
					self::setZoneYieldEstimateByGrowthStage($farm_id, $paddock_id, $crop_id, $result->zone_id, $i, 0);
				}		
			}
			
			$zone_id = $result->zone_id;
			
			switch ($growth_stage_id){
				case 1:					
					$populationLimited = self::populationLimited($crop_plant_spacing, $crop_bed_rows, $crop_bed_width, $plot_population_average);
					$isLimited = ($populationLimited) ? 'y': 'n';
					$zone_sample_counts[$result->zone_id]->zone_population_limited = $isLimited;
					$zone_sample_counts[$result->zone_id]->zone_interpretation = 'Method To-Do';
					break;
				case 2: // three-leaf stage
				case 3: // five-leaf stage
				case 4:	// bulbing stage					
					$leaf_area_cm_plant_plot = (float)self::GroundCoverPercentToLAI($result->ground_cover_average, $crop_bed_width, $plot_population_average);
					$gc_cm_plant_sqm = (10000*($result->ground_cover_average/100))/$plants_per_sqm;
					$leaf_area_sqcm_plant_sqm = self::GroundCoverCMToLAI($gc_cm_plant_sqm);
					$zone_sample_counts[$result->zone_id]->lai_estimate_cm_plant_sqm = $leaf_area_sqcm_plant_sqm;					
										
					if ($growth_stage_id != 4) {	// leaves are counted at 3-5 leaf stage not bulbing	
						$zone_mean_leaf_number = self::zoneGrowthStageMeanLeafNumber($growth_stage_id,$zone_id);
					} else {
						$zone_mean_leaf_number = null;
					}
					// ### 20171003 ### $zone_target_yield = self::zoneTargetYield($growth_stage_id, $leaf_area_sqcm_plant_sqm, $plants_per_sqm, $paddock_plant_date);																
					$zone_target_yield = self::zoneTargetYield($leaf_area_sqcm_plant_sqm, $zone_mean_leaf_number, $plants_per_sqm, $crop_plant_date);																
										
					$zone_sample_counts[$result->zone_id]->zone_target_yield = number_format($zone_target_yield, 4, '.', '');
					$populationLimited = self::populationLimited($crop_plant_spacing, $crop_bed_rows, 
												$crop_bed_width, $plot_population_average);
					$zone_sample_counts[$result->zone_id]->zone_population_limited_yield = ($populationLimited) ? 'y': 'n';
					$growthLimited = self::isGrowthLimited($growth_stage_id, $leaf_area_sqcm_plant_sqm);												
					$zone_sample_counts[$result->zone_id]->zone_growth_limited_yield = ($growthLimited) ? 'y': 'n';
					$zone_sample_counts[$result->zone_id]->management_action_zone = self::managementActionZone(
												$populationLimited, $growthLimited);
					// Add the yield estimate to the database for retrieval by final harvest report
					self::setZoneYieldEstimateByGrowthStage($farm_id, $paddock_id, $crop_id, $result->zone_id, $growth_stage_id, $zone_target_yield);
					break;
				case 5: // harvest stage				
										
					$zone_sample_counts[$result->zone_id]->zone_three_leaf_yield = number_format(self::getZoneYieldEstimateByGrowthStage(
								$farm_id, $paddock_id, $crop_id, $zone_id, 2), 2, '.', ''); // three-leaf stage
					$zone_sample_counts[$result->zone_id]->zone_five_leaf_yield = number_format(self::getZoneYieldEstimateByGrowthStage(
								$farm_id, $paddock_id, $crop_id, $zone_id, 3), 2, '.', ''); // five-leaf stage
					$zone_sample_counts[$result->zone_id]->zone_bulbing_yield = number_format(self::getZoneYieldEstimateByGrowthStage(
								$farm_id, $paddock_id, $crop_id, $zone_id, 4), 2, '.', ''); // bulbing stage
					$zone_harvest_yield = number_format($zone_sample_weight_kg_sqm*10, 2, '.', ''); // harvest stage
					$zone_sample_counts[$result->zone_id]->zone_harvest_yield = $zone_harvest_yield;
					self::setZoneYieldEstimateByGrowthStage($farm_id, $paddock_id, $crop_id, $result->zone_id, $growth_stage_id, $zone_harvest_yield);
					break;
				default:
					// To-do error trap
			}
			$i++;
        }


		$zone_sample_counts[$result->zone_id]->weighted_sum_three_leaf_yield = number_format(self::getPaddockYieldWeightedSumByGrowthStage(
								$farm_id, $paddock_id, $crop_id, 2), 2, '.', ''); // three-leaf sum								
		$zone_sample_counts[$result->zone_id]->three_leaf_tonnes_hectare = '['.number_format($zone_sample_counts[$result->zone_id]->weighted_sum_three_leaf_yield/$result->paddock_area, 2, '.', '').' t/ha]';
		$zone_sample_counts[$result->zone_id]->weighted_sum_five_leaf_yield = number_format(self::getPaddockYieldWeightedSumByGrowthStage(
								$farm_id, $paddock_id, $crop_id, 3), 2, '.', ''); // five-leaf sum
		$zone_sample_counts[$result->zone_id]->five_leaf_tonnes_hectare = '['.number_format($zone_sample_counts[$result->zone_id]->weighted_sum_five_leaf_yield/$result->paddock_area, 2, '.', '').' t/ha]';								
		$zone_sample_counts[$result->zone_id]->weighted_sum_bulbing_yield = number_format(self::getPaddockYieldWeightedSumByGrowthStage(
								$farm_id, $paddock_id, $crop_id, 4), 2, '.', ''); // bulbing sum
		$zone_sample_counts[$result->zone_id]->bulbing_tonnes_hectare = '['.number_format($zone_sample_counts[$result->zone_id]->weighted_sum_bulbing_yield/$result->paddock_area, 2, '.', '').' t/ha]';									
		$zone_sample_counts[$result->zone_id]->weighted_sum_harvest_yield = number_format(self::getPaddockYieldWeightedSumByGrowthStage(
								$farm_id, $paddock_id, $crop_id, 5), 2, '.', ''); // harvest sum
		$zone_sample_counts[$result->zone_id]->harvest_tonnes_hectare = '['.number_format($zone_sample_counts[$result->zone_id]->weighted_sum_harvest_yield/$result->paddock_area, 2, '.', '').' t/ha]';								
		
        return $zone_sample_counts;
    }
	
	public static function getPaddockYieldWeightedSumByGrowthStage($farm_id, $paddock_id, $crop_id, $growth_stage_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT paddock_area
				FROM paddock
				WHERE farm_id = :farm_id
				AND paddock_id = :paddock_id";
				
        $query = $database->prepare($sql);
		
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

		$paddock_area = 0;
		$paddock_yield = 0;	
		
		if ($query->rowCount() == 1 ) {
			//echo print_r($query->fetch()->yield_estimate, true);
			$paddock_area =  $query->fetch()->paddock_area;
			
			$sql = "SELECT y.yield_estimate, z.zone_paddock_percentage
				FROM yield y
				INNER JOIN zone z ON y.zone_id = z.zone_id
				WHERE z.farm_id = :farm_id
				AND z.paddock_id = :paddock_id
				AND z.crop_id = :crop_id
				AND y.growth_stage_id = :growth_stage_id";
						
			$query = $database->prepare($sql);
			
			$query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id, ':crop_id' => $crop_id, ':growth_stage_id' => $growth_stage_id));
								
			//echo print_r($query->rowCount(), true); 

			foreach ($query->fetchAll() as $result) {
				$paddock_yield+=($result->yield_estimate * ($result->zone_paddock_percentage/100)) * $paddock_area;
			}
		}	

		return $paddock_yield;		
	
	}
	
	public static function zoneInterpretation(){
		
	}
	
	private static function GroundCoverCMToLAI($gc_cm_plant_sqm){
		
		(float)$a = 0.183;		
		(float)$b = 0.0498;
	
		$LAI = pow($a,$b) * (float)$gc_cm_plant_sqm;	
		
		return $LAI;	
	}
	
	/* @
	 * return Leaf Area Index as cm^2 per plant
	 * 
	 */
	public static function GroundCoverPercentToLAI($ground_cover_average, $bed_width, $plot_population_average){
		
		
		
					
		/*		
		echo '<pre>';
		echo var_dump("ground_cover_average=".$ground_cover_average);
		echo var_dump("bed_width=".$bed_width);
		echo var_dump("plot_population_average=".$plot_population_average);		
		echo '<pre>';
		*/
		
		// exponential relationship between EasyLeafArea and Leaf Area Index
		// conversion function information provided by Plant & Food Research
		
		//(float)$GroundCover_f = $ground_cover/100; // percentage
		// groundcover percentage as area/plant in cm^2 
		$plot_width = Config::get('SAMPLE_PLOT_WIDTH');
		//(float)$ground_cover_plant_area_cm = ((10000/($bed_width*$plot_width))*(float)($ground_cover_average/100))/$plot_population_average; 
		(float)$ground_cover_plant_area_cm = (10000*((float)($ground_cover_average/100)))/$plot_population_average;
		(float)$a = 0.183;
		(float)$b = 0.0498;
		
		/*
		echo print_r('bed width='.$bed_width.'</br>', true); 	
		echo print_r('plot count mean='.$plot_population_average.'</br>', true); 
		echo print_r('GC mean %='.$ground_cover_average.'</br>', true); 
		echo print_r('GC cm^2/plant='.$ground_cover_plant_area_cm.'</br>', true); 
		 */
		
		$LAI = pow($a,$b) * (float)$ground_cover_plant_area_cm;			
		//echo print_r($LAI.'<br/>', true); 
		return $LAI;
		
	}	
	
	//public static function zoneTargetYield($growth_stage_id, $zone_lai_estimate, $plot_population_average, $planting_date){

	public static function zoneTargetYield($zone_mean_leaf_area_cm_plant_sqm, $zone_growth_stage_mean_leaf_number, $zone_mean_plants_per_sqm, $planting_date){		
	//public static function zoneTargetYield($zone_mean_leaf_area_cm_plant_sqm, $zone_growth_stage_mean_leaf_number, $zone_mean_plants_per_sqm, $planting_date){		
		
		$min_leaf_area_cm_plant_sqm = 0; // cm^2
		//$max_leaf_area_cm_plant_sqm = 0; // cm^2			
		
		// create associative array with detailed information about a specified date
		$d = date_parse($planting_date);
		//echo print_r($d['month'].'</br>', true);
		
		// cm^2 per plant
		/*
		switch ($growth_stage_id) {
			case 2:	// 3 leaf stage						
			case 3:	// 5 leaf stage					
				$min_leaf_area_cm_plant_sqm = self::minimumLeafArea($zone_growth_stage_mean_leaf_number);
				break;
			case 4:	// bulbing stage					
				$min_leaf_area_cm_plant_sqm = Config::get('MIN_LEAF_AREA_BULBING');
				//$max_leaf_area_cm_plant_sqm = Config::get('MAX_LEAF_AREA_BULBING');			
				break;				
			default:
				// To-do (trap error)
		}
		*/
		// determine minimum leaf area based average leaf number
		if ($zone_growth_stage_mean_leaf_number != null){
			// 3-5 leaf stage
			$min_leaf_area_cm_plant_sqm = self::minimumLeafArea($zone_growth_stage_mean_leaf_number);
		} else {
			// bulbing stage					
			$min_leaf_area_cm_plant_sqm = Config::get('MIN_LEAF_AREA_BULBING');
		}
		
		// if planted before August then use 130 grams else use 117 grams (Plant & Food Research supplied data)
		$minimum_optimal_bulb_weight = $d['month'] < 8 ? Config::get('OPTIMAL_BULB_WEIGHT_GRAMS_PRE_AUGUST') : Config::get('OPTIMAL_BULB_WEIGHT_GRAMS_POST_JULY'); // grams 

		// if planted in August or later then use reduce leaf area by 10% (Plant & Food Research supplied data)
		$min_leaf_area_cm_plant_sqm = $d['month'] < 8 ? $min_leaf_area_cm_plant_sqm : $min_leaf_area_cm_plant_sqm*0.9; // cm^2
		
		//$proportion_estimate = $zone_lai_estimate/$min_leaf_area_sqcm;
		$proportion_estimate = $zone_mean_leaf_area_cm_plant_sqm/$min_leaf_area_cm_plant_sqm;	

		
		//$zone_area_yield_estimate = ($minimum_optimal_bulb_weight*$proportion_estimate)*$plot_population_average*0.01;
		$zone_area_yield_estimate = ($minimum_optimal_bulb_weight*$proportion_estimate)*$zone_mean_plants_per_sqm*0.01;
		
		return $zone_area_yield_estimate;
	}
	
	
	public static function minimumLeafArea($x) {
		
		(float)$C = 98.98;
		(float)$b = 1.0266;
		(float)$m = 5.783;
		$min_leaf_area_cm_plant_sqm = 0;
		
		//echo print_r("x=".$x."</br>", true);
		//$x=3;
		$e = (float)(float)-$b*((float)$x-(float)$m);		
		
		//$min_leaf_area_cm_plant_sqm = $C/1+exp(((float)-$b*((float)$x -(float)$m)));
		$min_leaf_area_cm_plant_sqm = (float)$C/(1+exp((float)$e));
		//echo print_r("e=".$e." min_leafarea=".$min_leaf_area_cm_plant_sqm."</br>", true);
		return $min_leaf_area_cm_plant_sqm;
	}
	
	public static function zoneGrowthStageMeanLeafNumber($growth_stage_id, $zone_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();
		
		$sql = "SELECT mean_leaf_number
				FROM leaf_number
				WHERE growth_stage_id = :growth_stage_id
				AND zone_id = :zone_id";
				
        $query = $database->prepare($sql);
		
        $query->execute(array(':growth_stage_id' => $growth_stage_id, ':zone_id' => $zone_id));
		
		$zone_growth_stage_mean_leaf_number = 0;
		
		if ($query->rowCount() == 1 ) {
			$result = $query->fetch()->mean_leaf_number;
			$zone_growth_stage_mean_leaf_number =  $result;
		}
		
		//echo print_r("zone_id=".$zone_id." growth_stage_id=".$growth_stage_id." mean_leaf_number=".$result."</br>", true);
			
		return $zone_growth_stage_mean_leaf_number;
	}
	
	/*
	 * @ plant_spacing (float) mm
	 * @ $rows_per_bed (integer)
	 * 
	 * 
	 * 
	 * 
	 */ 
	public static function populationLimited($plant_spacing, $rows_per_bed, $bed_width, $plot_population_average){

		
		$plot_width = Config::get('SAMPLE_PLOT_WIDTH');
		$plant_spacing_cm = $plant_spacing/10;
		$average_emergence_percentage = 0.95;

		//$population_variation = 0.28; - deprecated - tightened up variation
		$population_variation = Config::get('POPULATION_LIMITED_PERCENT');
		//$population_variation = 0.15;
		
		$potential_population_per_square_meter = ((100/$plant_spacing_cm)*$rows_per_bed*$average_emergence_percentage)/$bed_width;
		
		$optimal_min = $potential_population_per_square_meter-($population_variation*$potential_population_per_square_meter);
		$optimal_max = $potential_population_per_square_meter+($population_variation*$potential_population_per_square_meter);		
		$actual_population_per_square_meter = self::unitPerSquareMeter($plot_population_average, $bed_width, $plot_width);

		//echo print_r($optimal_min, true); 		
		
		//$result = $actual_population_per_square_meter < $optimal_min ? $true: $false;
		return $actual_population_per_square_meter < $optimal_min ? true: false;		
		//return $result;
		
		
	}
	
public static function isGrowthLimited($growth_stage_id, $leaf_area_sqcm_plant_sqm){
		
		// cm^2 per plant
		switch ($growth_stage_id) {
			case 2:	// 3 leaf stage
				$min_leaf_area_cm_plant_sqm = Config::get('MIN_LEAF_AREA_3LEAF');
				break;
			case 3:	// 5 leaf stage
				$min_leaf_area_cm_plant_sqm  = Config::get('MIN_LEAF_AREA_5LEAF');
				break;
			case 4:	// bulbing stage
				$min_leaf_area_cm_plant_sqm = Config::get('MIN_LEAF_AREA_BULBING');	
				break;				
			default:
				// To-do (trap error)
		}	
		return $leaf_area_sqcm_plant_sqm < $min_leaf_area_cm_plant_sqm ? true: false;		
	}
	
	/* deprecated
	public static function growthLimited($growth_stage_id, $paddock_bed_width, $lai_estimate_cm_plant_plot, $plot_population_average){
		
		$plot_width = Config::get('SAMPLE_PLOT_WIDTH');
		$plot_area = $paddock_bed_width * $plot_width; // sample plots all plot width x bed width; result m^2
		//$plot_lai = $plot_area * $zone_lai_estimate_percent; // percentage
		
		//$estimated_plant_leaf_area_index = (($plot_area/$plot_population_average)*$lai_estimate_cm_plant_plot)*10000; //cm^2
		
		$estimated_leaf_area_cm_plant = (($plot_area/$plot_population_average)*$lai_estimate_cm_plant_plot)*10000; //cm^2
		
		//echo print_r($lai_estimate_cm_plant_plot.' '.$estimated_leaf_area_cm_plant.'<br/>' , true);
		//echo print_r($lai_estimate_cm_plant_plot.' '.$estimated_leaf_area_cm_plant.'<br/>' , true);  
		//$estimated_leaf_area_cm_plant
		//echo print_r($lai_estimate_cm_plant_plot.' ', true); 
	
		// cm^2 per plant
		switch ($growth_stage_id) {
			case 2:	// 3 leaf stage				
				//$min_leaf_area_index = 5;
				$min_leaf_area_cm_plant = 5;
				break;
			case 3:	// 5 leaf stage					
				//$min_leaf_area_index = 30;
				$min_leaf_area_cm_plant  = 30;
				break;
			case 4:	// bulbing stage					
				//$min_leaf_area_index = 60;						
				$min_leaf_area_cm_plant = 60;	
				break;				
			default:
				// To-do (trap error)
		}	
		
		//$result = $estimated_plant_leaf_area_index < $min_leaf_area_index ? $true: $false;
		//return $estimated_leaf_area_cm_plant < $min_leaf_area_cm_plant ? true: false;	
			return $lai_estimate_cm_plant_plot < $min_leaf_area_cm_plant ? true: false;
		//return $result;
	}
	*/
	
	/* Determine how many per square meter
	 * $i = population/weight: type=integer
	 * $x = length (meters): type=float
	 * $y = width (meters): type=float
	 * @return $per_sqm: type=float
	 */
	public static function unitPerSquareMeter($i, $x, $y){		
		if ($x != 0 && $y != 0){			
			return $per_sqm = (1/($x*$y))*$i;		
		}
		return 0;
	}

	/* deprecated and name change (plantsPerSquareMeter => unitPerSquareMeter)
	 * Now using variable plot_width parameter
	public static function plantsPerSquareMeter($plot_population_average, $bed_width){
	
		// sample plots are presumed to be 0.5m wide x bed width
		
		// updated fixed plot size to variable configuration setting
		$plot_width = Config::get('SAMPLE_PLOT_WIDTH');
		
		$population_per_square_meter = ($plot_population_average/$bed_width)*2;
		return $population_per_square_meter;	
		
	}
	*/
	public static function managementActionZone($populationLimited, $growthLimited){
		
		// optimal 
		if (!$populationLimited && !$growthLimited){
			$management_action_zone = 1;
		// growth limited 
		} elseif ($populationLimited && !$growthLimited){ 
			$management_action_zone = 2;
		// population limited
		} elseif (!$populationLimited && $growthLimited){
			$management_action_zone = 3;
		// population and growth limited
		} elseif ($populationLimited && $growthLimited){ 
			$management_action_zone = 4;
		}
		
		return $management_action_zone;
	}
	

	
	public static function zoneSampleWeightInKGs($weight_in_grams, $plant_count){
	
		$weight_in_kilograms = ($weight_in_grams*$plant_count)/1000;
		return $weight_in_kilograms;
	}

	public static function zonePopulationDifference($target, $estimate){
		
		$change = $estimate-$target;
		$percent_change = round(($change/$target)*100,2);
		
		return $percent_change;
	}
	
	public static function zonePopulationEstimate($sample_average, $zone_area, $paddock_bed_width){
		
		$zonePopulationEstimate = ($sample_average/$paddock_bed_width)*10000*$zone_area;
		
		return $zonePopulationEstimate;
	}
	
	
	public static function getZoneDetailsByID($farm_id, $paddock_id, $crop_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT zone_id, zone_name, zone_paddock_percentage FROM zone WHERE farm_id = :farm_id AND paddock_id = :paddock_id AND crop_id = :crop_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id, ':crop_id' => $crop_id));

        $zone_details = array();

        foreach ($query->fetchAll() as $result) {
	
			$zone_details[$result->zone_id] = new stdClass();
            $zone_details[$result->zone_id]->zone_id = $result->zone_id;
            $zone_details[$result->zone_id]->zone_name = $result->zone_name;			
            $zone_details[$result->zone_id]->zone_paddock_percentage = $result->zone_paddock_percentage;	
        }	

		return $zone_details;
	}
	
	public static function getZoneSampleCountsByID($farm_id, $paddock_id, $crop_id, $growth_stage_id){
		
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT sample_id, sample_count, zone_id 
				FROM sample 
				WHERE 
				farm_id = :farm_id AND 
				paddock_id = :paddock_id AND
				crop_id = :crop_id AND				
				growth_stage_id = :growth_stage_id";	
		
        $query = $database->prepare($sql);
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id, ':crop_id' => $crop_id, ':growth_stage_id' => $growth_stage_id));

        $zone_sample_details = array();

        foreach ($query->fetchAll() as $result) {	
			$zone_sample_details[$result->zone_id] = new stdClass();
            $zone_sample_details[$result->zone_id]->zone_id = $result->zone_id;			
            $zone_sample_details[$result->zone_id]->sample_id = $result->sample_id;
            $zone_sample_details[$result->zone_id]->sample_count = $result->sample_count;	
        }	

		return $zone_sample_details;		
	}
	
	private static function zoneYieldEstimatesExist($farm_id, $paddock_id, $crop_id, $zone_id, $growth_stage_id){
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT * FROM `yield` 
				WHERE 
				farm_id = :farm_id AND 
				paddock_id = :paddock_id AND
				crop_id = :crop_id AND
				zone_id = :zone_id AND				
				growth_stage_id = :growth_stage_id";
			
		$query = $database->prepare($sql);

		try{
			$query->execute(array(
				':farm_id' => $farm_id,
				':paddock_id' => $paddock_id,
				':crop_id' => $crop_id,
				':zone_id' => $zone_id,
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
	
	public static function setZoneYieldEstimateByGrowthStage($farm_id, $paddock_id, $crop_id, $zone_id, $growth_stage_id, $yield_estimate){
		$database = DatabaseFactory::getFactory()->getConnection();
		
		if ($yield_estimate > 0 ) {		
			// add the data
			$sql = "REPLACE INTO yield 
				(farm_id, paddock_id, crop_id, zone_id, growth_stage_id, yield_estimate) 
				VALUES 
				(:farm_id, :paddock_id, :crop_id, :zone_id, :growth_stage_id, :yield_estimate)";
		} else {
			// initialise the values with zeros (used for charting reports)
			$sql = "INSERT INTO yield 
				(farm_id, paddock_id, crop_id, zone_id, growth_stage_id, yield_estimate) 
				VALUES 
				(:farm_id, :paddock_id, :crop_id, :zone_id, :growth_stage_id, :yield_estimate)
						ON DUPLICATE KEY UPDATE yield_estimate = VALUES(yield_estimate)";			
		}
		/*
        $sql = "INSERT INTO yield 
		(farm_id, paddock_id, zone_id, growth_stage_id, yield_estimate) 
		VALUES 
		(:farm_id, :paddock_id, :zone_id, :growth_stage_id, :yield_estimate)
				ON DUPLICATE KEY UPDATE yield_estimate = VALUES(yield_estimate)";				
		*/
		//echo print_r("zone yield: ".$yield_estimate.'<br/>', true);
        $query = $database->prepare($sql);

		try{
			$query->execute(array(
				':farm_id' => $farm_id,
				':paddock_id' => $paddock_id,
				':crop_id' => $crop_id,
				':zone_id' => $zone_id,
				':growth_stage_id' => $growth_stage_id,
				':yield_estimate' => $yield_estimate
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
	
	public static function getZoneYieldEstimateByGrowthStage($farm_id, $paddock_id, $crop_id, $zone_id, $growth_stage_id){

		
		$database = DatabaseFactory::getFactory()->getConnection();
		
        $sql = "SELECT yield_estimate 
				FROM `yield` 
				WHERE 
				farm_id = :farm_id AND 
				paddock_id = :paddock_id AND
				crop_id = :crop_id AND
				zone_id = :zone_id AND				
				growth_stage_id = :growth_stage_id";
		
        $query = $database->prepare($sql);
		
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id,
							':crop_id' => $crop_id, ':zone_id' => $zone_id, 
							':growth_stage_id' => $growth_stage_id));
							
		//echo print_r($query->rowCount(), true); 
		
		if ($query->rowCount() == 1 ) {
			//echo print_r($query->fetch()->yield_estimate, true);
			return $query->fetch()->yield_estimate;
		}
	}
	
	
	public static function getPaddockYieldSummary($farm_id, $paddock_id, $crop_id, $target_paddock_tonnes_hectare){
/*		
		$paddock = new Paddock($farm_id, $paddock_id);
		
		$target_paddock_yield = DatabaseCommon::getTargetPaddockYield($paddock->paddock_target_population, $paddock->paddock_plant_date);
		$target_paddock_tonnes_hectare = round(DatabaseCommon::getTargetPaddockTonnesHectare($target_paddock_yield, $paddock->paddock_area));
		
		$paddock = null;
*/		
		
		$database = DatabaseFactory::getFactory()->getConnection();	
		
		
		/*
		// to build the sql query we need to determine if the paddock has any 
		// records for the growth stage in question 
		// this should not be required any longer as default zero (0) values 
		// are inserted into the yield table when the assessment is first run
		
		$sql = "SELECT growth_stage_id 
				FROM yield
				WHERE farm_id = :farm_id AND paddock_id = :paddock_id
				GROUP BY growth_stage_id";
				
        $query = $database->prepare($sql);		
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

		$sql = "";
		$union_all_string = " UNION ALL ";
		
		$gs_yield_data = $query->rowCount();
		
		while ( $gs = $query->fetchObject() ) {
			$sql .= "(SELECT z.zone_name, y.growth_stage_id,y.yield_estimate 
				FROM yield y INNER JOIN zone z ON y.zone_id=z.zone_id 
				WHERE y.farm_id = :farm_id AND y.paddock_id = :paddock_id 
				AND y.growth_stage_id = ".(int)$gs->growth_stage_id." ORDER BY z.zone_id)".$union_all_string;				
		}
		*/
		// Build our query string
		$sql = "";
		$union_all_string = " UNION ALL ";
		for ($i=2;$i<=5;$i++){
			$sql .= "(SELECT z.zone_name, y.growth_stage_id,y.yield_estimate 
				FROM yield y INNER JOIN zone z ON y.zone_id=z.zone_id 
				WHERE y.farm_id = :farm_id AND y.paddock_id = :paddock_id 
				AND y.crop_id = :crop_id AND y.growth_stage_id = ".$i." ORDER BY z.zone_id)".$union_all_string;				
		}		
		// remove the trailing UNION ALL string		
		$sql = substr_replace($sql, "", strlen($sql)-strlen($union_all_string), strlen($union_all_string));		
		
		/*
		$sql = "(SELECT z.zone_name, y.growth_stage_id,y.yield_estimate 
			FROM yield y INNER JOIN zone z ON y.zone_id=z.zone_id 
			WHERE y.farm_id = :farm_id AND y.paddock_id = :paddock_id AND y.growth_stage_id = 2
			ORDER BY z.zone_id)
				UNION ALL
				(SELECT z.zone_name, y.growth_stage_id,y.yield_estimate 
			FROM yield y INNER JOIN zone z ON y.zone_id=z.zone_id 
			WHERE y.farm_id = :farm_id AND y.paddock_id = :paddock_id AND y.growth_stage_id = 3
			ORDER BY z.zone_id)
				UNION ALL
				(SELECT z.zone_name, y.growth_stage_id,y.yield_estimate 
			FROM yield y INNER JOIN zone z ON y.zone_id=z.zone_id 
			WHERE y.farm_id = :farm_id AND y.paddock_id = :paddock_id AND y.growth_stage_id = 4
			ORDER BY z.zone_id)
				UNION ALL
				(SELECT z.zone_name, y.growth_stage_id,y.yield_estimate 
			FROM yield y INNER JOIN zone z ON y.zone_id=z.zone_id 
			WHERE y.farm_id = :farm_id AND y.paddock_id = :paddock_id AND y.growth_stage_id = 5
			ORDER BY z.zone_id)";
		*/
		
		
        $query = $database->prepare($sql);		
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id, ':crop_id' => $crop_id));

		//$zone_count = $query->rowCount()/$gs_yield_data; // four growth stages normally
		$zone_count = $query->rowCount()/4; // four growth stages currently
		$c = 1; // column for growth stage related yield estimate 
		$r = 0; // row for zone name
		while ( $zone = $query->fetchObject() ) {
			$data[$r+1][0] = $zone->zone_name;
			$data[$r+1][$c] = (float)$zone->yield_estimate;	
			$data[$r+1][$c+1] = (int)$target_paddock_tonnes_hectare;
			$r++;
			if ($r === $zone_count) {	
				// move to the next growth stage group
				$r = 0;
				$c++;				
			}
		}
		return $data;		
	}
	
	private static function addRecordData(){
		return $data;
	}
	
	public static function getPaddockYieldEstimateByGrowthStage($farm_id, $paddock_id, $crop_id, $growth_stage_id, $r = 1, $target_tonnes_hectare){

/*		
		$paddock = new Paddock($farm_id, $paddock_id);
		
		$target_paddock_yield = DatabaseCommon::getTargetPaddockYield($paddock->paddock_target_population, $paddock->paddock_plant_date);
		$target_paddock_tonnes_hectare = round(DatabaseCommon::getTargetPaddockTonnesHectare($target_paddock_yield, $paddock->paddock_area));
		
		$paddock = null;
*/		
		
		$database = DatabaseFactory::getFactory()->getConnection();
			
		$sql = "SELECT 
					z.zone_name, y.yield_estimate 
				FROM 
					yield y
				INNER JOIN 
					zone z
				ON 
					y.zone_id=z.zone_id
				WHERE 
					y.farm_id = :farm_id AND 
					y.paddock_id = :paddock_id AND
					y.crop_id = :crop_id AND
					y.growth_stage_id = :growth_stage_id";
		
        $query = $database->prepare($sql);		
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id, ':crop_id' => $crop_id, ':growth_stage_id' => $growth_stage_id));							
		
		$data = array();

		while ( $row = $query->fetchObject() ) {			
			$data[$r][0] = $row->zone_name;
			$data[$r][1] = (float)$row->yield_estimate;
			$data[$r][2] = (int)$target_tonnes_hectare;
			$r++;
		}
		
/*
		echo '<pre>';
		echo var_dump($data);
		echo '<pre>';
*/		
		return $data;
	}

	public static function getMeanPopulationByZone($farm_id, $paddock_id, $crop_bed_width, $target_plants_sqm){

/*		
		$sql = "SELECT paddock_bed_width,paddock_target_population,paddock_area FROM paddock
			WHERE paddock_id =:paddock_id";
		
		$rs = self::getPaddockPropertiesByID($sql, $paddock_id);	
		
		$paddock_bed_width = (float)$rs->paddock_bed_width;
		$target_paddock_plants_sqm = round($rs->paddock_target_population/($rs->paddock_area*10000));
*/		
		$plot_width = Config::get('SAMPLE_PLOT_WIDTH');
		
		$database = DatabaseFactory::getFactory()->getConnection();
			
		$sql = "SELECT 
					z.zone_name, AVG(s.sample_count) as mean_population 
				FROM 
					sample s
				INNER JOIN 
					zone z
				ON 
					s.zone_id=z.zone_id					
				WHERE 
					s.growth_stage_id = 1 AND 
					s.farm_id= :farm_id AND 
					s.paddock_id = :paddock_id 
				GROUP BY s.zone_id
				ORDER BY s.zone_id";
		
        $query = $database->prepare($sql);		
        $query->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id));

		$data = array();
		$r = 1;

		while ( $row = $query->fetchObject() ) {			
			$data[$r][0] = $row->zone_name;			
			$plants_per_sqm = self::unitPerSquareMeter((float)$row->mean_population, $crop_bed_width, $plot_width);
			$data[$r][1] = round($plants_per_sqm, 2);
			$data[$r][2] = $target_plants_sqm;
			$r++;
		}
/*
		echo '<pre>';
		echo var_dump($data);
		echo '<pre>';
*/		
		return $data;
	}	
	
	
	
	public static function savePDF($report_name){
        
        // get the payload
		$html = self::formatForOutput();
		
        require_once '../vendor/dompdf/dompdf_config.inc.php';
        // define output file 
        $file_name = uniqid(ucwords($report_name).'_Assessment_').'.pdf';
        // initialise the PDF object
        $dompdf = new DOMPDF();  
        $dompdf->load_html($html);
        $dompdf->render();
        // output to file handle
        $dompdf->stream($file_name);
        //Session::add('feedback_positive', Text::get('FEEDBACK_PDF_CREATION_SUCCESSFUL'));		
		
		return true;
		
    }
	
	public static function assessmentReportHeader($report_name){
		
		$state = null;
		if(Session::get("user_account_type") == 1){ 
			$state = 'disabled style="background-color: transparent;"';
		}
		
		$head = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
				<script src="'.Config::get('URL').'scripts/charting_plots.js"></script>
				<div class="container">
					<form method="post" action="'.Config::get('URL').'reports/assessment_action">
					<h1 class="responsive" data-compression="20" data-min="8" data-max="24">'.ucwords($report_name).' Assessment<span style="float:right;"><input type="submit" value="Save As PDF" name="assessment" id="save_pdf_image" title="Save PDF" class="imgPDF" /></span>
						<span style="float:right;"><input type="submit" value="Export To CSV" name="assessment" id="export_csv_image" title="Export CSV" class="imgCSV" /></span>
						<span style="float:right;"><input type="submit" value="Send Via Email" name="assessment" id="send_email_image" title="Email Report" class="imgEmail" '.$state.'/></span>
					</h1>
				<div class="report-box">';
				
		return $head;
	}
	
	public static function assessmentReportHidden($data){
		
		$hidden = '<input type="hidden" name="report_name" value="'.$data->report_name.'">
				<input type="hidden" name="farm_id" value="'.$data->farm_id.'">
				<input type="hidden" name="paddock_id" value="'.$data->paddock_id.'">
				<input type="hidden" name="crop_id" value="'.$data->crop_id.'">
				<input type="hidden" id="growth_stage_id" name="growth_stage_id" value="'.$data->growth_stage_id.'">
				<input type="hidden" id="chartData" name="chartData" value=\''.$data->chartData.'\'>
				<input type="hidden" id="chartAverages" name="chartAverages" value=\''.$data->chartAverages.'\'>
				<input type="hidden" id="hAxisTitle" name="hAxisTitle" value=\''.$data->hAxisTitle.'\'>
				<input type="hidden" id="gridLines" name="gridLines" value=\''.$data->gridLines.'\'>
				<input type="hidden" id="vAxisTitle" name="vAxisTitle" value=\''.$data->vAxisTitle.'\'>
				<input type="hidden" id="vAxisMin" name="vAxisMin" value=\''.$data->vAxisMin.'\'>
				<input type="hidden" id="vAxisMax" name="vAxisMax" value=\''.$data->vAxisMax.'\'>
				<input type="hidden" id="chartURI" name="chartURI" value="">
				<input type="hidden" id="chartSummaryURI" name="chartSummaryURI" value="">';
		
		return $hidden;
	}
	
	
	public static function assessmentBottomNavigation(){
		
		if(Session::get("user_account_type") == 1){ 
			$disabled = 'disabled style="background-color: transparent;"'; 
		} else {
			$disabled = null;
		}
		// return page
		$url = Config::get("URL")."reports";
		
		$navigation = '<div class="container_bottom_navigation">
			<!-- Return to assessment selection page -->
			<div class="app-button" style="margin:2px 0; padding:0;">                
				<a class="buttonBack" href="'.$url.'">Back</a>
			</div>			
		</div>
	</div>
	</form>
</div>';
		
		return $navigation;
	}
	
	public static function buildHTMLReporttable($farm_id, $paddock_id, $crop_id, $growth_stage_id){
		
		$html = self::formatForOutput($farm_id, $paddock_id, $crop_id, $growth_stage_id);
		
		return $html;
	}
	
	public static function getChartDataByPlots($farm_id, $paddock_id, $growth_stage_id){
	
		/* At the specific growth stage get all samples for each zone; */
		$database = DatabaseFactory::getFactory()->getConnection();		

		$sql = "SELECT zone_id, zone_name FROM zone WHERE paddock_id = :paddock_id AND	farm_id = :farm_id ORDER BY zone_id";		
			
	    $cols = $database->prepare($sql);
        $cols->execute(array(':paddock_id' => $paddock_id, ':farm_id' =>$farm_id));
		
		$data = array();
		$data[0][0] = 'Plot#';
		$max = 0; // used for setting vAxisMax in Google charts
		$min = 1000; // used for setting vAxisMin in Google charts
		$c =1; // starting column for data
		while ( $col = $cols->fetchObject() ) {	
			// set the row back to start to add new column header (zone_name)
			$r = 0;			 
			// add columns titles 
			$data[$r][$c] = $col->zone_name;
			// get zone_id to populate column sample data
			//$zone_id = $col->zone_id;
			$sql = "SELECT zone_sample_plot_id, sample_count, sample_ela_score, sample_bulb_weight
			FROM sample
			WHERE				
			growth_stage_id = :growth_stage_id AND			
			paddock_id = :paddock_id AND				
			farm_id = :farm_id AND
			zone_id = :zone_id
			ORDER BY 
			zone_sample_plot_id";
			
			$rows = $database->prepare($sql);
			$rows->execute(array(':growth_stage_id' => $growth_stage_id,
							':paddock_id' => $paddock_id,
							':farm_id' => $farm_id, 
							':zone_id' => $col->zone_id));
			// next row for data
			$r = 1;
			while ( $row = $rows->fetchObject() ) {	
				// add plot zone sample ids (y-axis)
				// not most efficient as overwriting each zone pass
				Session::set('gridLines', $rows->rowCount());
				$data[$r][0] = (int)$row->zone_sample_plot_id;
				switch ($growth_stage_id){
					case 1:	
						$max = $max < $row->sample_count ? $row->sample_count : $max;				
						$min = ($min > $row->sample_count) ? $row->sample_count : $min;						
						$data[$r][$c] = (int)$row->sample_count;						
						break;
					case 2:
					case 3:
					case 4:
						$max = $max < $row->sample_ela_score ? $row->sample_ela_score : $max;				
						$min = ($min > $row->sample_ela_score) ? $row->sample_ela_score : $min;
						$data[$r][$c] = (float)$row->sample_ela_score;						
						break;
					case 5:
						$max = $max < $row->sample_bulb_weight ? $row->sample_bulb_weight : $max;				
						$min = ($min > $row->sample_bulb_weight) ? $row->sample_bulb_weight : $min;
						$data[$r][$c] = (float)$row->sample_bulb_weight;
						break;
					default:
				}				
				// move to next row
				$r++;				
			}
			// move to next column (rinse & repeat)
			$c++;			
		}
		switch ($growth_stage_id){
			case 1:	
			case 5:
				$min = $min-5 < 0 ? 0 : $min-5;
				$max = $max + 5;
				break;
			case 2:	
			case 3:
			case 4:
				$min = $min-1 < 0 ? 0 : $min-1;
				$max = $max + 1;
				break;
			default:
		}
		
		// Set vAxisMin and Max values for use with Google charts		
		Session::set('vAxisMin', $min);
		Session::set('vAxisMax', $max);			
		/*
		echo '<pre>';
		echo var_dump(json_encode($data));
		echo '<pre>';	 
		*/
		return json_encode($data);	
	}

	/* todo */
	private static function getChartTitleRowByZones($farm_id, $paddock_id, $crop_id){
		
		$title = array();		
		$title[0][0] = "Zone";
		
		/* At the specific growth stage get all samples for each zone; */
		$database = DatabaseFactory::getFactory()->getConnection();			
		
		$sql = "SELECT crop_zone_count, crop_zone_sample_count
				FROM crop
				WHERE farm_id = :farm_id AND paddock_id = :paddock_id AND crop_id = :crop_id";
		
		$p = $database->prepare($sql);
		$p->execute(array(':farm_id' => $farm_id, ':paddock_id' => $paddock_id, ':crop_id' => $crop_id));
		$row = $p->fetch();
		$crop_zone_sample_count = $row->crop_zone_sample_count;
		$crop_zone_count = $row->crop_zone_count;
		// Option used by Google Charts
		Session::set('gridLines', $crop_zone_count);

		for ($i=0;$i<(int)$crop_zone_sample_count;$i++){
			$title[0][$i+1] = 'Plot#'.(string)($i+1);
		}		
		return $title;
	}

	

	private static function getChartTitleRowByGrowthStage($target_tonnes_hectare){
		
		$title = array();		
		$title[0][0] = "Zone";	
		
		$database = DatabaseFactory::getFactory()->getConnection();			
		/* Get the name for growth stages > emergence
		i.e. the ones that have yield estimates available */
		$sql = "SELECT growth_stage_name
				FROM growth_stage
				WHERE growth_stage_id > 1";
		
		$stmt = $database->prepare($sql);
		$stmt->execute();
		$i = 1;
		while ($row = $stmt->fetch()) {			
			$title[0][$i] = ucwords($row->growth_stage_name);
			$i++;
		}
		// add paddock target column title
		$title[0][$i] = "Target (".$target_tonnes_hectare." t/ha)";
		return $title;
	}		
	
	public static function getChartMeanYieldByZones($farm_id, $paddock_id, $crop_id, $growth_stage_id){
		
		$title = array();
		$data = array();
		
		switch ((int)$growth_stage_id){
			case 1:
			
				$sql = "SELECT paddock_area FROM paddock
					WHERE paddock_id =:paddock_id";
				
				$rs = self::getPaddockPropertiesByID($sql, $paddock_id);	
				
				$paddock_area = (float)$rs->paddock_area;
				
				$sql = "SELECT crop_bed_width,crop_target_population FROM crop
					WHERE crop_id =:crop_id";
				
				$rs = self::getCropPropertiesByID($sql, $crop_id);	
				
				$target_plants_sqm = round($rs->crop_target_population/($paddock_area*10000));			
			
				$title[0][0] = "Zone Name";
				$title[0][1] = "Population";
				$title[0][2] = "Target (".$target_plants_sqm."/sqm)";
				// Todo 
				$data = self::getMeanPopulationByZone($farm_id, $paddock_id, $rs->crop_bed_width, $target_plants_sqm);
				break;			
			case 2:
			case 3:
			case 4:
			
				//$paddock = new Paddock($farm_id, $paddock_id);
				//$target_paddock_yield = DatabaseCommon::getTargetPaddockYield($paddock->paddock_target_population, $paddock->paddock_plant_date);
				//$target_paddock_tonnes_hectare = round(DatabaseCommon::getTargetPaddockTonnesHectare($target_paddock_yield, $paddock->paddock_area));
				//$paddock = null;
				
				$crop = new Crop($farm_id, $paddock_id, $crop_id);
		
				$target_yield = DatabaseCommon::getTargetYield($crop->crop_target_population, $crop->crop_plant_date);
				$target_tonnes_hectare = round(DatabaseCommon::getTargetTonnesHectare($target_yield, $crop->paddock_area));
				
				$crop = null;

			
				$title[0][0] = "Zone Name";
				$title[0][1] = "Estimated Yield";
				$title[0][2] = "Target (".$target_tonnes_hectare." t/ha)";
				$data = self::getPaddockYieldEstimateByGrowthStage($farm_id, $paddock_id, $crop_id, $growth_stage_id, 1, $target_tonnes_hectare);
			
				break;
			case 5:
				$paddock = new Paddock($farm_id, $paddock_id);
				$crop = new Crop($farm_id, $paddock_id, $crop_id);
				$target_yield = DatabaseCommon::getTargetYield($crop->crop_target_population, $crop->crop_plant_date);
				$target_tonnes_hectare = round(DatabaseCommon::getTargetTonnesHectare($target_yield, $paddock->paddock_area));				
				$paddock = null;
			
				$title = self::getChartTitleRowByGrowthStage($target_tonnes_hectare);
				$data = self::getPaddockYieldSummary($farm_id, $paddock_id, $crop_id, $target_tonnes_hectare);
				break;
			default:
		}	
		/*
		if ((int)$growth_stage_id === 5) {		
			$title = self::getChartTitleRowByGrowthStage();
			$data = self::getPaddockYieldSummary($farm_id, $paddock_id);
		} else {
			$title[0][0] = "Zone Name";
			$title[0][1] = "Estimated Yield";
			$data = self::getPaddockYieldEstimateByGrowthStage($farm_id, $paddock_id, $growth_stage_id, 1);
		}		
		*/
		// merge title and data rows
		$data = $title+$data;
		
		/*		
		echo '<pre>';
		echo var_dump($data);
		echo '<pre>';
		*/
		return json_encode($data);
	}

	
	public static function getChartDataByZones($farm_id, $paddock_id, $crop_id, $growth_stage_id){
		
		$max = 0; // used for setting vAxisMax in Google charts
		$min = 1000; // used for setting vAxisMin in Google charts

		$data = array();
		
		$data = self::getChartTitleRowByZones($farm_id, $paddock_id, $crop_id);
		
		// initilize row and column pointers
		$r = 0;
		$c = 0;
	
		/* At the specific growth stage get all samples for each zone; */		
		$database = DatabaseFactory::getFactory()->getConnection();			
		
		// for consistency sample counts have been changed from plants/plot to plants/sqm
		$sql = "SELECT * FROM crop
			WHERE crop_id =:crop_id";
			

		$rs = self::getCropPropertiesByID($sql, $crop_id);	
		
		$crop_bed_width = (float)$rs->crop_bed_width;
		$plot_width = Config::get('SAMPLE_PLOT_WIDTH');	
		
		$sql = "SELECT 
				zone.zone_id, zone.zone_name, sample.zone_sample_plot_id,sample.sample_count,
				sample.sample_ela_score, sample.sample_bulb_weight
			FROM 
				zone
			JOIN 
				sample
			ON 
				zone.zone_id = sample.zone_id
			WHERE				
				sample.growth_stage_id = :growth_stage_id AND
				sample.crop_id = :crop_id AND			
				sample.paddock_id = :paddock_id AND				
				sample.farm_id = :farm_id				
			ORDER BY
				zone.zone_id,
				sample.zone_sample_plot_id";
		
		$plots = $database->prepare($sql);
		$plots->execute(array(':growth_stage_id' => $growth_stage_id,
							':crop_id' => $crop_id,
							':paddock_id' => $paddock_id,
							':farm_id' => $farm_id));
		// initialise zone_id comparator
		$id = 0;
		while ( $plot = $plots->fetchObject() ) {
			// for each row check the zone_id to ensure we're still dealing with the same zone
			$zone_id = $plot->zone_id;
			if ($id !== (int)$zone_id) {				
				// have changed zones so start new row 
				$r++;
				$id = (int)$zone_id;
				// reset column position and add zone name
				//$c = 0;
				$data[$r][$c=0] = $plot->zone_name;
				$c++;
			}

			
			switch ($growth_stage_id){
				case 1:	
					$sample_plants_plot = (int)$plot->sample_count;
					$sample_plants_sqm = self::unitPerSquareMeter($sample_plants_plot, $crop_bed_width, $plot_width);
/*					$max = $max < $plot->sample_count ? $plot->sample_count : $max;				
					$min = ($min > $plot->sample_count) ? $plot->sample_count : $min;						
					$data[$r][$c] = (int)$plot->sample_count;
*/					$max = $max < $sample_plants_sqm ? $sample_plants_sqm : $max;				
					$min = ($min > $sample_plants_sqm) ? $sample_plants_sqm : $min;						
					$data[$r][$c] = round((float)$sample_plants_sqm, 2);		
					break;
				case 2:
				case 3:
				case 4:
					$max = $max < $plot->sample_ela_score ? $plot->sample_ela_score : $max;				
					$min = ($min > $plot->sample_ela_score) ? $plot->sample_ela_score : $min;
					$data[$r][$c] = (float)$plot->sample_ela_score;						
					break;
				case 5:
					$sample_bulb_weight = (float)$plot->sample_bulb_weight; //kgs ?
					// Determine yield data (t/ha)
					$sample_weight_kg_sqm = self::unitPerSquareMeter($sample_bulb_weight, $crop_bed_width, $plot_width);
					//$sample_yield_tonnes_hectare = number_format($sample_weight_kg_sqm*10, 2, '.', ''); // harvest stage
					$sample_yield_tonnes_hectare = $sample_weight_kg_sqm*10;

					$max = $max < $sample_yield_tonnes_hectare ? $sample_yield_tonnes_hectare : $max;				
					$min = ($min > $sample_yield_tonnes_hectare) ? $sample_yield_tonnes_hectare : $min;

					$data[$r][$c] = (float)round($sample_yield_tonnes_hectare,2);
				
					
					/*
					// Display bulb weight in kgs
					$max = $max < $plot->sample_bulb_weight ? $plot->sample_bulb_weight : $max;				
					$min = ($min > $plot->sample_bulb_weight) ? $plot->sample_bulb_weight : $min;
					$data[$r][$c] = (float)$plot->sample_bulb_weight;
					*/
					
					break;
				default:
			}
			// move to next column and repeat
			$c++;
		}
		switch ($growth_stage_id){
			case 1:	
			case 5:
				$min = $min-5 < 0 ? 0 : $min-5;
				$max = $max + 5;
				break;
			case 2:	
			case 3:
			case 4:
				$min = $min-1 < 0 ? 0 : $min-1;
				$max = $max + 1;
				break;
			default:
		}
		
		// Set vAxisMin and Max values for use with Google charts		
		Session::set('vAxisMin', $min);
		Session::set('vAxisMax', $max);			
		
		/*
		echo '<pre>';
		echo var_dump($data);
		echo '<pre>';
		
		/*
		echo '<pre>';
		echo var_dump(json_encode($data));
		echo '<pre>';	
		*/
		/*
		echo '<pre>';
		echo json_encode($data);
		echo '<pre>';
		*/
		return json_encode($data);
		
	}	
	
	public static function gethAxisTitle($growth_stage_id){
		switch ($growth_stage_id){
			case 1:
			case 2:
			case 3:
			case 4:						
			case 5:
				return '"Management Zone"';			
				break;
			default:
		}
	}
	
	public static function getvAxisTitle($growth_stage_id){
		switch ($growth_stage_id){
			case 1:
				return '"Population"';
				break;
			case 2:
			case 3:
			case 4:	
				return '"Groundcover %"';
				break;
			case 5:
				return '"Estimated Yield (t/ha)"';			
				//return '"Bulb Weight (kg)"';
				break;
			default:
		}		
	}

	private static function getPaddockPropertiesByID($sql, $paddock_id){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        $stmt->execute(array(':paddock_id' => $paddock_id));
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();         
    } 

	private static function getCropPropertiesByID($sql, $crop_id){
        
        $database = DatabaseFactory::getFactory()->getConnection();
        $stmt = $database->prepare($sql);
        $stmt->execute(array(':crop_id' => $crop_id));
        
        // fetch() is the PDO method that gets a single result
        return $stmt->fetch();         
    } 		
	
	private static function formatForOutput($farm_id=null, $paddock_id=null, $crop_id=null, $growth_stage_id=null, $sendAsEmail=null, $footer=null){
        
		if(isset($farm_id) && isset($paddock_id) && isset($crop_id) && isset($growth_stage_id)){
			$objReport = new Report($farm_id, $paddock_id, $crop_id, $growth_stage_id);
			
			$html = $objReport->HTMLReport_farmInfo().
					$objReport->HTMLReport_table(true).
					$objReport->HTMLReport_charts().					
					$objReport->HTMLReport_data(true);
		
		// create PDF or send email report
		} else {
			//$objReport = new Report($report_name, Request::post('farm_id'), Request::post('paddock_id'), Request::post('growth_stage_id'));
			
			$farm_id = Request::post('farm_id');
			$paddock_id = Request::post('paddock_id');
			$crop_id = Request::post('crop_id');
			$growth_stage_id = Request::post('growth_stage_id');
			
			//$objReport = new Report(Request::post('farm_id'), Request::post('paddock_id'), Request::post('growth_stage_id'));
			$objReport = new Report($farm_id, $paddock_id, $crop_id, $growth_stage_id);	
			/*
			 // With footer        
			if(isset($footer)){
			$html = $objReport->HTMLReport_styles().
					$objReport->HTMLReport_header().					
					$objReport->HTMLReport_body().
					$objReport->HTMLReport_footer(); 
			} else {
				// No footer        
				$html = $objReport->HTMLReport_styles().
						$objReport->HTMLReport_header().						
						$objReport->HTMLReport_body();
			}
			*/
			// sending email save image to server and absolute link to it rather than embed base64 file
			//$imageData = $_POST['chartURI'];
			//$imageData = saveChartImage($_POST['chartURI']);
			// absolute path to server image if emailing or base64 for PDF embedding
			$imageData = $sendAsEmail === true ? self::saveChartImage($_POST['chartURI'], $farm_id, $paddock_id, $crop_id, $growth_stage_id) : $_POST['chartURI']; 
			$imageSummaryData = $sendAsEmail === true ? self::saveChartImage($_POST['chartSummaryURI'], $farm_id, $paddock_id, $crop_id, $growth_stage_id, true) : $_POST['chartSummaryURI']; 
		
			
			$html = $objReport->HTMLReport_styles().
					$objReport->HTMLReport_header().					
					$objReport->HTMLReport_farmInfo(true).
					$objReport->HTMLReport_table(false).					
					$objReport->HTMLReport_charts($imageData, $imageSummaryData).					
					$objReport->HTMLReport_data(false);
			
			// With footer        
			if(isset($footer)){			
				$html .= $objReport->HTMLReport_footer(); 
			} 			
		}
		
		/*
        echo '<pre>';
			print_r($objReport->report_name.'</br>');
			print_r($objReport->paddock);
			print_r($objReport->zones);
			print_r($objReport->samples);
		echo '</pre>';
		*/
    	//echo print_r($html);
		
        return $html;
    }
	
	public static function saveChartImage($base64, $farm_id, $paddock_id, $crop_id, $growth_stage_id, $summary = false){

		$isSummary = $summary === true ? '_summary': null;
		// image filename
		$imageName = $farm_id.'_'.$paddock_id.'_'.$crop_id.'_'.$growth_stage_id.$isSummary.'.png';
		// relative path
		$relativePath = '/images/tmp/';
		// local server path and filename
		$serverPath = $_SERVER['DOCUMENT_ROOT'].$relativePath.$imageName;		
		// Convert base64 string to image and save to the server using unique identifier name
		file_put_contents($serverPath, self::base64ToImage($base64));		
		// record file URI for use in the email
		$URI = Config::get('URL').substr($relativePath, 1).$imageName;
		
		return $URI;
	}
	
	
	public static function base64ToImage($base64){
		return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
	}
	
	public static function exportCSV($farm_id, $paddock_id, $crop_id, $growth_stage_id) {
		
		switch ($growth_stage_id){
			case 1:
				$filename = 'emergence_data.csv';
				break;
			case 2:
				$filename = '3leaf_data.csv';
				break;
			case 3:
				$filename = '5leaf_data.csv';
				break;
			case 4:	
				$filename = 'bulbing_data.csv';				
				break;
			case 5:
				$filename = 'harvest_data.csv';
				break;
			default:
				$filename = 'data.csv';
		}
		
		$csv_payload = array();
		
		// Create new csv class
		$csv = new ExportToCSV();
		// open file handle
		$csv->CSV_open($filename);
		// write header row
		$csv->CSV_writeHeader(self::setCSVHeader($growth_stage_id));
			
		// Create new sample data class
		$data = new ZoneSampleData($farm_id, $paddock_id, $crop_id, $growth_stage_id);
		// Get the sample data
		$payload = $data->buildSampleDataTable();
		
		// add this to the csv payload array
		foreach ($payload as $zones) {			
			// initialise			
			$i = 1;
			foreach ($zones as $sample_data) {
				$csv_row = array();
				
					switch ($growth_stage_id){
						case 1: // Emergence
						// header->"zone","plot#","sample_date","plants_plot","plants_m^2","pop_limited","comments"
							array_push($csv_row, $sample_data->zone_name, $i, $sample_data->sample_date, 
								$sample_data->sample_count_plot, round($sample_data->sample_count_sqm,2), 
								$sample_data->sample_population_limited, $sample_data->sample_comment);
							break;
						case 2: // 3 Leaf						
						case 3: // 5 Leaf	
						case 4: // Bulbing
						// header->"zone","plot#","sample_date","plants_plot","plants_m^2","cover%_plot","cm^2_plant_m^2","LAI","yield","comments"
							array_push($csv_row, $sample_data->zone_name, $i, $sample_data->sample_date, 
								$sample_data->sample_count_plot, $sample_data->sample_count_sqm, 
								$sample_data->sample_ela_score, $sample_data->sample_groundcover_cm_plant_sqm, 
								$sample_data->sample_lai_cm_plant_sqm, $sample_data->sample_yield_tonnes_hectare, 
								$sample_data->sample_comment);			
							break;
						case 5: // harvest
						// header->"zone","plot#","sample_date","bulbs_plot","bulbs_m^2","kg_plot","kg_m^2","yield","comments"
							array_push($csv_row, $sample_data->zone_name, $i, $sample_data->sample_date, 
								$sample_data->sample_count_plot, $sample_data->sample_count_sqm, 
								$sample_data->sample_weight_kg_plot, $sample_data->sample_weight_kg_sqm, 
								$sample_data->sample_yield_tonnes_hectare, $sample_data->sample_comment);
							break;
						default:
					}
				// add row to csv file
				$csv->CSV_writeRow($csv_row);
								
				unset($csv_row);
				$i++;
			}
		}
		// close file handle
		$csv->CSV_exit();		
		return true;		
    }
	
	private static function setCSVHeader($growth_stage_id){
		
		$header = array();
		switch ($growth_stage_id){
			case 1:	// Emergence
				array_push($header, "zone","plot#","sample_date","plants_plot","plants_m^2","pop_limited","comments");
				break;
			case 2:	 // 3 Leaf
			case 3:	 // 5 Leaf
			case 4:	 // Bulbing
				array_push($header, "zone","plot#","sample_date","plants_plot","plants_m^2","cover%_plot","cm^2_plant_m^2","LAI","yield","comments");
				break;
			case 5:	 // Harvest
				array_push($header, "zone","plot#","sample_date","bulbs_plot","bulbs_m^2","kg_plot","kg_m^2","yield","comments");
				break;
			default:
		}
		return $header;
	}
    
    public static function createEmail($email_to_address){
        
        $body = self::formatForOutput(null,null,null,null,true);

        $to_email = $email_to_address; 
        $from_email = Config::get('EMAIL_ASSESSMENT_REPORT_FROM_EMAIL');
        $from_name = Config::get('EMAIL_ASSESSMENT_REPORT_FROM_NAME'); 
        $subject = Config::get('EMAIL_ASSESSMENT_REPORT_SUBJECT');       

        $mail = new Mail;        
        
        $mail_sent = $mail->sendMail($to_email, $from_email, $from_name, $subject, $body);

        if ($mail_sent) {
            Session::add('feedback_positive', Text::get('FEEDBACK_REPORT_EMAIL_SENDING_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_REPORT_EMAIL_SENDING_FAILED') . $mail->getError() );
            return false;
        }        
    }
	
}
