<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php echo '<script src="'.Config::get('URL').'scripts/charting_plots.js"></script>'; ?>
<form method="post" action="<?php echo Config::get('URL');?>reports/assessment_action">
<div class="container">
	<h1>Harvest Assessment Report<span style="float:right;"><input type="submit" value="Save As PDF" name="assessment" id="save_pdf_image" title="Save PDF" class="imgPDF" /></span>
					<span style="float:right;"><input type="submit" value="Export To CSV" name="assessment" id="export_csv_image" title="Export CSV" class="imgCSV" /></span>
					<span style="float:right;"><input type="submit" value="Send Via Email" name="assessment" id="send_email_image" title="Email Report" class="imgEmail" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="background-color: transparent;"'; } ?>/></span>
	</h1>
	<div class="report-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php 
			$this->renderFeedbackMessages(); 
			/* Set current page name */
			Session::set('page', basename(__FILE__, ".php"));
		?>
		<p>
			<!--<form method="post" action="<?php echo Config::get('URL');?>reports/assessment_action">-->

				<!-- report table::start -->
				<?php echo $this->html_report_table; ?>					
				<!-- report table::end -->		
				<input type="hidden" name="report_name" value="<?php echo $this->report_name ?>">
				<input type="hidden" name="farm_id" value="<?php echo $this->farm_id ?>">
				<input type="hidden" name="paddock_id" value="<?php echo $this->paddock_id ?>">
				<input type="hidden" id="growth_stage_id" name="growth_stage_id" value="<?php echo $this->growth_stage_id ?>">
				<input type="hidden" id="chartData" name="chartData" value='<?php echo $this->chartData ?>'>
				<input type="hidden" id="chartAverages" name="chartAverages" value='<?php echo $this->chartAverages ?>'>
				<input type="hidden" id="hAxisTitle" name="hAxisTitle" value='<?php echo $this->hAxisTitle ?>'>
				<input type="hidden" id="gridLines" name="gridLines" value='<?php echo $this->gridLines ?>'>
				<input type="hidden" id="vAxisTitle" name="vAxisTitle" value='<?php echo $this->vAxisTitle ?>'>
				<input type="hidden" id="vAxisMin" name="vAxisMin" value='<?php echo $this->vAxisMin ?>'>
				<input type="hidden" id="vAxisMax" name="vAxisMax" value='<?php echo $this->vAxisMax ?>'>
				<input type="hidden" id="chartURI" name="chartURI" value="">
				<input type="hidden" id="chartSummaryURI" name="chartSummaryURI" value="">					
				<input type="submit" value="Save As PDF" name="assessment" id="save_pdf">
				<input type="submit" value="Export To CSV" name="assessment" id="export_csv">
				<input type="submit" value="Send Via Email" name="assessment" id="send_email" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>>
            
			<!-- Return to assessment selection page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>reports">Exit</a>
            </div>
        </p>        
    </div>
	
</div>
</form>