<?php
/**
 * Install function for the Hotaru CMS installer.
 * 
 * Steps through the set-up process, creating database tables and registering 
 * the Admin user. Note: You must delete this file after installation as it 
 * poses a serious security risk if left.
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


/**
 * Initialize Database
 *
 * @return object
 */
function init_database()
{
	$ezSQL = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
	$ezSQL->query("SET NAMES 'utf8'");
	
	return $ezSQL;
}
    
    
/**
 * Initialize Inspekt
 *
 * @return object
 */
function init_inspekt_cage()
{
	$cage = Inspekt::makeSuperCage(); 
	
	// Add Hotaru custom methods
	$cage->addAccessor('testAlnumLines');
	$cage->addAccessor('testPage');
	$cage->addAccessor('testUsername');
	$cage->addAccessor('testPassword');
	$cage->addAccessor('getFriendlyUrl');
	$cage->addAccessor('sanitizeAll');
	$cage->addAccessor('sanitizeTags');
	$cage->addAccessor('getHtmLawed');
	
	return $cage;
}


/**
 * Delete plugin database table
 *
 * @param string $table_name - table to drop
 */
function drop_table($table_name)
{
	global $db;
	
	$db->query("DROP TABLE " . $table_name);
}


function urlLang($h)
{
        $pageURL = 'http';
        if ($h->cage->server->getAlpha("HTTPS") == "on") {$pageURL .= "s";}
        $pageURL .= "://";

        $serverName = $h->cage->server->getRaw("SERVER_NAME");
        $requestUri = $h->cage->server->getRaw("REQUEST_URI");
        $port = $h->cage->server->getAlpha("SERVER_PORT");
        
        if ($port != "80") {
            $pageURL .= $serverName.":".$port.$requestUri;
        } else {
            $pageURL .= $serverName.$requestUri;
        }
        
        if (strpos($pageURL, '?') !== false) {
                list($base, $query) = explode('?', $pageURL, 2);
                $base .= '?';
        } else {
                $base = $pageURL . '?';
                $query = '';
        }
        
        $args = explode('&', $query);
        $newQuery = '';
        if (is_array($args)) {
                foreach ($args as $arg) {
                    $parts = explode('=', $arg);
                    if ($parts[0] != 'lang') {
                        $newQuery .= $arg . '&';
                    }
                }
        }
        
        $newQuery .= 'lang=';
        $resultUrl = $base . $newQuery;
 
        return $resultUrl;
}


/*
 * function for calling templates with header and footer
 * 
 */
function template($h, $template, $args = array())
{
        global $lang;
        global $currentLang;
        global $action;

        // check for any vars being passed in
        extract($args);

        include_once('templates/header.php');

        include_once('templates/' . $template);

        include_once('templates/footer.php');
}

/**
 * Step 1 of upgrade - checks existing version available and confirms details
 */
function upgrade_check($h, $old_version, $show_next)         
{               
        // delete existing cache
        $h->deleteFiles(CACHE . 'db_cache');
        $h->deleteFiles(CACHE . 'css_js_cache');
        $h->deleteFiles(CACHE . 'rss_cache');
        $h->deleteFiles(CACHE . 'lang_cache');
        $h->deleteFiles(CACHE . 'html_cache');

        template($h, 'upgrade/upgrade_step_1.php', array(
            'old_version' => $old_version,
            'show_next' => $show_next
        ));	
}

function createCacheFolders()
{
        $dirs = array('debug_logs/' , 'db_cache/', 'css_js_cache/', 'html_cache/', 'rss_cache/', 'lang_cache/'); 

	foreach ($dirs as $dir) {
	    if (!is_dir(CACHE . $dir)) {
		mkdir(CACHE . $dir);
	    }
	}
}
