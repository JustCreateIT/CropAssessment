<div class="container">
    <h1>Setup A New Farm</h1>
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
        <h2>Farm Information</h2>
        <p>
            <form id="input_form" method="post" action="<?php echo Config::get('URL');?>setup/createFarm_action">
                <p>
					<label>Farm name</label>
					<input type="text" name="farm_name" required />
				</p>
                <p>
					<label>First name</label>
					<input type="text" name="farm_contact_firstname" value="<?= $this->user_first_name; ?>" required />
				</p>
                <p>
					<label>Last name</label>
					<input type="text" name="farm_contact_lastname" value="<?= $this->user_last_name; ?>" required />
				</p>
                <p>
					<label>Email address</label>
					<input type="text" name="farm_email_address" value="<?= $this->user_email; ?>" required />
				</p>
                <p>
					<label>Phone number</label>
					<input type="text" name="farm_phone_number" value="<?= $this->user_phone_number; ?>" />
				</p>
                <p><input type="submit" value="Save Farm Details" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>>
            </form>
			<!-- Return to the setup page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>setup">Exit</a>
            </div>			
        </p>
    </div>
</div>
