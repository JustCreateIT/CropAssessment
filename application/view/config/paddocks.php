<div class="container">
    <h1>Edit Paddocks</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		
		<div class="table-edit">  
			<table>
			<thead>
			<tr>
				<td><label>Paddock<label></td>
				<td><label>Address<label></td>
				<td><label style="width:50px;">Area (h)<label></td>
			<tr>
			</thead>
			<?php foreach ($this->paddocks as $paddock) { 
				$paddock_area = $paddock->paddock_google_area > 0 ? $paddock->paddock_google_area : $paddock->paddock_area; ?>
			
			<form action="<?= config::get("URL"); ?>config/configUpdateDeletePaddock" method="post">
				<tr>
					<td><input style="width:100px;" type="text" name="paddock_name" value="<?= $paddock->paddock_name; ?>"></td>
					<td><input style="width:250px;" type="text" name="paddock_address" value="<?= $paddock->paddock_address; ?>"></td>
					<td><input style="width:50px;" type="number" step=0.01 min=0 name="paddock_area" value="<?= $paddock_area ?>"></td>
					<td><input type="submit" name="submit" value="Update" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>/></td><td><input type="submit" name="submit" value="Delete" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>/></td>
				</tr>
				<input type="hidden" name="paddock_id" value="<?= $paddock->paddock_id; ?>" />
				<input type="hidden" name="return_page" value="config/paddocks">
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