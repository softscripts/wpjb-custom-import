<?php function jci_import() {
global $wpdb;
$url = jci_get_url('import'); //Current page url

/******************
 Common Requests
******************/
$status = '';
$msg = '';

/**************
 Step1 Setup
***************/
if(isset($_REQUEST['import_step'])) {
	if(!empty($_REQUEST['jci_import_xml'])) {
		$xml = $_REQUEST['jci_import_xml'];
		$pwd = $_REQUEST['jci_import_pwd'];
		$result = jci_import_xml($xml,$pwd);
		
		if($result->result == 0) {
			$status = 'error';
		 	$msg = (string)$result->message;
		} else {
			$status = 'success';
		 	$msg = 'Done! jobs imported sucessfully.';
		}
	}
}

?>
<div class="wrap jci_wrap">
	<div class="add_new"><div id="icon-edit-pages" class="icon32"><br></div><h2>WPJB - Custom Importer</h2></div>			
	<div class="jci_container">
		<?php if(!empty($msg)) { ?>
		<div class="alert alert-<?php echo $status; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
		    <?php echo $msg; ?>
		</div>
		<?php } ?>
		<form method="post" id="import_form_step" class="wb_steps_form" name="import_form_step" action="">
			<div class="field-set">
				<label class="description" for="jci_import_xml"><?php _e( 'Insert XML url'); ?></label> <input id="jci_import_xml" class="j_import_xml regular-text large data-required" type="text" name="jci_import_xml" value="" />&nbsp;&nbsp;OR&nbsp;&nbsp;<input id="button_jci_import_xml" class="meta_upload button" name="button_jci_import_xml" type="button" value="Upload here" style="width: auto;" />
			</div>	
			<div class="field-set">
				<div class="field-notes">Please enter your password to confirm importing xml file.</div>
				<label class="description" for="jci_import_pwd"><?php _e( 'Password'); ?></label> <input id="jci_import_pwd" class="jci_import_pwd regular-text large data-required" type="password" name="jci_import_pwd" value="" />				
			</div>	
			<div class="submit">
			<input type="submit" id="jci-submit" class="wb-sumit button-primary" name="import_step" value="<?php _e( 'Upload and start import'); ?>" />
			</div>
		</form>	
	</div>
</div> <!-- end wrapper -->
<?php } ?>
