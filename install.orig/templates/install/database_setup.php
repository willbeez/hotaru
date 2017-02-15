		
		<!-- Step title -->
		<legend><?php echo $lang['install_step1']; ?></legend>
			
		<div class='alert alert-info' role='alert'>
			<strong><?php echo $lang['install_step1_instructions_create_db']; ?></strong>
			<!-- Complete Step Progress Bar -->
			<div class='progress'>
				<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100' style='width: 25%'>
					<span class='sr-only'>25% Complete</span>
				</div>
			</div>
                        <?php echo $lang['install_step1_instructions_manual_setup']; ?>&nbsp;<a href='?step=1&action=install&type=manual'><?php echo $lang['install_step1_instructions_manual_setup_click']; ?></a>.

		</div>
	
		<!-- Manual creation link -->
		<div class='install_content'>
			
			<?php showMessages($h); ?>
			
			<div class='panel panel-primary'>
				<div class='panel-heading'>
					<h3 class='panel-title'>Database Setup Information</h3>
				</div>
				<div class='panel-body'>
				
					<!-- Registration form -->
					<form class='form-horizontal' role='form' name='install_admin_reg_form' action='../install/index.php?step=1' method='post'>

						<!-- BASEURL -->
						<div class='form-group'>
							<label for='inputBaseURL' class='col-sm-2 control-label'><?php echo $lang['install_step1_baseurl']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='baseurl' id='inputBaseURL' value='<?php echo $baseurl_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_baseurl_explain']; ?></p>
							</div>
						</div>
						
						<!-- DB_USER -->
						<div class='form-group'>
							<label for='inputDBuser' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbuser']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbuser' id='inputDBuser' value='<?php echo $dbuser_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbuser_explain']; ?></p>
							</div>
						</div>
						
						<!-- DB_PASSWORD -->
						<div class='form-group'>
							<label for='inputDBpassword' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbpassword']; ?></label>
							<div class='col-sm-5'>
								<input type='password' class='form-control' name='dbpassword' id='inputDBpassword' value='<?php echo $dbpassword_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbpassword_explain']; ?></p>
							</div>
						</div>
					
						<!-- DB_NAME -->
						<div class='form-group'>
							<label for='inputDBname' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbname']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbname' id='inputDBname' value='<?php echo $dbname_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbname_explain']; ?></p>
							</div>
						</div>

						<!-- DB_PREFIX -->
						<div class='form-group'>
							<label for='inputDBprefix' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbprefix']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbprefix' id='inputDBprefix' value='<?php echo $dbprefix_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbprefix_explain']; ?></p>
							</div>
						</div>
						
						<!-- DB_HOST -->
						<div class='form-group'>
							<label for='inputDBhost' class='col-sm-2 control-label'><?php echo $lang['install_step1_dbhost']; ?></label>
							<div class='col-sm-5'>
								<input type='text' class='form-control' name='dbhost' id='inputDBhost' value='<?php echo $dbhost_name; ?>'>
								<p class='help-block'><?php echo $lang['install_step1_dbhost_explain']; ?></p>
							</div>
						</div>
                                                
                                                <?php
                                                        $btnType = 'btn-primary'; 
                                                        if ($cage->post->getAlpha('updated') != 'true' && $settings_file_exists) {
                                                            $btnType = 'btn-danger'; 
                                                        ?>
                                                        <!-- Alert if Settings file already exists -->
                                                        <div class='col-sm-offset-1 col-sm-7 alert alert-warning'>
                                                                <?php echo $lang['install_step1_settings_file_already_exists']; ?>
                                                        </div>
                                                <?php } ?>
						
						<!-- Update button -->
						<div class='form-group'>
							<div class='col-sm-offset-2 col-sm-10'>
								<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
                                                                <input type='hidden' name='step' value='2' />
								<input type='hidden' name='updated' value='true' />
                                                                <input type='submit' id='install_dbTestBtn' class='btn btn-default' value='Test Db Connection' />
                                                                <button class='btn <?php echo $btnType; ?>'><i class='fa fa-save'></i> <?php echo $lang['install_step3_form_update']; ?></button>
							</div>
						</div>
						
					</form>
				</div>
			</div>
                    
                    

			<?php
			if (isset($table_exists) && ($table_exists)) { ?>
			
				<!-- Alert if database already exists -->
				<div class='alert alert-danger' role='alert'>
					<i class='fa fa-exclamation-triangle'></i> <?php echo $lang['install_step1_settings_db_already_exists']; ?>
				</div>
			<?php } ?>
			

			<div class='form-actions'>
				<!-- Previous/Next buttons -->
				<a href='index.php?action=install&step=0' class='btn btn-default' role='button'><i class='fa fa-arrow-left'></i> <?php echo $lang['install_back']; ?></a>
				<?php if ($show_next) { // and if db was connected ok ?>
					<a href='index.php?action=install&step=2' class='btn btn-default pull-right' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
				<?php } else { // link disbaled ?>		    
					<a class='btn btn-default disabled pull-right' href='#' role='button'><?php echo $lang['install_next']; ?> <i class='fa fa-arrow-right'></i></a>
				<?php } ?>
			</div>
		</div>
                
<script type='text/javascript'>
    jQuery('document').ready(function($) {  
        $('#install_dbTestBtn').click(function() {
            event.preventDefault();
            var dbuser_name = $('#inputDBuser').val();
            var dbpassword_name = $('#inputDBpassword').val();
            var dbhost_name = $('#inputDBhost').val();
            var dbname_name = $('#inputDBname').val();
            
            var sendurl = "index.php?step=99";
            var formdata = 'dbuser_name=' + dbuser_name + '&dbpassword_name='  + dbpassword_name + '&dbhost_name='  + dbhost_name + '&dbname_name='  + dbname_name;

            $.ajax({
                type: 'get',
                url: sendurl,
                data: formdata,

                beforeSend: function () {
                        $('#install_dbTestBtn').removeClass('btn-success').removeClass('btn-danger').removeClass('btn-warning').addClass('btn-primary');
                        $('#install_dbTestBtn').val('Checking Db Connection');
                },
                error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                        $('#install_dbTestBtn').val('Code Error');
                        $('#install_dbTestBtn').removeClass('btn-primary').addClass('btn-danger');
                },
                success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                    if (data.error === true) {
                        $('#install_dbTestBtn').removeClass('btn-primary').addClass('btn-warning');
                        $('#install_dbTestBtn').val('Connection Failed: Test Again');
                    } else {           
                        $('#install_dbTestBtn').val('Db Connection Success');
                        $('#install_dbTestBtn').removeClass('btn-primary').addClass('btn-success');
                    }
                    //$('.message').html(data.message);
                },
                dataType: "json"
            });
        });
    });
</script>