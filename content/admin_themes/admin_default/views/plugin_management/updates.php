<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


    // for some reason, even though we have passed the array to here it gets lost somehow in the include,so we have to get it again
    $the_plugins = isset($h->vars['installed_plugins']) ? $h->vars['installed_plugins'] : array();
        
?>

<p> 
    You can download the latest versions of plugins here if your server permissions line up nicely with the web.<br/>
    <strong>Note:</strong> Servers without SuExec enabled may not work. Future versions will additionally include FTP access to solve this.
</p>

<table class="table table-bordered">
    <tr class="info">
        <td>Plugin</td>
        <td>Installed</td>
        <td>Latest</td>
        <td>Update</td>
    </tr>
    
    <?php 
    if ($the_plugins) {
        foreach ($the_plugins as $plugin) {             
            if (isset($plugin['latestversion']) && $plugin['latestversion'] > $plugin['version'])
                { 
                $href= SITEURL . "admin_index.php?page=plugin_management&action=update&plugin=" . strtolower($plugin['folder']) . "&resourceId=" . $plugin['resourceId'] . "&versionId=" . $plugin['resourceVersionId'] . "#tab_updates";
                ?>
                    <tr>
                        <td><?php echo $plugin['name']; ?></td>
                        <td><?php echo $plugin['version']; ?></td>
                        <td><?php echo $plugin['latestversion']; ?></td>
                        <td><a href="<?php echo $href; ?>" class="btn btn-xs btn-primary">Update</button></a></td>
                    </tr>
                <?php } ?>
        <?php } 
    }
    ?>
</table>
