<div class="container">
    <h1>Edit Farm Details</h1>
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php 
			$this->renderFeedbackMessages(); ?>
        <?php if ($this->paddock) { ?>
		<p>
            <form method="post" action="<?php echo Config::get('URL'); ?>edit/editSavePaddock">
                <!-- we use htmlentities() here to prevent user input with " etc. break the HTML -->                
				<div><label>Paddock name</label><input type="text" name="paddock_name" value="<?php echo htmlentities($this->paddock->paddock_name); ?>" /></div>
                <div class='float_left'><label>Firstname</label><input type="text" name="farm_contact_firstname" value="<?php echo htmlentities($this->paddock->farm_contact_firstname); ?>" /></div>
                <div class='float_right'><label>Lastname</label><input type="text" name="farm_contact_lastname" value="<?php echo htmlentities($this->paddock->farm_contact_lastname); ?>" /></div>
                <div class='float_left'><label>Email address</label><input type="text" name="farm_email_address" value="<?php echo $this->paddock->farm_email_address; ?>" readonly/></div>
                <div class='float_right'><label>Phone number</label><input type="text" name="farm_phone_number" value="<?php echo htmlentities($this->paddock->farm_phone_number); ?>" /></div>				  
				<p><input type="submit" value='Update Farm' /></p>
				<input type="hidden" name="farm_id" value="<?php echo htmlentities($this->paddock->farm_id); ?>" />
            </form>
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>edit">Exit</a>
            </div>
        </p>
        <?php } else { ?>
            <p>This farm does not exist.</p>
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>edit">Exit</a>
            </div>
        <?php } ?>        
    </div>
</div>  
