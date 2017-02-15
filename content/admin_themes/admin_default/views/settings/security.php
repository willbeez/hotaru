<?php 
/**
 * Theme name: admin_default
 * Template name: settings.php
 * Template author: shibuya246
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
$thisSettings = array();
$loaded_settings = $h->vars['admin_settings'];
foreach ($loaded_settings as $setting) {        
    if (strtolower($setting->settings_type) == 'security') {        
        $thisSettings[] = $setting;
    }
}
$loaded_settings = $thisSettings;
?>
        
    <form id='settings_form' class='form' name='settings_form' action='<?php echo SITEURL; ?>admin_index.php?page=settings' method='post'>
	
	<table id="settings" class="table table-bordered">    
            <tr class="info">
                    <td><?php echo $h->lang("admin_theme_settings_setting"); ?></td>
                    <td><?php echo $h->lang("admin_theme_settings_value"); ?></td>
                    <td><?php echo $h->lang("admin_theme_settings_default"); ?></td>
                    <td><?php echo $h->lang("admin_theme_settings_notes"); ?></td>
            </tr>
	
            <?php // **********************************************************
	
		// Loop through the settings, displaying each one as a row...  
                if ($loaded_settings) { 
		foreach ($loaded_settings as $ls)
		{
                    if ($ls->settings_show == 'N') { continue; } // skip this row

                    // replace underscores with spaces and make the first character of the setting name uppercase.
                    $name = ucfirst(preg_replace('/_/', ' ', $ls->settings_name));

                    // get settings_names that need warning for '/' character being attached to text
                    if ($ls->settings_name == 'THEME' || $ls->settings_name == 'ADMIN_THEME') { $css_class = ' class="warning_slash"'; } else {$css_class = ''; }

		?>
                    <tr>
			<td>
			    <?php
			    if (strpos($name, 'CACHE')) {echo "<a href='" . SITEURL . "/admin_index.php?page=maintenance'>" . $name . "</a>"; }
			    elseif ($name == 'THEME') {echo "<a href='" . SITEURL . "admin_index.php?page=theme_settings&theme=" . $ls->settings_value . "'>" . $name . "</a>"; }
			    else { echo $name; }
			    ?>:
                            <?php if ($ls->settings_name == 'FORUM_PASSWORD') {
                                echo '&nbsp;<span id="admin_settings_btn_check_password" class="btn btn-warning btn-xs">Check Password</span>';
                            } ?>
                            <?php if ($ls->settings_name == 'HOTARU_API_KEY') {
                                echo '&nbsp;<span id="admin_settings_btn_get_hotaru_api_key" class="btn btn-danger btn-xs">Reset API Key</span>';
                            } ?>
                            <?php if ($ls->settings_name == 'HOTARUCMS_COM_CONNECTED') {
                                echo '&nbsp;<span id="admin_settings_btn_hotaru_com_connect" class="btn btn-primary btn-xs">Connect @HotaruCMS.com</span>';
                            } ?>
			</td>
			<td>
                                <?php
                                if ( $ls->settings_value == 'true' || $ls->settings_value == 'false' ) {
                                        echo '<input class="radio-inline" type="radio" name="' . $ls->settings_name .'" value="true" ';
                                        if ($ls->settings_value == 'true') { echo ' checked'; }
                                        echo ' >&nbsp;ON&nbsp;&nbsp;';
                                        echo '<input class="radio-inline" type="radio" name="' . $ls->settings_name .'" value="false" ';
                                        if ($ls->settings_value == 'false') { echo ' checked'; }
                                        echo ' >&nbsp;OFF';
                                }
                                else {
                                        $type = strpos($ls->settings_name,'PASSWORD') !== false ? 'password' : 'text';
                                        $disabled = $ls->settings_name == 'HOTARU_API_KEY' ? 'disabled' : '';
                                        echo '<input ' . $disabled . ' id="input_' . $ls->settings_name . '" class="form-control" type="' . $type . '" size=20 name="' . $ls->settings_name .'" value="' . $ls->settings_value . '" ' . $css_class . ' />';
                                }
                                ?>
			</td>
			<td>
				<?php 
					if ($ls->settings_default == 'true') {
						echo "ON"; 
					} elseif($ls->settings_default == 'false') {
						echo "OFF"; 
					} else {
						echo $ls->settings_default; 
					}
				?>
			</td>
			<td><i><?php echo $ls->settings_note; ?></i></td>
                    </tr>
	 
            <?php }} // End loop **********************************************************     ?>


            <input type='hidden' name='settings_update' value='true' />
            <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        </table>
        <div class="form-actions">
            <input id='settings_submit' class='btn btn-primary' type='submit' value='Save' />
        </div>
    </form>
