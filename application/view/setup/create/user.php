<div class="container">
    <h1>Add A New Farm User</h1>
	<div class="input-box">
		<!-- echo out the system feedback (error and success messages) -->
        <div id="feedback_messages"></div>
		<form id="addFarmUser_action" method="post" action="<?php echo Config::get('URL');?>setup/addFarmUser_action">        
        <div><label for="farm_id">Select Farm</label><select name="farm_id" id="farm_id"></select></div>
		<h2>User Information</h2>		
        <p>
           <p>
				<label>First name</label>
				<input type="text" placeholder="First name" name="user_first_name" value="" required />
			</p>
			<p>
				<label>Last name</label>
				<input type="text" placeholder="Last name" name="user_last_name" value="" required />
			</p>
			<p>
				<label>Email address</label>
				<input type="text" placeholder="User email address (a real address)" name="user_email" value="" required />
			</p>
			<p>
				<label>Confirm Email address</label>
				<input type="text" placeholder="Repeat email address (to prevent typos)" name="user_email_repeat" value="" required />
			</p>			
			<p>
				<label>Phone number</label>
				<input type="text" placeholder="Phone number" name="user_phone_number" value="" />
			</p>
	
			<div class="submit_float_left"> 				
				<div class="app-button" style="margin:0; padding:0;">                
					<a href="<?php echo Config::get('URL'); ?>setup">Back</a>
				</div>
			</div>
			<div class="submit_float_left">
				<input class="disabled" type="submit" id="btnSubmit" value="Send User Request" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc;"'; } ?>/>
			</div>
			<div style="clear:both;"></div>
		</form>
		<input type="hidden" name="farm_details" id="farm_details" value='<?php echo $this->farm_details; ?>'>			
		</p>
    </div>
</div>
<div id="loader_icon" style="display:none;"><img src="<?php echo Config::get('URL'); ?>images/ajax-loader.gif" /></div>
<div class="container">
    <p style="display: block; font-size: 0.75em; color: #999; margin:15px 0 0 0;">
        Once submitted the user will receive an email with information detailing how to access their account. If they do not receive the email message have them check their spam filter settings. You should then resend a new request from this page.
    </p>
</div>