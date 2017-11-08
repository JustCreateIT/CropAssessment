<div class="container">
    <h1>Edit Farms</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		
		<div class="table-edit">  
			<table>
			<thead>
			<tr>
				<td><label>Farm<label></td>
				<td><label style="width:85px;">Firstname<label></td>
				<td><label style="width:85px;">Lastname<label></td>
				<td><label style="width:100px;">Phone#<label></td>
				<td colspan="2"><label>Email address<label></td>
			<tr>
			</thead>
			<?php foreach ($this->farms as $farm) { ?>
			
			<form action="<?= config::get("URL"); ?>config/configUpdateDeleteFarm" method="post">
				<tr>
					<td><input style="width:auto;" type="text" name="farm_name" value="<?= $farm->farm_name; ?>" /></td>
					<td><input style="width:85px;" type="text" name="farm_contact_firstname" value="<?= $farm->farm_contact_firstname; ?>" /></td>
					<td><input style="width:85px;" type="text" name="farm_contact_lastname" value="<?= $farm->farm_contact_lastname; ?>" /></td>
					<td><input style="width:100px;" type="text" name="farm_phone_number" value="<?= $farm->farm_phone_number; ?>" /></td>
					<td><input type="text" name="farm_email_address" value="<?= $farm->farm_email_address; ?>" readonly/></td>
					<td><input type="submit" name="submit" value="Update" <?php if((Session::get("user_account_type") == 1) || (Session::get("user_account_type") == 5)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>/></td><td><input type="submit" name="submit" value="Delete" <?php if((Session::get("user_account_type") == 1) || (Session::get("user_account_type") == 5)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>/></td>
				</tr>
				<input type="hidden" name="farm_id" value="<?= $farm->farm_id; ?>" />
				<input type="hidden" name="return_page" value="config/farms">
			</form>
			<?php } ?>
			</table>				
        </div>
		<div class="app-button" style="margin: 15px 0 0 0;">                
			<a href="<?php echo Config::get('URL'); ?>config">Exit</a>
		</div>
		<!-- End content section -->
    </div>
</div>