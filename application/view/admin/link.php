<div class="container">
    <h1>Administration Panel</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
        <h3>Link Farms to the current User</h3>
        <div class="overview-table">
            <table class="overview-table">							
                <thead>
                <tr>                    				
					<td nowrap>Full Name</td>
					<td>Available Farms</td>
					<td>&nbsp;</td>
					<td>Farms To Link</td>
				</tr>
                </thead>
				<tbody><?php if ($this->user) { ?>
				<tr class="<?= ($this->user->user_active == 0 ? 'inactive' : 'active'); ?>">
					<form action="<?= config::get("URL"); ?>admin/actionLinkFarmsToUser" method="post">
					<!--<td><?= $this->user->user_id; ?></td>-->
					<td nowrap><?= $this->user->user_first_name; ?>&nbsp;<?= $this->user->user_last_name; ?></td>
					<td>
					<select id="source" size="<?= $this->farms_count; ?>" multiple style="width:250px;">
					<?php foreach ( $this->unlinked_farm_users as $unlinked_farm_user ) { 
						if ($this->user->user_id == $unlinked_farm_user->user_id){
							?> <option value="<?= $unlinked_farm_user->farm_id ?>"><?= ucwords($unlinked_farm_user->farm_name) ?></option>
						<?php } 
					} ?>
					</select></td>
					<td class="adminBtn">
						<button type="button" id="btnAllRight">>></button>
						<button type="button" id="btnRight">></button>
						<button type="button" id="btnLeft"><</button>
						<button type="button" id="btnAllLeft"><<</button>
					</td>
					<td><select id="destination" name="destination[ ]" multiple style="width:250px;" size="<?= $this->farms_count; ?>">
					<?php foreach ( $this->farm_users as $farm_user ) { 
						if ($this->user->user_id == $farm_user->user_id){
							?> <option value="<?= $farm_user->farm_id ?>"><?= ucwords($farm_user->farm_name) ?></option>
						<?php } 
					} ?>
					</select></td>
					<td>
						<input type="hidden" name="user_id" value="<?= $this->user->user_id; ?>" />						
						<input type="submit" name="link_farms" id="link_farms" value="Link Farms" <?php if((Session::get("user_account_type") != 88)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>/>
					</td>					
					</form>
				</tr>
				</tbody>
                <?php } ?>
            </table>
		
        </div>

    </div>
	<!-- Return to previous selection page -->
	<div class="app-button" style="margin: 0 auto;">                
		<a style="margin: 0 auto;" href="<?php echo Config::get('URL'); ?>admin">Back</a>
	</div>	
</div>
