<?php echo $this->report_header; ?>
<!-- echo out the system feedback (error and success messages) -->
<?php 
	$this->renderFeedbackMessages(); 
	/* Set current page name */
	Session::set('page', basename(__FILE__, ".php"));
?>
<!-- report table -->
<?php echo $this->html_report_table; ?>
<!-- report data -->
<?php echo $this->report_hidden; ?>
<!-- page navigation -->
<?php echo $this->bottom_navigation; ?>