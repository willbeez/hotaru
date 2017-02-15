<?php
/**
 * User Profile
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

$logins = $h->vars['logins'];

?>

<div id="logins" class="col-md-9">
    <?php $h->showMessages(); ?>
   <form method="post">
    <?php 
    if ($logins) {
        foreach ($logins as $login) { ?>
            <div class="row">
                <div class="col-md-8">
                    <span class="label label-default"><?php echo $login->login_provider; ?></span>&nbsp;&nbsp;
                    <?php echo $login->created_at; ?>
                </div>
                <div class="col-sm-4">
                    
                        <button type="submit" name="key" value="<?php echo $login->provider_key; ?>" class="btn btn-xs btn-danger"><i class="fa fa-ban"></i></button>
                    
                </div>
            </div>
        <?php } 
    } else {
        print "no logins recorded";
    }
?>
    </form>
</div>
