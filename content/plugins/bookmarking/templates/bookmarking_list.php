<?php
/**
 * Template for bookmarking plugin: bookmarking_list
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

$i=0;
foreach ($h->vars['pagedResults']->items as $post) {
    $h->readPost(0, $post);
    $i++;
?>

<?php
    
    if ($i % 6 == 0 )
    {?>
    <div style="margin-left:60px;margin-bottom:30px;margin-top:-10px;">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- narrow -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:728px;height:90px"
             data-ad-client="ca-pub-5634143173853305"
             data-ad-slot="1346783428"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    <?php
    }
    ?>

    <!-- POST -->
    <?php $h->pluginHook('pre_show_post'); ?>
    <div class="show_post vote_button_space media" id="show_post_<?php echo $h->post->id; ?>" >
        
        <?php $h->pluginHook('show_post_pre_title'); ?>
        <div class="media-body">
            <div class="show_post_title">
                <span class="hidden-xs ">
                    <?php   // Show avatars if enabled (requires an avatars plugin)
                        if($h->isActive('avatar')) {
                            $h->setAvatar($h->post->author, 36, 'g', 'img-circle', $h->post->email, $h->post->authorname);
                            echo $h->wrapAvatar();
                        }
                    ?>    
                </span>
                <?php if ($h->vars['link_action'] == 'source') { 
                    echo "<a href=' ". $h->post->origUrl ."' " . $h->vars['target'] ." class='click_to_source' rel='nofollow'>" . html_entity_decode($h->post->title) . "</a>";
                 } else { 
                    echo "<a href='" . $h->url(array('page'=>$h->post->id)) ."' " . $h->vars['target'] . " class='click_to_post'>" . html_entity_decode($h->post->title) . "</a>";
                 } ?>
                
                <?php $h->pluginHook('show_post_title'); ?>
            </div> 
        
            <div class="show_post_author_date">    
                <?php //echo " " . $h->lang["bookmarking_post_posted_by"] . " "; ?>
                <li class="fa fa-user"></li>
                <?php 
                if ($h->post->authorname) {
                    echo "<a href='" . $h->url(array('user' => $h->post->authorname)) . "'>" . $h->post->authorname . "</a>";
                } else {
                    echo $h->lang['main_anonymous'];
                }
                ?>

                <li class="fa fa-clock-o"></li>
                <?php echo time_difference(unixtimestamp($h->post->date), $h->lang) . " " . $h->lang["bookmarking_post_ago"]; ?>
                
                <?php $h->pluginHook('show_post_author_date'); ?>
                
            </div>
        
            <?php if ($h->vars['use_content']) { ?>
            <div class="show_post_content">
                <?php $h->pluginHook('show_post_content_list'); ?>
                <?php if ($h->vars['use_summary']) { ?>
                    <?php echo truncate($h->post->content, $h->vars['summary_length']); ?>
                <?php } else { ?>
                    <?php echo $h->post->content; ?>
                <?php } ?>    
                <small><a href='<?php echo $h->url(array('page'=>$h->post->id)); ?>'><?php echo $h->lang['bookmarking_post_read_more']; ?></a></small>
            </div>
            <?php } ?>	
            
            <div class="show_post_extra_fields">
                <ul class="list-inline">
                    <?php $h->pluginHook('show_post_extra_fields'); ?>
                
                    <?php
                        if (1==0 && $h->currentUser->isAdmin) { ?>
                            <!-- Split button -->
                            <div class="btn-group">
                              <a type="button" class="btn btn-xs btn-default" href="<?php echo $h->url(array('page'=>'edit_post', 'post_id'=>$h->post->id)); ?>"><i class='fa fa-edit'></i> <?php echo $h->lang("bookmarking_post_edit"); ?></a>
                              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <ul class="dropdown-menu" role="menu">
                                  <li><a href="<?php echo  BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=post_manager&amp;post_id=" . $h->post->id;?>">Post Manager</a></li>
                                  <li><a href="<?php echo $h->url(array('page'=>'edit_post', 'post_id'=>$h->post->id, 'action'=>'delete')); ?>"><i class='fa fa-ban'></i> Delete</a></li>
                              </ul>
                            </div>
                        <?php }
                     
                        if ($h->currentUser->getPermission('can_edit_posts') == 'yes'
                            || (($h->currentUser->getPermission('can_edit_posts') == 'own') && ($h->currentUser->id == $h->post->author))) { 
                            echo "<li class=''><a class='show_post_edit btn btn-xs btn-default' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $h->post->id . "'><i class='fa fa-edit'></i> " . $h->lang("bookmarking_post_edit") . "</a></li>"; 
                        }
    //                    if ($h->currentUser->getPermission('can_delete_posts') == 'yes'
    //                        || ($h->currentUser->getPermission('can_delete_posts') == 'own' && $h->currentUser->id == $h->post->author)) { 
    //                            echo "&nbsp;<a class='show_post_delete btn btn-xs btn-danger' href='" . BASEURL . "index.php?page=delete__post&amp;post_id=" . $h->post->id . "'>" . $h->lang("bookmarking_post_delete") . "</a>"; 
    //                     
    //                    }
                    ?> 
                </ul>
            </div>

            <div class="show_post_extras">
                <ul>
                    <?php $h->pluginHook('show_post_extras'); ?>
                </ul>
                
            </div>
        </div>
            
    </div>
    
    <div class="clear"></div>

    <!-- END POST --> 

<?php } ?>
