/* **************************************************************************************************** 
 *  File: /content/admin_themes/admin_default/javascript/admin_default.js
 *  Purpose: JQuery for admin_default
 *  Notes: 
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (c) 2010 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */


jQuery('document').ready(function($) {   
    
    // add warning slash to the settings page for when admins leave slash off end of setting
    $('.warning_slash').blur(function() {
        var value = $(this).val();
        var length = value.length;
        var check = value.substring(length-1,length);        
        var notes = $(this).parent().parent().children('td:eq(3)');
        if (check != '/' ) { notes.addClass('alert-danger'); } else { notes.removeClass('alert-danger');}
    });

    $('#admin_theme_theme_activate').click(function() {        
                var theme = $(this).attr("name");       
                var formdata = 'admin=theme_settings&theme='  + theme;
		var sendurl = SITEURL + "admin_index.php?page=settings";

                $.ajax(
			{
			type: 'post',
				url: sendurl,
				data: formdata,
				beforeSend: function () {
						$('#admin_theme_theme_activate').html('<img src="' + SITEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Attempting to activate theme.<br/>');
					},
				error: 	function(XMLHttpRequest, textStatus, errorThrown) {
						$('#admin_theme_theme_activate').html('ERROR');
                                                $('#admin_theme_theme_activate').removeClass('btn-success').addClass('btn-danger');
				},
				success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
					if (data.error === true) {
                                                $('#admin_theme_theme_activate').removeClass('btn-success').addClass('btn-danger');
					} else {                        
                                                $('#admin_theme_theme_activate').html(data.message);
                                                $('#admin_theme_theme_activate').removeClass('btn-success').addClass('btn-primary');
					}
					$('.message').html(data.message).addClass(data.color, 'visible');
				},
				dataType: "json"
		});
    });
        
    $('#admin_theme_maintenance_openclose_site').click(function() {        
                var action = $(this).attr("name"); 
                var formdata = 'action='  + action;  // close, open
		var sendurl = SITEURL + "admin_index.php?page=maintenance";

                $.ajax(
			{
			type: 'get',
				url: sendurl,
				data: formdata,
				beforeSend: function () {
						$('#admin_theme_maintenance_openclose_site').html('<img src="' + SITEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Attempting to ' + action + ' site.<br/>');
					},
				error: 	function(XMLHttpRequest, textStatus, errorThrown) {
						$('#admin_theme_maintenance_openclose_site').html('ERROR');
                                                $('#admin_theme_maintenance_openclose_site').removeClass('btn-success').removeClass('btn-warning').addClass('btn-danger');
				},
				success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
					if (data.error === true) {
                                                $('#admin_theme_maintenance_openclose_site').removeClass('btn-success').removeClass('btn-warning').addClass('btn-danger');
                                                $('#admin_theme_maintenance_openclose_site').html('Failed to ' + action + ' site');
                                        } else {                        
                                                $('#admin_theme_maintenance_openclose_site').html(data.message);
                                                $('#admin_theme_maintenance_openclose_site').removeClass('btn-success').removeClass('btn-warning').addClass('btn-primary');
                                                $('#admin_theme_maintenance_openclose_site').attr('name', data.name);
					}
					$('.message').html(data.message);
				},
				dataType: "json"
		});
    });
    
    $('#admin_settings_btn_check_password').click(function() {
		var sendurl = SITEURL + "admin_index.php?page=ajax_loginforum";

                $.ajax(
			{
			type: 'get',
				url: sendurl,
				
				beforeSend: function () {
						$('#admin_settings_btn_check_password').html('<img src="' + SITEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Checking password.');
					},
				error: 	function(XMLHttpRequest, textStatus, errorThrown) {
						$('#admin_settings_btn_check_password').html('ERROR');
                                                $('#admin_settings_btn_check_password').removeClass('btn-success').removeClass('btn-warning').addClass('btn-danger');
				},
				success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                                        if (data.error === true) {
                                                $('#admin_settings_btn_check_password').removeClass('btn-success').removeClass('btn-warning').addClass('btn-danger');
                                                $('#admin_settings_btn_check_password').html('Password Failed');
                                        } else {           
                                                $('#admin_settings_btn_check_password').html(data.message);
                                                $('#admin_settings_btn_check_password').removeClass('btn-success').removeClass('btn-danger').removeClass('btn-warning').addClass('btn-success');
                                                
					}
					$('.message').html(data.message);
				},
				dataType: "json"
		});
    });
    
    $('#admin_settings_btn_get_hotaru_api_key').click(function() {
		var sendurl = SITEURL + "admin_index.php?page=ajax_getHotaruApiKey";

                $.ajax(
			{
			type: 'get',
				url: sendurl,
				
				beforeSend: function () {
                                                $('#admin_settings_btn_get_hotaru_api_key').removeClass('btn-success').removeClass('btn-danger').removeClass('btn-warning').addClass('btn-primary');
						$('#admin_settings_btn_get_hotaru_api_key').html('<img src="' + SITEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>&nbsp;Resetting API Key');
					},
				error: 	function(XMLHttpRequest, textStatus, errorThrown) {
						$('#admin_settings_btn_get_hotaru_api_key').html('ERROR');
                                                $('#admin_settings_btn_get_hotaru_api_key').removeClass('btn-success').removeClass('btn-warning').removeClass('btn-primary').addClass('btn-danger');
				},
				success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                                        if (data.error === true) {
                                                $('#admin_settings_btn_get_hotaru_api_key').removeClass('btn-success').removeClass('btn-warning').removeClass('btn-primary').addClass('btn-danger');
                                                $('#admin_settings_btn_get_hotaru_api_key').html('Failed to reset API Key');
                                        } else {           
                                                $('#admin_settings_btn_get_hotaru_api_key').html(data.message);
                                                $('#input_HOTARU_API_KEY').val(data.apiKey);
                                                $('#admin_settings_btn_get_hotaru_api_key').removeClass('btn-success').removeClass('btn-danger').removeClass('btn-primary').removeClass('btn-warning').addClass('btn-success');
                                                
					}
					$('.message').html(data.message);
				},
				dataType: "json"
		});
    });
});	

function doSearch() {
        var q = document.getElementById("q");
        var v = q.value.toLowerCase();
        var t1 = document.getElementsByClassName("table_col_0")[0];
        var t2 = document.getElementsByClassName("table_col_1")[0];
        var rows = t1.getElementsByClassName("table_plugin_item");
        var rows2 = t2.getElementsByClassName("table_plugin_item");
        //var rows = rows1.concat(rows2);
        
        // cant get the merge of objects to work with $.extend so making 2 loops ;(
        var on = 0;
        for ( var i = 0; i < rows.length; i++ ) {
          var fullname = rows[i].getElementsByTagName("td");
          fullname = fullname[0].innerHTML.toLowerCase();
          if ( fullname ) {
              if ( v.length == 0 || (v.length < 2 && fullname.indexOf(v) == 0) || (v.length >= 2 && fullname.indexOf(v) > -1 ) ) {
              rows[i].style.display = "";
              on++;
            } else {
              rows[i].style.display = "none";
            }
          }
        }
        
        var on = 0;
        for ( var i = 0; i < rows2.length; i++ ) {
          var fullname = rows2[i].getElementsByTagName("td");
          fullname = fullname[0].innerHTML.toLowerCase();
          if ( fullname ) {
              if ( v.length == 0 || (v.length < 2 && fullname.indexOf(v) == 0) || (v.length >= 2 && fullname.indexOf(v) > -1 ) ) {
              rows2[i].style.display = "";
              on++;
            } else {
              rows2[i].style.display = "none";
            }
          }
        }
      }


$(function() {
    $("#left-col, #right-col").sortable({
        tolerance: 'pointer',
        containment: '#plugintable_installed',
        cursor: 'move',
        opacity: 0.6, 
        scroll: true,
        scrollSensitivity: 20,
        //handle: '.item h2',
        revert: 'invalid',
        placeholder: 'placeholder',
        forceHelperSize: true,
        connectWith: '#right-col, #left-col',
        update: function(event, ui) {
            var info_left = $('#left-col').sortable("serialize");
            var info_right = $('#right-col').sortable("serialize");
            $.ajax({
                type: "POST",
                url: SITEURL + "admin_index.php?page=plugin_management&action=orderAjax",
                data: info_left + '&' + info_right,
                beforeSend: function () {                                  
                                jQuery("body").css('cursor','progress');
                                $('#left-col').sortable({disabled: true});
                                $('#right-col').sortable({disabled: true});
                                // alert(info_left + '&' + info_right);
                        },
                error: 	function(XMLHttpRequest, textStatus, errorThrown) {  
                                jQuery("body").css('cursor','default');
                                alert('ERROR');      
                                $('#left-col').sortable({disabled: false});
                                $('#right-col').sortable({disabled: false});                                
                },
                success: function(data) { // success means it returned some form of json code to us. may be code with custom error msg                                                                                                                                                   
                                //alert(data);
                                
                                //if comes back reordered then refresh grid?
                                
                                
                                //if comes back failure then revert the reordered col and give error message
                                
                                // allow reordering again
                                jQuery("body").css('cursor','default');
                                $('#left-col').sortable({disabled: false});
                                $('#right-col').sortable({disabled: false});
                                
                },
                dataType: 'html'
          });

        }    
    }).disableSelection();
    
});
