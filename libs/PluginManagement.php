<?php
/**
 * Plugin Management Functions
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
namespace Libs;

class PluginManagement extends Prefab
{
	/**
	 * Get an array of plugins
	 *
	 * Reads plugin info directly from top of plugin files, then compares each 
	 * plugin to the database. If present and latest version, reads info from 
	 * the database. If not in database or newer version, uses info from plugin 
	 * file. Used by Plugin Management.
	 *
	 * @return array $allplugins
	 */
	public function getPlugins($h)
	{ 
		$plugins_array = $this->getPluginsMeta();
		$count = 0;
		$allplugins = array();
		
		if ($plugins_array) {
                        $pluginsDb = array();
                        //$pluginsFromDb = \Hotaru\Models\Plugin::getAllActiveAndInactiveDetails();
                        $pluginsFromDb = \Hotaru\Models2\Plugin::getAllActiveAndInactiveDetails($h);
                        if ($pluginsFromDb) {
                            foreach ($pluginsFromDb as $pluginFromDb)
                            { 
                                $pluginsDb[$pluginFromDb->plugin_folder] = $pluginFromDb;
                            }
                        }
                        
			foreach ($plugins_array as $plugin_details) {
			
				$allplugins[$count] = array();
//				
                                $plugin_row = isset( $pluginsDb[$plugin_details['folder']] ) ? $pluginsDb[$plugin_details['folder']] : null;
				
				if ($plugin_row) {
					// if plugin is in the database...
                                        $allplugins[$count]['id'] = $plugin_row->plugin_id;   // need this for when we reorder the lists - use id in the sort_id col
					$allplugins[$count]['name'] = $plugin_row->plugin_name;
					$allplugins[$count]['description'] = $plugin_row->plugin_desc;
					$allplugins[$count]['folder'] = $plugin_row->plugin_folder;
					$allplugins[$count]['author'] = $plugin_row->plugin_author;
					$allplugins[$count]['authorurl'] = urldecode($plugin_row->plugin_authorurl);
					$allplugins[$count]['resourceId'] = urldecode($plugin_row->plugin_resourceId);
					$allplugins[$count]['resourceVersionId'] = urldecode($plugin_row->plugin_resourceVersionId);
					$allplugins[$count]['status'] = $plugin_row->plugin_enabled ? 'active' : 'inactive';
					$allplugins[$count]['version'] = $plugin_row->plugin_version;
					$allplugins[$count]['latestversion'] = $plugin_row->plugin_latestversion;
					$allplugins[$count]['install'] = "installed";
					$allplugins[$count]['location'] = "database";
					$allplugins[$count]['settings'] = $h->hasSettings($allplugins[$count]['folder']); // true or false
					$allplugins[$count]['order'] = $plugin_row->plugin_order;
				} else {
					// if plugin is not in database...                                        
					$allplugins[$count]['name'] = $plugin_details['name'];
					$allplugins[$count]['description'] = $plugin_details['description'];
					$allplugins[$count]['folder'] = $plugin_details['folder'];
					
					if (isset($plugin_details['author'])) {
						$allplugins[$count]['author'] = $plugin_details['author'];
					}
					
					if (isset($plugin_details['authorurl'])) {
						$allplugins[$count]['authorurl'] = urldecode($plugin_details['authorurl']);
					}
					
					$allplugins[$count]['status'] = "inactive";
					$allplugins[$count]['version'] = $plugin_details['version'];					
					$allplugins[$count]['install'] = "install";
					$allplugins[$count]['location'] = "folder";
					$allplugins[$count]['order'] = 0;
				}
				
				// Conditions for "active"...
				if ($allplugins[$count]['status'] == 'active') {					
					$allplugins[$count]['active'] = "<div class='switch'  id='switch#". $allplugins[$count]['folder'] . "'><input class='theswitch' type=\"checkbox\" data-size='mini' checked=\"checked\" name='switch#". $allplugins[$count]['folder'] . "'></div> </a>";
				} else {					
					$allplugins[$count]['active'] = "<div class='switch' id='switch#". $allplugins[$count]['folder'] . "'><input class='theswitch' type=\"checkbox\" data-size='mini' name='switch#". $allplugins[$count]['folder'] . "'></div>";
				}
				
				// Conditions for "install"...
				if ($allplugins[$count]['install'] == 'install') { 
					$allplugins[$count]['install'] = "<a href='" . SITEURL . "admin_index.php?page=plugin_management&amp;action=install&amp;plugin=". $allplugins[$count]['folder'] . "'><i class=\"fa fa-upload\"></i> </a>";
				} else { 
					$allplugins[$count]['install'] = "<a href='" . SITEURL . "admin_index.php?page=plugin_management&amp;action=uninstall&amp;plugin=". $allplugins[$count]['folder'] . "'><i class=\"fa fa-times-circle\"></i> </a>";
				}
				
				// Conditions for "requires"...
				if (isset($plugin_details['requires']) && $plugin_details['requires']) {
					$h->plugin->requires = $plugin_details['requires'];
					$this->requiresToDependencies($h);
					
					// Converts plugin folder names to well formatted names...
					foreach ($h->plugin->dependencies as $this_plugin => $version) {
						$h->plugin->dependencies[$this_plugin] = $version;
						$allplugins[$count]['requires'][$this_plugin] = $h->plugin->dependencies[$this_plugin];
					}
				
				} else {
					$allplugins[$count]['requires'] = array();
				}
				
				// Conditions for "order"...
				// The order is sorted numerically in the plugin_management.php template, so we need separate order and order_output elements.
				if ($allplugins[$count]['order'] != 0) { 
					$order = $allplugins[$count]['order'];
					$allplugins[$count]['order_output'] = "<a href='" . SITEURL;
					$allplugins[$count]['order_output'] .= "admin_index.php?page=plugin_management&amp;";
					$allplugins[$count]['order_output'] .= "action=orderup&amp;plugin=". $allplugins[$count]['folder'];
					$allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
					$allplugins[$count]['order_output'] .= "<i class=\"icon-chevron-up\"></i> ";
					$allplugins[$count]['order_output'] .= "</a> \n&nbsp;<a href='" . SITEURL;
					$allplugins[$count]['order_output'] .= "admin_index.php?page=plugin_management&amp;";
					$allplugins[$count]['order_output'] .= "action=orderdown&amp;plugin=". $allplugins[$count]['folder'];
					$allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
					$allplugins[$count]['order_output'] .= "<i class=\"icon-chevron-down\"></i> ";
					$allplugins[$count]['order_output'] .= "</a>\n";
				} else {
					$allplugins[$count]['order_output'] = "";
				}
			
				$count++;
			}
		}
		return $allplugins;
	}
	
        
        public function getPluginsArray($h, $sort = true)
        {
                $plugins = $this->getPlugins($h);
            
                if (!$plugins) { return false; }
                
                $pluginArray = array();
                
                foreach ($plugins as $plugin) {
                    $pluginArray[] = $plugin['folder'];
                }
                
                if ($sort) {
                    sort($pluginArray);
                }
                
                return $pluginArray;
        }
        
	
	/**
	 * Used by array_filter to keep only installed plugins
	 */
	public function getInstalledPlugins($var)
	{
		if ($var['location'] == 'database') { return $var; }
	}
	
	
	/**
	 * Used by array_filter in template to keep only installed plugins
	 */
	public function getUninstalledPlugins($var)
	{
		if ($var['location'] == 'folder') { return $var; }
	}
	
	
	/**
	 * Read and return plugin info directly from plugin files.
	 */
	public function getPluginsMeta()
	{
		$plugin_list = getFilenames(PLUGINS, "short");
		$plugins_array = array();
		foreach ($plugin_list as $plugin_folder_name) {
			if($plugin_metadata = $this->readPluginMeta($plugin_folder_name)) {
				array_push($plugins_array, $plugin_metadata);
			}
		}
		return $plugins_array; // return plugins in alphabetical order
	}
	
	
	/**
	 * Read and return plugin info from top of a plugin file.
	 *
	 * @param string $plugin_file - a file from the /plugins folder 
	 * @return array|false
	 */
	public function readPluginMeta($plugin_file)
	{
		if ($plugin_file === 'placeholder.txt' || $plugin_file === 'README.md') { return false; }
		
		// Include the generic_pmd class that reads post metadata from the a plugin
		require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');
		$metaReader = new \generic_pmd();
		$plugin_metadata = $metaReader->read(PLUGINS . $plugin_file . "/" . $plugin_file . ".php");
		
		if ($plugin_metadata) { return $plugin_metadata; } else { return false; }
	}
	
	
	/**
	 * Converts $h->plugin->requires into $h->plugin->dependencies array.
	 * Result is an array containing 'plugin' -> 'version' pairs
	 */
	public function requiresToDependencies($h)
	{
		// unset each key from previous time here
		foreach ($h->plugin->dependencies as $k => $v) {
			unset($h->plugin->dependencies[$k]);
		}
		
		foreach (explode(',', $h->plugin->requires) as $pair) {		    
		    $pair_array = explode(' ', trim(strtolower($pair)));
		    $pair_array ? $k = $pair_array[0] : $k=$h->lang("admin_plugins_install_unknown_plugin");
		    count($pair_array) > 1 ? $v = $pair_array[1] : $v=0;		    
		    $h->plugin->dependencies[$k] = $v;
		}
	}
	
	
	/**
	 * Add a plugin to the plugins table
	 *
	 * @param int $upgrade flag to indicate we need to show "Upgraded!" instead of "Installed!" message
	 */
	public function install($h, $upgrade = 0, $clearCache = true)
	{
                if ($clearCache) {
                    // Clear the database cache to ensure stored plugins and hooks 
                    // are up-to-date.
                    $h->deleteFiles(CACHE . 'db_cache');

                    // Clear the css/js cache to ensure any new ones get included
                    $h->deleteFiles(CACHE . 'css_js_cache');

                    // Clear the language cache to ensure any new language files get included
                    $h->clearCache('lang_cache', false);

                    $h->messages['db, css, language caches cleared'] = 'alert-info'; 
                }
            
		// Read meta from the top of the plugin file
		$plugin_metadata = $this->readPluginMeta($h->plugin->folder);
		
                if (!$plugin_metadata) { return false; }
                
		$h->plugin->enabled  = 1;    // Enable it when we add it to the database.
		$this->assignPluginMeta($h, $plugin_metadata);
		
		$dependency_error = 0;
		foreach ($h->plugin->dependencies as $dependency => $version) {
			if (version_compare($version, $h->getPluginVersion($dependency), '>')) {
				$dependency_error = 1;
			}
		}
		
		if ($dependency_error == 1) {
			foreach ($h->plugin->dependencies as $dependency => $version) {
				if (($h->isActive($dependency) == 'inactive') 
					|| version_compare($version, $h->getPluginVersion($dependency), '>')) {
					$dependency = make_name($dependency);
					$h->messages[$h->lang("admin_plugins_install_sorry") . " " . $h->plugin->name . " " . $h->lang("admin_plugins_install_requires") . " " . $dependency . " " . $version] = 'red';
				}
			}
			return false;
		}
		
		// set a new plugin order if NOT upgrading
		if ($upgrade == 0) {
			$sql = "REPLACE INTO " . TABLE_PLUGINS . " (plugin_enabled, plugin_name, plugin_folder, plugin_class, plugin_extends, plugin_type, plugin_desc, plugin_requires, plugin_version, plugin_author, plugin_authorurl, plugin_updateby) VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d)";
			$h->db->query($h->db->prepare($sql, $h->plugin->enabled, $h->plugin->name, $h->plugin->folder, $h->plugin->class, $h->plugin->extends, $h->plugin->type, $h->plugin->desc, $h->plugin->requires, $h->plugin->version, $h->plugin->author, urlencode($h->plugin->authorurl), $h->currentUser->id));
			
			// Get the last order number - doing this after REPLACE INTO because 
			// we don't know whether the above will insert or replace.
			$sql = "SELECT plugin_order FROM " . TABLE_PLUGINS . " ORDER BY plugin_order DESC LIMIT 1";
			$highest_order = $h->db->get_var($h->db->prepare($sql));
			
			// Give the new plugin the order number + 1
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = LAST_INSERT_ID()";
			$h->db->query($h->db->prepare($sql, ($highest_order + 1)));
		} else {
			// upgrading:
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %d, plugin_name = %s, plugin_folder = %s, plugin_class = %s, plugin_extends = %s, plugin_type = %s, plugin_desc = %s, plugin_requires = %s, plugin_version = %s, plugin_author = %s, plugin_authorurl = %s, plugin_updateby = %d WHERE plugin_folder = %s";
			$h->db->query($h->db->prepare($sql, $h->plugin->enabled, $h->plugin->name, $h->plugin->folder, $h->plugin->class, $h->plugin->extends, $h->plugin->type, $h->plugin->desc, $h->plugin->requires, $h->plugin->version, $h->plugin->author, urlencode($h->plugin->authorurl), $h->currentUser->id, $h->plugin->folder));
		}
		
		// Add any plugin hooks to the hooks table
		$this->addPluginHooks($h);
		
		// Force inclusion of a language file (if exists) because the 
		// plugin isn't ready to include it itself yet.
		$h->includeLanguage();
		
		$result = $h->pluginHook('install_plugin', $h->plugin->folder);
		
		// Re-sort all orders and remove any accidental gaps
		$this->refreshPluginOrder($h);
		$this->sortPluginHooks($h);
		
		// For plugins to avoid showing this success message, they need to 
		// return a non-boolean value to $result.
		if (!is_array($result)) {
			if ($upgrade == 0) {
				$h->messages[$h->lang("admin_plugins_install_done")] = 'green';
			} else {
				$h->messages[$h->lang("admin_plugins_upgrade_done")] = 'green';
			}
		}
                
                return true;
	}
	
	
	/**
	 * Assign info from top of a plugin file to the current object.
	 *
	 * @param array $plugin_metadata 
	 * @return array|false
	 */
	public function assignPluginMeta($h, $plugin_metadata)
	{
		if (!$plugin_metadata) { return false; }
		
		$h->plugin->name         = $plugin_metadata['name'];
		$h->plugin->desc         = $plugin_metadata['description'];
		$h->plugin->version      = $plugin_metadata['version'];
		$h->plugin->folder       = $plugin_metadata['folder'];
		$h->plugin->class        = $plugin_metadata['class'];
		
		if (!isset($plugin_metadata['hooks'])) { 
			$h->plugin->hooks = array();
		} else {
			$h->plugin->hooks = explode(',', $plugin_metadata['hooks']);
		}
		
		if (isset($plugin_metadata['extends'])) {   $h->plugin->extends      = $plugin_metadata['extends']; }
		if (isset($plugin_metadata['type'])) {      $h->plugin->type         = $plugin_metadata['type'];    }
		if (isset($plugin_metadata['author'])) {    $h->plugin->author       = $plugin_metadata['author'];  }
		if (isset($plugin_metadata['authorurl'])) { $h->plugin->authorurl    = $plugin_metadata['authorurl']; }
		
		if (isset($plugin_metadata['requires']) && $plugin_metadata['requires']) {
			$h->plugin->requires = $plugin_metadata['requires'];
			$this->requiresToDependencies($h);
		}
		
		return true;
	}
	
	
	/**
	 * Adds all hooks for a given plugin
	 */
	public function addPluginHooks($h)
	{ 
		$values = '';
		$pvalues = array();
		$pvalues[0] = "temp"; // will be filled with $sql

		foreach ($h->plugin->hooks as $hook) {
			$exists = $this->isHook($h, trim($hook));
			
			if (!$exists) {
				$values .= "(%s, %s, %d), ";
				array_push($pvalues, $h->plugin->folder);
				array_push($pvalues, trim($hook));
				array_push($pvalues, $h->currentUser->id);
			}
		}
		
		if ($values) {
			$values = rstrtrim($values, ", "); // strip off trailing comma
			$pvalues[0] = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES " . $values;
			$h->db->query($h->db->prepare($pvalues));
		}
	}
	
	
	/**
	 * Check if a plugin hook exists for a given plugin
	 *
	 * @param string $folder plugin folder name
	 * @param string $hook plugin hook name
	 * @return int|false
	 */
	public function isHook($h, $hook = "", $folder = "")
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		$sql = "SELECT count(phook_id) FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s AND plugin_hook = %s";
		if ($h->db->get_var($h->db->prepare($sql, $folder, $hook))) { return true;} else { return false; }
	}
	
	
	/**
	 * Uninstall all plugins
	 */
	public function uninstallAll($h)
	{
		// Clear the database cache to ensure plugins and hooks are up-to-date.
		$h->deleteFiles(CACHE . 'db_cache');
		
		// Clear the css/js cache to ensure any new ones get included
		$h->deleteFiles(CACHE . 'css_js_cache');
		
		// Clear the language cache to ensure any new language files get included
		$h->clearCache('lang_cache', false);
                
                $h->messages['db, css, language caches cleared'] = 'alert-info'; 
		
		$h->db->query("TRUNCATE TABLE " . TABLE_PLUGINS);
		$h->db->query("TRUNCATE TABLE " . TABLE_PLUGINHOOKS);
		
		$h->messages[$h->lang("admin_plugins_uninstall_all_done")] = 'green';
	}
	
	
	/**
	 * Delete plugin from table_plugins, pluginhooks and pluginsettings
	 *
	 * @param int $upgrade flag to disable message
	 */
	public function uninstall($h, $upgrade = 0, $clearCache = true)
	{
                if ($clearCache) {
                    // Clear the database cache to ensure plugins and hooks are up-to-date.
                    $h->deleteFiles(CACHE . 'db_cache');

                    // Clear the css/js cache to ensure this plugin's files are removed
                    $h->deleteFiles(CACHE . 'css_js_cache');

                    // Clear the language cache to ensure any new language files get included
                    $h->clearCache('lang_cache', false);

                    $h->messages['db, css, language caches cleared'] = 'alert-info'; 
                }
                
		if ($upgrade == 0) { // don't delete plugin when we're upgrading
			$h->db->query($h->db->prepare("DELETE FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $h->plugin->folder));
		}
		
                \Hotaru\Models2\Pluginhook::removeHook($h, $h->plugin->folder);
		//$h->db->query($h->db->prepare("DELETE FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s", $h->plugin->folder));
		
		// Settings aren't deleted anymore, but a user can do so manually from Admin->Maintenance
		//$h->db->query($h->db->prepare("DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s", $h->plugin->folder));
		
		$h->pluginHook('uninstall_plugin', $h->plugin->folder);
		
		if ($upgrade == 0) {
			$h->messages[$h->lang("admin_plugins_uninstall_done")] = 'green';
		}
		
		// Re-sort all orders and remove any accidental gaps
		$this->refreshPluginOrder($h);
		$this->sortPluginHooks($h);
	}
	
	
	/**
	 * Removes gaps in plugin order where plugins have been uninstalled.
	 */
	public function refreshPluginOrder($h)
	{
		$need_refresh = false;
		
		// First, do a quick query and simple loop to check for gaps
		$sql = "SELECT plugin_order FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
		$rows = $h->db->get_results($h->db->prepare($sql));
		if ($rows) { 
                    $previous_row = 0;
                    foreach ($rows as $row) {
                        if ($row->plugin_order != ($previous_row + 1)) { 
                            $need_refresh = true;
                            break;
                        } else {
                            $previous_row = $row->plugin_order; // increment $previous_row
                        }
                    }
		}
		
		if (!$need_refresh) { return true; }
		
		// Gaps found! Refresh all the plugin orders to fill the gaps!
		$sql = "SELECT * FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
		$rows = $h->db->get_results($h->db->prepare($sql));
		
		if ($rows) { 
                    $i = 1;
                    foreach ($rows as $row) {
                        $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
                        $h->db->query($h->db->prepare($sql, $i, $row->plugin_id));
                        $i++; 
                    }
		}
		
		// optimize the table
		$h->db->query("OPTIMIZE TABLE " . TABLE_PLUGINS);
		
		return true;
	}
	
	
	/**
	 * Orders the plugin hooks by plugin_order
	 */
	public function sortPluginHooks($h)
	{
		$sql = "SELECT p.plugin_folder, p.plugin_order, p.plugin_id, h.* FROM " . TABLE_PLUGINHOOKS . " h, " . TABLE_PLUGINS . " p WHERE p.plugin_folder = h.plugin_folder ORDER BY p.plugin_order ASC";
		$rows = $h->db->get_results($h->db->prepare($sql));
		
		// Remove all hooks for this site
		$h->db->query($h->db->prepare("TRUNCATE TABLE " . TABLE_PLUGINHOOKS));
		
		$values = '';
		$pvalues = array();
		$pvalues[0] = "temp"; // will be filled with $sql
		
		// Add plugin hooks back into the hooks table
		if ($rows) {
                    foreach ($rows  as $row) {
                        $values .= "(%s, %s, %d), ";
                        array_push($pvalues, $row->plugin_folder);
                        array_push($pvalues, $row->plugin_hook);
                        array_push($pvalues, $h->currentUser->id);
                    }

                    $values = rstrtrim($values, ", "); // strip off trailing comma
                    $pvalues[0] = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES " . $values;
                    $h->db->query($h->db->prepare($pvalues));
		}
	}

	
	/**
	 * Upgrade plugin
	 *
	 * @param string $folder plugin folder name
	 *
	 * Note: This function does nothing by itself other than read the latest 
	 * file's metadata.
	 */
	public function upgrade($h)
	{
		// Read meta from the top of the plugin file
		$plugin_metadata = $this->readPluginMeta($h->plugin->folder);
		
		$h->plugin->enabled  = 1;    // Enable it when we add it to the database.
		$this->assignPluginMeta($h, $plugin_metadata);
		
		$this->uninstall($h, 1);    // 1 indicates that "upgrade" is true, used to disable the "Uninstalled" message
		$this->install($h, 1);      // 1 indicates that "upgrade" is true.
	}


	/**
	 * Enables or disables a plugin, installing if necessary
	 *
	 * @param int $enabled 
	 * Note: This function does not uninstall/delete a plugin.
	 */
	public function activateDeactivate($h, $newStatus = 0, $clearCache = true)
	{	
                // 0 = deactivate, 1 = activate
		
                if ($clearCache) {
                    // Clear the database cache to ensure plugins and hooks are up-to-date.
                    $h->deleteFiles(CACHE . 'db_cache');

                    // Clear the css/js cache to ensure any new ones get included
                    $h->deleteFiles(CACHE . 'css_js_cache');

                    $h->messages['db, css caches cleared'] = 'alert-info'; 
                }
                
		// Get the enabled status for this plugin...
                //$plugin = \Hotaru\Models\Plugin::getEnabledStatus($h->plugin->folder);
                $plugin = \Hotaru\Models2\Plugin::getEnabledStatus($h, $h->plugin->folder);
		
                // if not installed
		if (!$plugin) {
			// If the user is activating the plugin, go and install it...
                        $result = ($newStatus == 1) ? $this->install($h) : true;
		} else {
			$result = $this->activateDeactivateDo($h, $plugin, $newStatus);
		}
                
                return $result;
	}
	
	
	/**
	 * Enables or disables all plugins, installing if necessary
	 *
	 * @param int $enabled 
	 * Note: This function does not uninstall/delete a plugin.
	 */
	public function activateDeactivateAll($h, $enabled = 0)
	{	
                // 0 = deactivate, 1 = activate
		
                // Clear the database cache to ensure plugins and hooks are up-to-date.
		$h->deleteFiles(CACHE . 'db_cache');
		
		// Clear the css/js cache to ensure any new ones get included
		$h->deleteFiles(CACHE . 'css_js_cache');
                
                $h->messages['db, css caches cleared'] = 'alert-info'; 
                
		// if you want to deactivate, just go ahead and do it:
		if ($enabled == 0) { 
		    $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %d, plugin_updateby = %d";
		    $h->db->query($h->db->prepare($sql, $enabled, $h->currentUser->id));
                    $h->messages[$h->lang("admin_plugins_deactivated")] = 'green'; 
		    return false;
		    //$active_plugins = $this->activePlugins($h->db, '*', 1);
		}

		// if you want to activate, find all the inactive plugins
		if ($enabled == 1) { 
                    $active_plugins = $this->activePlugins($h->db, '*', 0);
                }
		
		if (!$active_plugins) { return false; }
		        
		/*  The problem with upgrading plugins is many of them require other plugins to work, 
			therefore half the plugins can't be upgraded if the upgrade is attempted in a 
			random order. So let's minimize the problem by sorting the plugins by number of 
			requirements, i.e. plugins that have no requirements (Users, Submit, Sidebar Widgets)
			are upgraded first, then plugins with one requirement. */ 
		$i = 0;
		foreach ($active_plugins as $active) {
                    $h->plugin->folder = $active->plugin_folder;
                    $ordered[$i]['name'] = $active->plugin_folder;
                    if (!$active->plugin_requires) { 
                            $ordered[$i]['req_count'] = 0; 
                    } else {
                            $requires = explode(', ', $active->plugin_requires);
                            $ordered[$i]['req_count'] = count($requires);
                    }
                    $i++;
		}
		
		$ordered = sksort($ordered, 'req_count', 'int', true);
		foreach ($ordered as $ord) {
                    $plugin_row = $h->db->get_row($h->db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $ord['name']));
                    $h->plugin->folder = $plugin_row->plugin_folder;
                    $this->activateDeactivateDo($h, $plugin_row, $enabled);
		}
	}
	
	
	/**
	 * Enables or disables all plugins, installing if necessary
	 *
	 * @param int $enabled 
	 * Note: This function does not uninstall/delete a plugin.
	 */
	public function activateDeactivateDo($h, $plugin, $newStatus = 0)
	{	// 0 = deactivate, 1 = activate
		// The plugin is already installed. Activate or deactivate according to $enabled (the user's action).
		if ($plugin->plugin_enabled == $newStatus) { return false; }  // only update if we're changing the enabled value.
 
                //$result = \Hotaru\Models\Plugin::updateEnabled($h->plugin->folder, $newStatus, $h->currentUser->id);
                $result = \Hotaru\Models2\Plugin::updateEnabled($h, $plugin->plugin_folder, $newStatus, $h->currentUser->id);
		
                if ($newStatus == 1) { // Activating now...
		
			// Get plugin version from the database...
                        $db_version = \Hotaru\Models2\Plugin::getVersionNumber($h, $plugin->plugin_folder);
			//$db_version = $h->getPluginVersion($plugin->plugin_folder);
			
			// Get plugin version from the file....
			$plugin_metadata = $this->readPluginMeta($plugin->plugin_folder);
                        if (!$plugin_metadata) { return false; }
			
                        $file_version = $plugin_metadata['version'];

			// If file version is newer the the current plugin version, then upgrade...
			if (version_compare($file_version, $db_version, '>')) {
				$this->upgrade($h); // runs the install function and shows "upgraded!" message instead of "installed".
			} else {
				// else simply show an activated message...
				$h->messages[$h->lang("admin_plugins_activated")] = 'green'; 
			}
			
			// Force inclusion of a language file (if exists) because the 
			// plugin isn't ready to include it itself yet.
			$h->includeLanguage();
		}
		
		if ($newStatus == 0) { 
			$h->messages[$h->lang("admin_plugins_deactivated")] = 'green'; 
		}
		
		//$h->pluginHook('activate_deactivate', '', array('enabled' => $newStatus));
                
                // if save fails then return error
                return true;
	}
	
	
	/**
	 * Get a list of active or inactive plugins (or their descriptions, etc.)
	 *
	 * @param string $select the table column to return
	 * @param int $enabled 0 for inactive, 1 for active
	 * @return array
	 */
	public function activePlugins($db, $select = 'plugin_folder', $enabled = 1)
	{
		$sql = "SELECT $select FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d";
		$active_plugins = $db->get_results($db->prepare($sql, $enabled));
		
		if ($active_plugins) { return $active_plugins; } else {return false; }
	}
	
        
        /**
         * 
         */
        public function pluginReorder($h, $sort = '')
        {
            if (!$sort) { return false; }

            $base_plugins = array('Bookmarking', 'Categories', 'Widgets', 'User Signin', 'Users', 'Gravatar');

            $countBasePlugins = count($base_plugins);
            
            $sql = "SELECT plugin_id, plugin_name FROM " . TABLE_PLUGINS;
            $all_plugins = $h->db->get_results($sql);
            foreach ($all_plugins as $plugin) {
                $pluginIndex[$plugin->plugin_id] = $plugin->plugin_name;
            }
            
            foreach($sort as $p => $id) {
                if (in_array($pluginIndex[$id], $base_plugins)) {
                    // get the order by adding 1 to the key (because the keys start at 0)
                    $order = array_search($pluginIndex[$id], $base_plugins) + 1;
                } else {
                    $order = $p + $countBasePlugins + 1;
                }
                
                $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
                //print $h->db->prepare($sql, $order, $id) . '<br/>';
                $h->db->query($h->db->prepare($sql, $order, $id)); 			
            }   
            
            //refresh cache and sort hooks
            $this->refreshPluginDetails($h);
            $this->sortPluginHooks($h);
            $h->clearCache('db_cache', false);
                
            return true;
        }
	
        
	/**
	 * Updates plugin order and order of their hooks, i.e. changes the order 
	 * of plugins in pluginHook.
	 * 
	 * @param int $order current order
	 * @param string $arrow direction to move
	 */
	public function pluginOrder($h, $order = 0, $arrow = "up")
	{
		if ($order == 0) {
			$h->messages[$h->lang('admin_plugins_order_zero')] = 'red';
			return false;
		}
		
		$this_plugin = $h->getPluginName();
		
		if ($arrow == "up") {
			// get row above
			$sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
			$row_above = $h->db->get_row($h->db->prepare($sql, ($order - 1)));
			
			if (!$row_above) {
				$h->messages[$this_plugin . " " . $h->lang('admin_plugins_order_first')] = 'red';
				return false;
			}
			
			if ($row_above->plugin_order == $order) {
				$h->messages[$h->lang('admin_plugins_order_above')] = 'red';
				return false;
			}

			// update row above 
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
			$h->db->query($h->db->prepare($sql, ($row_above->plugin_order + 1), $row_above->plugin_id)); 
			
			// update current plugin
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
			$h->db->query($h->db->prepare($sql, ($order - 1), $h->plugin->folder));
		} else {
			// get row below
			$sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
			$row_below = $h->db->get_row($h->db->prepare($sql, ($order + 1)));
			
			if (!$row_below) {
				$h->messages[$this_plugin . " " . $h->lang('admin_plugins_order_last')] = 'red';
				return false;
			}
			
			if ($row_below->plugin_order == $order) {
				$h->messages[$h->lang('admin_plugins_order_below')] = 'red';
				return false;
			}
			
			// update row above 
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
			$h->db->query($h->db->prepare($sql, ($row_below->plugin_order - 1), $row_below->plugin_id)); 
			
			// update current plugin
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
			$h->db->query($h->db->prepare($sql, ($order + 1), $h->plugin->folder)); 
		}
		
		$h->messages[$h->lang('admin_plugins_order_updated')] = 'green';
		
		// Re-sort all orders and remove any accidental gaps
		$this->refreshPluginOrder($h);
		$this->sortPluginHooks($h);
		
		return true;
	}
	

	/**
	 * Refresh Plugin Details
	 * Plugin Management updates often happen after $h->allPluginDetails has been filled.
	 * This little hack clears the cached update time and refills $h->allPluginDetails
	 */
	public function refreshPluginDetails($h)
	{
		unset($h->vars['last_updates']['plugins']);
		PluginFunctions::getAllPluginDetails($h);
	}
        
        
        /**
	 * Update Plugins
	 *
	 * @param <type> $h
	 */
	public function update($h)
	{
                $url = "http://forums.hotarucms.org/resources/";
                
		$folder = $h->plugin->folder;
		$version = $h->cage->get->getHtmLawed('versionId');
                $resourceId= $h->cage->get->getHtmLawed('resourceId');
		$findfolder = str_replace('_', '-', $folder);
                $resourceVersionId = $version;
		$version = str_replace('.', '-', $version);		
		
                $resourceFile = $resourceId . "/download?version=" . $resourceVersionId;
                
                // TODO
                // make temp folder the copy directory and unzip files here first
                // copy old folder somewhere and then bring in new one
                // before deleting zip and old folder totally
                //$copydir = CONTENT . "temp/";
                
                $copydir = PLUGINS;
		$file = $findfolder . "-" . $version . ".zip";

                // Create those directories if need be:
	                 
                if (! is_dir($copydir) && ! mkdir($copydir, 0777) )  {	                        
                        $h->messages['Failed to create temp directory.' . $copydir] = 'red';                        
                } else {
                    //if ($h->debug) $h->messages['temp folder located'] = 'alert-info';
                }
	        
		// get ftpsettings
//		$ftpserver = "api.hotarucms.org";
//		$ftppath   = "/public_html/api/content/";
//		$ftpuser   = "hotarorg";
//		$ftppass   = "";
                if ($h->debug) { $h->messages['setting copy directory as ' . $copydir] = 'alert-info'; }

		//$ftp_url = "ftp://" . $ftpuser . ":" . stripslashes($ftppass) . "@" . $ftpserver . $ftppath . $folder . "/"  ;
                //$h->messages[$resourceFile] = 'alert-success';
		// check that we can access the remote plugin repo site via curl
                
                $settings = $h->vars['admin_settings'];
                
                if ($settings) { 
                    foreach ($settings as $ls) {
                        if ($ls->settings_name == 'FORUM_USERNAME' ) { $username = $ls->settings_value; }
                        if ($ls->settings_name == 'FORUM_PASSWORD' ) { $password = $ls->settings_value; }
                    }
                }
                
                if ($this->fileCheckCurlConnection($h, $url, $resourceFile, $username, $password) == 200) {
                    $h->messages['File succesfully located on remote plugin server'] = 'alert-success';
		    if ($write = is_writeable($copydir)) {			
                        //if ($h->debug) $h->messages['we will use php for file copy'] = 'alert-info';
			$this->filePhpWrite($h, $url, $resourceFile, $file, $findfolder, $copydir, $username, $password);
		    } else {			
                        //if ($h->debug) $h->messages['we will use ftp for file copy'] = 'alert-info';
                        $h->messages['ftp copy not operational yet in this version of Hotaru CMS'] = 'red';
			//$this->fileFtpWrite($h, $url, $ftp_url, $file, $findfolder, $copydir);
		    }
		} else {		    
		    $h->messages[$url . $resourceFile . $h->lang('admin_theme_fileexist_error')] = 'red';
		}

		// unzip		
		if (file_exists( $copydir . $file)) {
                    //$h->messages['About to start the unzip process' . $copydir . $file] = 'alert-info';
                    
                    // check chmod
		    if (!$write) { $this->fileFtpChmod($h, $ftp_url, $folder, '777'); }

		    // Should we rename old files first and then bring in new ?

		    $zipResult = $this->fileUnzip($h, $copydir . $file, $copydir);
		    if (!$write) { $this->fileFtpChmod($h, $ftp_url, $folder, '755'); }
                    
                    if ($zipResult == 1) {
                        // only delete zip file if we have been succesful ?
                        //if ($h->debug) $h->messages['About to delete zip file'] = 'alert-info';
                        if ($write) {
                            //print "we can use PHP<br/>";
                            $this->filePhpDelete($h, $file, $copydir);
                        } else {
                            //print "we will use FTP<br/>";
                            $this->fileFtpDelete($h, $ftp_url, $file, $copydir);
                        }
                    } else {
                        $h->messages['not deleting zip file as unzip failed'] = 'red';
                    }
		} else {
		    $h->messages[$h->lang('admin_theme_filecopy_error') . $file] = 'red';
		}
	}

	private function fileCheckCurlConnection($h, $url, $file, $username, $password)
	{
                // create a new CURL resource and login to forum for cookie
                $ch = $h->loginForum($username, $password);
                
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url . $file);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		set_time_limit(30); # 30 seconds for PHP
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); # and also for CURL

		//don't fetch the actual page, you only want to check the connection is ok
		curl_setopt($ch, CURLOPT_NOBODY, true);

		$zipfile = curl_exec($ch);
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		//print 'checking directory is accesible: ' . $statusCode . '<br/>';

		return $statusCode;                                
	}

	public function filePhpWrite($h, $url, $resourceFile, $file, $findfolder, $copydir, $username, $password )
	{	
                // create a new CURL resource and login to forum for cookie
                $ch = $h->loginForum($username, $password);

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url . $resourceFile);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // dont need this because we are using output file method instead
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // need this because forum redirects to the attachement zip

		set_time_limit(300); # 5 minutes for PHP
		curl_setopt($ch, CURLOPT_TIMEOUT, 300); # and also for CURL

		//don't fetch the actual page, you only want to check the connection is ok
		curl_setopt($ch, CURLOPT_NOBODY, true);

		$zipfile = curl_exec($ch);
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($statusCode == 200) {

		    if (is_writeable($copydir)) {
			//reset this from above
			curl_setopt($ch, CURLOPT_NOBODY, false);
			$outfile = @fopen($copydir . $file, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $outfile);
			$handle =base64_encode(curl_exec ($ch));			
			fclose($outfile);
                        		
//print_r(curl_getinfo($ch));
			if ($handle) {
			    $h->messages[$file . $h->lang('admin_theme_filecopy_success')] = 'green';
			} else {
                            $h->messages['something went wrong getting base64 handle'];
                        }
		    } else {
			$h->messages[$h->lang('admin_theme_filecopy_error' . $file)] = 'red';
		    }
		} else {
		    $h->messages[$h->lang('admin_theme_fileexist_error' . $file)] = 'red';
		}
		curl_close($ch);           
	}

	public function fileUnzip($h, $file, $to)
	{
		//$h->messages[$file . $h->lang('admin_theme_filecopy_success')] = 'green';
            //$file = '/home/ipadrank/public_html/content/temp/metatags-0-3.zip';

                $z = new \ZipArchive();
	        $zopen = $z->open($file, \ZIPARCHIVE::CHECKCONS);                           

                //if ($h->debug) $h->messages['Attempt to unzip ' . $file] = 'alert-info';
                
                if ($zopen !== true) {
                        $h->messages['Could not open zip file ' . $file] = 'red';
                        return false;
                }

                // code from Wordpress with much appreciation
                for ( $i = 0; $i < $z->numFiles; $i++ ) {
	                if ( ! $info = $z->statIndex($i) ) {
                            $h->messages['Could not retrieve file from archive.' . $file] = 'red';
	                    return false;
                        }
                       
                        if ( '__MACOSX/' === substr($info['name'], 0, 9) ) // Skip the OS X-created __MACOSX directory
	                        continue;
	
	                if ( '/' == substr($info['name'], -1) ) // directory
	                        $needed_dirs[] = $to . rtrim($info['name'], '/');
	                else
	                        $needed_dirs[] = $to . rtrim(dirname($info['name']),'/');
	        } 
	
	        $needed_dirs = array_unique($needed_dirs);
	        foreach ( $needed_dirs as $dir ) {
	                // Check the parent folders of the folders all exist within the creation array.
	                if ( rtrim($to,'/') == $dir ) // Skip over the working directory, We know this exists (or will exist)
	                        continue;
	                if ( strpos($dir, $to) === false ) // If the directory is not within the working directory, Skip it
	                        continue;
	
	                $parent_folder = dirname($dir);
	                while ( !empty($parent_folder) && rtrim($to,'/') != $parent_folder && !in_array($parent_folder, $needed_dirs) ) {
	                        $needed_dirs[] = $parent_folder;
	                        $parent_folder = dirname($parent_folder);
	                }
	        }
	        asort($needed_dirs);
	
	        // Create those directories if need be:
	        foreach ( $needed_dirs as $dir ) {  
	                if (!is_dir($dir) && !mkdir($dir, 0777))  {// Only check to see if the Dir exists upon creation failure. Less I/O this way.	                        
                                $h->messages['Could not create directory.' . $dir] = 'red';
                                return false;
                        }
	        }
	        unset($needed_dirs);
	
	        for ( $i = 0; $i < $z->numFiles; $i++ ) {
	                if ( ! $info = $z->statIndex($i) ) {
                                $h->messages['Could not retrieve file from archive.'] = 'red';
                                return false;
                        }	                        
	
	                if ( '/' == substr($info['name'], -1) ) // directory
	                        continue;
	
	                if ( '__MACOSX/' === substr($info['name'], 0, 9) ) // Don't extract the OS X-created __MACOSX directory files
	                        continue;
	
	                $contents = $z->getFromIndex($i);
	                if ( false === $contents ) {
                                $h->messages['Could not extract file from archive.' . $info['name']] = 'red';
                                return false;
                        }	                       

	                if ( ! file_put_contents( $to . $info['name'], $contents, 644) ) {
                                $h->messages['Could not copy file.' . $info['filename']] = 'red';
                                return false;
                        }	                        
	        }
                
//		require_once(EXTENSIONS . 'pclZip/pclzip.lib.php');
//		$archive = new PclZip($copydir . $file);
//
//		if (($list = $archive->extract(PCLZIP_OPT_PATH, PLUGINS)) == 0) {
//		    $h->messages[$h->lang('admin_theme_unzip_error'] . $file) = 'red';
//                    return false;
//		}
                                
                $h->messages[$file . $h->lang('admin_theme_unzip_success')] = 'green';
                $z->close();
                
                return true;		
	}

        public function folderDelete($h, $folder)
        {
                @chmod($folder, 666);
		$deleted = @unlink($folder);
		if (!$deleted) {
		    $h->messages['folder could not be deleted before unzipping'] = 'yellow';
		} else {
                    $h->messages['old folder deleted successfully'] = 'green';
                }
        }
        
	public function filePhpDelete($h, $file, $copydir)
	{
		@chmod($copydir . $file,666);
		$deleted = @unlink($copydir . $file);
		if (!$deleted) {
		    $h->messages[$file . $h->lang('admin_theme_zipdelete_error')] = 'yellow';
		}
	}

	public function fileFtpChmod($h, $ftp_url, $folder, $permission)
	{// print "start chmod for " . $ftp_url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ftp_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_POSTQUOTE, array("CHMOD " . $permission .  ' ' . $folder));

		curl_exec($ch);
		if ($error = curl_error($ch)) {
		    // write this to error log
		    echo "<br/>Error: $error<br />\n";
		}
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// 250 comes from FTP error code saying action completed ok
		if (!$statusCode == 250) {
		    print "problem";
		}

		curl_close($ch);
	}

	public function fileFtpDelete($h, $ftp_url, $file, $copydir)
	{	    
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ftp_url . 'plugins/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTQUOTE, array("DELE " .  $file));

		curl_exec($ch);
//		if ($error = curl_error($ch)) {
//		    // write this to error log
//		    echo "<br/>Error: $error<br />\n";
//		}
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// 250 comes from FTP error code saying action completed ok
		if (!$statusCode == 250) {
		    $h->messages[$file . $h->lang('admin_theme_zipdelete_error')] = 'yellow';
		}

		curl_close($ch);
	}

	public function fileFtpWrite($h, $url, $ftp_url, $file, $folder, $copydir)
	{
		$BUFF="";
		$ch = curl_init();

		print "Checking FTP at " . $url . " to get file " . $file. "<br/>";

		curl_setopt($ch, CURLOPT_URL, $url . $file);
		// Set callback function for body
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'read_body'));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

		curl_exec($ch);
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($error = curl_error($ch)) {
		    $h->messages[$h->lang('admin_theme_filecopy_permission_error')] = 'red';
		    echo "Error: $error<br />\n";
		}

		print "<br/><br/>Trying to upload to: " .$ftp_url . 'plugins/' . $file;

		curl_setopt($ch, CURLOPT_URL, $ftp_url . 'plugins/' .$file);
		curl_setopt($ch, CURLOPT_UPLOAD, 1);
		#curl_setopt($ch, CURLOPT_INFILE, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		#curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt ($ch, CURLOPT_READFUNCTION, array($this, 'write_function'));

		// set size of the image, which isn't _mandatory_ but helps libcurl to do
		// extra error checking on the upload.
		#curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));

		curl_exec($ch);
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($error = curl_error($ch)) {
		    $h->messages[$h->lang('admin_theme_filecopy_permission_error')] = 'red';
		    echo "Error: $error<br />\n";
		}

		curl_close($ch);

	    return $error;
	}

	public function write_function($handle, $fd, $length)
	{
	    global $BUF;
	    $l = strlen($BUF);
	    if ( $l > $length ) {
		$part = substr($BUF, 0, $length);
		$BUF = substr($BUF, $length);
	    } else {
		$part = $BUF;
		$BUF = "";
	    }

	    echo "<br/>Sent $l bytes<br/>\n";
	    return $part;
	}

	public function read_body($ch, $string)
	{
	    global $BUF;
	    $length = strlen($string);
	    echo "Received $length bytes<br />\n";
	    $BUF=$BUF.$string;
	    return $length;
	}

        public function versionCheck($h)
	{
		$systeminfo = SystemInfo::instance();
		$result = $systeminfo->plugin_version_getAll($h);
		if ($result) {
		    $h->messages[$h->lang('admin_theme_version_check_completed')] = 'alert-success';
		} else {
                    $h->messages[$h->lang('admin_theme_version_check_failed')] = 'alert-danger';
                }
	}
}
