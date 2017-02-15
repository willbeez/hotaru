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
 
$username = $h->displayUser->name;
$userId = $h->displayUser->id;
$imageFolder = BASE . '/content/images/user/' . $userId . '/';

// make folder if does not exist
if(!is_dir($imageFolder)) {
    mkdir($imageFolder, 0777, true);
}

// check if we have profile pix for user. If not use default
$imageId = rand(1, 2);

if (file_exists($imageFolder . 'filename.jpg')) {
    $fileUrl = BASEURL . 'content/images/user/' . $userId . '';
} else {
    $fileUrl = BASEURL . 'content/images/user/default/profile-pix' . $imageId . '.jpg';
}

// determine permissions
$denied = false;
$admin = $h->currentUser->getPermission('can_access_admin') == 'yes' ? 1 : 0;
$own = $h->currentUser->id == $userId ? true : false;
?> 

<?php if (1==0) { ?>
<div class="panel">
    <div class="panel-bg-cover">
            <img class="img-responsive" src="<?php echo $fileUrl;?>" alt="Image">
    </div>
    <div class="panel-media">
            <?php
                if ($h->isActive('avatar')) {
                       //echo "<div id='profile_avatar'>";
                       $h->setAvatar($userId, 96, 'g', 'img-circle');
                       echo $h->linkAvatar();
                       //echo "</div>";
               } ?>
            <img src="img/av1.png" class="panel-media-img img-circle img-border-light" alt="Profile Picture">
            <div class="row">
                    <div class="col-lg-7">
                            <h4 class="panel-media-heading"><?php echo $username; ?></h4>
                            <a href="#" class="btn-link">@user</a>
                            <p class="text-muted mar-btm">Designer</p>
                    </div>
                    <div class="col-lg-5 text-lg-right">
                            <button class="btn btn-sm btn-primary">Add Friend</button>
                            <button class="btn btn-sm btn-mint btn-icon fa fa-envelope icon-lg"></button>
                    </div>
            </div>
    </div>
    <div class="panel-body">
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper
    </div>
</div>
<?php } ?>

<div class="">
    <div id="userProfilePixBox">
	
	<div class="profile_pix">
	    <img style="width:100%;" title="<?php echo $username; ?>"  src="<?php echo $fileUrl;?>" alt="userPix">	    
	</div>
	
	<div id="profileAvatarOverlay" style="position:absolute;">
	    <?php
                if ($h->isActive('avatar')) {
                       echo "<div id='profile_avatar'>";
                       $h->setAvatar($userId, 140, 'g', 'img-polaroid');
                       echo $h->linkAvatar();
                       echo "</div>";
               } ?>
        </div>	
	    	
    </div>
    <div class="clear">&nbsp;</div>
    
	<div id="user_profile_navigation"class="mainBox">
	    <span class="profileBox pull-left">
                <h3><?php echo ucfirst($username); ?></h3>
            </span>
            <span class="pull-right" style="padding:15px;">
                <ul class="nav nav-pills">
                    <?php $h->pluginHook('profile_action_buttons'); ?>
                </ul>
            </span>	    
	</div>

    <div class="clear">&nbsp;</div>
    
</div>

<?php if (isset($h->vars['theme_settings']['userProfile_tabs']) && $h->vars['theme_settings']['userProfile_tabs']) { ?>
<div class="profile_navigation2 tabbable tabs-below">
 
    <ul class="nav nav-tabs">        
        <li class="active"><a href='#profile' data-toggle='tab'><?php echo $h->lang["users_profile"]; ?></a></li>
        <?php $h->pluginHook('profile_navigation'); ?>
                        
    <?php // show account and profile links to owner or admin access users: 
        if ($own) { ?>

            <li><a href='#account' data-toggle='tab'><?php echo $h->lang["users_account"]; ?></a></li>
            <li><a href='#editProfile' data-toggle='tab'><?php echo $h->lang["users_profile_edit"]; ?></a></li>
            <li><a href='#settings' data-toggle='tab'><?php echo $h->lang["users_settings"]; ?></a></li>

    <?php } ?>
    
    </ul> 
    
    <div class="tab-content">
        <div class="tab-pane active" id="profile">
            <?php echo $h->template('users_profile'); ?>
        </div>
                
        <?php $h->pluginHook('profile_content'); ?>
        
        <?php if ($admin || $own) { ?>
        <div class="tab-pane" id="account">
            <?php echo $h->template('users_account'); ?>
        </div>
        
        <div class="tab-pane" id="editProfile">
            <?php echo $h->template('users_edit_profile'); ?>
        </div>
        
        <div class="tab-pane" id="settings">
            <?php echo $h->template('users_settings'); ?>
        </div>
        <?php } ?>
    </div>
     </div> 
<?php } else {
     ?>
    
    <div class="profile-navigation col-md-3">
        <ul class="list-group">        
            <li><a href='<?php echo $h->url(array('page'=>'profile', 'user'=>$username)) ?>'><?php echo $h->lang["users_profile"]; ?></a></li>

            <?php $h->pluginHook('profile_navigation'); ?>

            <?php // show account and profile links to owner or admin access users: 
            if ($own) { ?>
                <li><a href='<?php echo $h->url(array('page'=>'account', 'user'=>$username)); ?>'><?php echo $h->lang["users_account"]; ?></a></li>
                <li><a href='<?php echo $h->url(array('page'=>'user-logins', 'user'=>$username)); ?>'><?php echo $h->lang("users_logins"); ?></a></li>
                <li><a href='<?php echo $h->url(array('page'=>'user-settings', 'user'=>$username)); ?>'><?php echo $h->lang["users_settings"]; ?></a></li>
            <?php } ?>
        </ul>
    </div>
          
<?php    
}
