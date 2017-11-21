<?php

class ZoneSampleData extends Crop {

	private $growth_stage_id;

	public function __construct($farm_id, $paddock_id, $crop_id, $growth_stage_id) {
		
		// initialise parent class
		parent::__construct($farm_id, $paddock_id, $crop_id);

		$this->growth_stage_id = $growth_stage_id;
	}
	
	public function buildSampleDataTable(){

		/* At the specific growth stage get all samples for each zone; */
		$database = DatabaseFactory::getFactory()->getConnection();

		$sql = "SELECT z.zone_name, s.zone_sample_plot_id, s.sample_date, s.sample_count,
			s.sample_comment, s.sample_ela_score, s.sample_bulb_weight
			FROM zone z, sample s
			WHERE				
			s.growth_stage_id = :growth_stage_id AND
			s.zone_id = z.zone_id AND
			s.crop_id = :crop_id AND
			s.paddock_id = :paddock_id AND				
			s.farm_id = :farm_id				
			ORDER BY 
			z.zone_id, s.zone_sample_plot_id";	

		$query = $database->prepare($sql);
		$query->execute(array(':growth_stage_id' => $this->growth_stage_id,
							':crop_id' => $this->crop_id,
							':paddock_id' => $this->paddock_id,
							':farm_id' =>$this->farm_id));

		//$plot_width = Config::get('SAMPLE_PLOT_WIDTH');
		$plot_width = $this->crop->crop_sample_plot_width;		
		$crop_bed_width = $this->crop_bed_width;
		$planting_date = $this->crop_plant_date;

		// data table
		$zone_data = array();
		
		foreach ($query->fetchAll() as $data) {	
			//echo print_r($data->zone_id, true);
			$zone_data[$data->zone_name][$data->zone_sample_plot_id] = new stdClass();
			$zone_data[$data->zone_name][$data->zone_sample_plot_id]->zone_name = $data->zone_name;	
			$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_date = $data->sample_date;
			$sample_count_plot = $data->sample_count;
			$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_count_plot = $sample_count_plot;			
			$samples_sqm = ReportsModel::unitPerSquareMeter($sample_count_plot, $crop_bed_width, $plot_width);				
			$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_count_sqm = $samples_sqm;
			if ( $this->growth_stage_id == 1 ) {
				$populationLimited = ReportsModel::populationLimited($this->crop_plant_spacing, $this->crop_bed_rows, 
								$this->crop_bed_width, $this->crop->crop_sample_plot_width, $data->sample_count);
				$isLimited = ($populationLimited) ? 'true': 'false';				
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_population_limited = $isLimited;
			}	
			if ( $this->growth_stage_id == 2 || $this->growth_stage_id == 3 || $this->growth_stage_id == 4 ){ // 3,5,bulbing
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_ela_score = $data->sample_ela_score;				
				//$gc_cm_plant_sqm = (10000/($paddock_bed_width*$plot_width))*($data->sample_ela_score/100)/$samples_sqm;
				$gc_cm_plant_sqm = (10000*($data->sample_ela_score/100))/$samples_sqm;
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_groundcover_cm_plant_sqm = $gc_cm_plant_sqm;
				$lai_cm_plant_sqm = $this->GroundCoverCMToLAI($gc_cm_plant_sqm);
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_lai_cm_plant_sqm = $lai_cm_plant_sqm;
				$estimated_yield_tonnes_hectare = ReportsModel::zoneTargetYield($this->growth_stage_id, $lai_cm_plant_sqm, $samples_sqm, $planting_date);
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_yield_tonnes_hectare = $estimated_yield_tonnes_hectare;
			}
			if ( $this->growth_stage_id == 5 ){ // harvest 
				// add weight data
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_weight_kg_plot = $data->sample_bulb_weight;
				//$sample_weight_kg_sqm = (1/($paddock_bed_width*$plot_width))*$data->sample_bulb_weight;
				$sample_weight_kg_sqm = ReportsModel::unitPerSquareMeter($data->sample_bulb_weight, $crop_bed_width, $plot_width);
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_weight_kg_sqm = $sample_weight_kg_sqm;				
				$sample_yield_tonnes_hectare = number_format($sample_weight_kg_sqm*10, 2, '.', ''); // harvest stage
				$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_yield_tonnes_hectare = $sample_yield_tonnes_hectare;
					//self::setZoneYieldEstimateByGrowthStage($farm_id, $paddock_id, $result->zone_id, $growth_stage_id, $zone_harvest_yield);
			}
			$zone_data[$data->zone_name][$data->zone_sample_plot_id]->sample_comment = $data->sample_comment;	
		}	
		return $zone_data;
	}
	
	private function GroundCoverCMToLAI($gc_cm_plant_sqm){
		
		(float)$a = 0.183;		
		(float)$b = 0.0498;	
		$LAI = pow($a,$b) * (float)$gc_cm_plant_sqm;	
		
		return $LAI;	
	}
	
}

