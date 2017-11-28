<div class="container">    
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
        <div class="form_input">			 
			<form action="<?= config::get("URL"); ?>user/actionAddUser" style="width:350px;margin: 0 auto;" method="post">
			<h1>Add A Farm User</h1>
			<div>
				<label for="farm_details">Select a farm:</label>
			</div>
				<div>				
				<select name="farm_details" id="farm_details">
					<?php foreach ($this->farm_details as $farm) { ?> 
					<option value="<?= $farm->farm_id ?>"><?= ucwords($farm->farm_name) ?></option>
					<?php } ?>
				</select>
			</div>
			<div>
				<label for="user_first_name">First name:</label>
			</div>
			<div>				
				<input type="text" name="user_first_name" id="user_first_name">
			</div>
			<div>
				<label for="user_last_name">Last name:</label>
			</div>
			<div>				
				<input type="text" name="user_last_name" id="user_last_name">
			</div>			
			<div>
				<label for="user_email_address">Email address:</label>
			</div>
			<div>				
				<input type="email" name="user_email_address" id="user_email_address">
			</div>
			<div>
				<label for="user_phone_number">Phone number:</label>
			</div>
			<div>				
				<input type="tel" name="user_phone_number" id="user_phone_number">
			</div>			
			<div>
				<label for="user_password">Password:</label>
			</div>
			<div>				
				<span><input type="text" name="user_password" id="user_password" rel="generate_password" data-size="16" data-character-set="A-Z,a-z,0-9" style="width:298px;"><button type="button" id="generate_password" name="generate_password"></button></span>
			</div>
			<div>
				<label for="user_account_type_id">Account type:</label>
			</div>
			<div>				
				<select name="user_account_type_id" id="user_account_type_id">
					<?php foreach ($this->account_types as $account_type) { 
						// owners add Standard users only
						if (ucwords($account_type->account_name) == "Standard") { ?> 
						<option value="<?= $account_type->account_type ?>" selected><?= ucwords($account_type->account_name) ?></option>
					<?php } 									
					} ?></select>
			</div>
			
			<div>
				<span><label for="send_details_user">Send user email confirmation:</label><input type="checkbox" name="send_details_user" id="send_details_user" value="" checked disabled></span>
			</div>
			<div>
				<span><label for="send_details_self">Send yourself email confirmation:</label><input type="checkbox" name="send_details_self" id="send_details_self" value="" checked></span>
			</div>			
			<div>
				<label for="submit"></label>
				<input type="submit" value="Send Email">
			</div>			
			</form>
        </div>		
    </div>
</div>
