<?php
/**
 * name: Submit No Links
 * description: Removes requirement to submit a link
 * version: 0.2
 * folder: submit_no_links
 * class: SubmitNoLinks
 * requires: submit 2.4
 * extends: Submit
 * hooks: theme_index_top
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

require_once(PLUGINS . 'submit/submit.php');

class SubmitNoLinks extends Submit
{
    /**
     * Determine the submission step and perform necessary actions
     */
    public function checkSubmitStep($h)
    {
        // get functions
        $funcs = new SubmitFunctions();

        switch ($h->pageName)
        {
            // SUBMIT STEP 1
            case 'submit':
            case 'submit1':
                // set properties
                $h->pageName = 'submit1';
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_step1"];
                
                $h->vars['submitted_data']['submit_orig_url'] = '';
                $h->vars['submitted_data']['submit_editorial'] = true;
                
                $key = $funcs->saveSubmitData($h);
                $redirect = htmlspecialchars_decode($h->url(array('page'=>'submit2', 'key'=>$key)));
                header("Location: " . $redirect);
                exit;
                break;
                
            // SUBMIT STEP 2 
            case 'submit2':
                // set properties
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_step2"];
                $this->doSubmit2($h, $funcs);
                break;
                
            // SUBMIT STEP 3
            case 'submit3':
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_step3"];
                $this->doSubmit3($h, $funcs);
                break;
                
            // SUBMIT CONFIRM
            case 'submit_confirm':
                $this->doSubmitConfirm($h, $funcs);
                break;
                
            // EDIT POST (after submission)
            case 'edit_post':
                $h->pageType = 'submit';
                $h->pageTitle = $h->lang["submit_edit_title"];
                $this->doSubmitEdit($h, $funcs);
                break;
        }
    }
}
?>
