<?php 
/**
 * Theme name: admin_default
 * Template name: header.php
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
 * @author    shibuya246 <admin@hotarucms.org>
 * @copyright Copyright (c) 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>
<?php header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">

        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
        <title><?php echo $h->getTitle(); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        
        <!-- Theme -->
        <?php $h->getThemeCss(); ?>
        <link rel='stylesheet' href='<?php echo SITEURL; ?>libs/frameworks/bootstrap-switch/css/bootstrap-switch.min.css' type='text/css'>
        <link rel='stylesheet' href='<?php echo SITEURL; ?>libs/extensions/superbox/style.css' type='text/css'>
        
        <!-- include this CSS last so it gets priority -->
        <?php $h->getFramework('bootstrap3'); ?>		
           
        <!-- Include merged files for all the plugin css and javascript (if any) -->
        <?php $h->doIncludes('css'); ?>		
        <script type='text/javascript' src='<?php echo SITEURL; ?>libs/frameworks/bootstrap-switch/js/bootstrap-switch.min.js'></script>
	<script type='text/javascript' src='<?php echo SITEURL; ?>libs/extensions/flotcharts/jquery.flot.min.js'></script>
        <script type='text/javascript' src='<?php echo SITEURL; ?>libs/extensions/flotcharts/jquery.flot.pie.min.js'></script>
        <script type='text/javascript' src='<?php echo SITEURL; ?>libs/extensions/superbox/superbox.min.js'></script>
   
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
	
	<?php $h->pluginHook('admin_header_include_raw'); ?>

</head>

<body>

    <div id="wrap">   
        
        <?php if ($h->currentUser->adminAccess) {
            echo $h->template('admin_navigation');
         } 
        