<?php
/**
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_edit.php
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

$category = $h->vars['edit_cat'];
//print_r($category);
?>

<h3>Category: <?php echo $category->category_name; ?></h3>

<div class="col-md-10">
<form role="form">
  <div class="form-group">
    <label for="exampleInputEmail1">Name</label>
    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter name" value="<?php echo $category->category_name; ?>">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Slug</label>
    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Slug" value="<?php echo $category->category_safe_name; ?>">
  </div>
  <div class="form-group">
    <label for="exampleInputFile">Parent</label>
    <select class="form-control" value="<?php echo $category->category_parent; ?>">
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
    </select>
    <p class="help-block">Example block-level help text here.</p>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Description</label>
    <textarea class="form-control" rows="3"></textarea>
  </div>
    <div class="form-group">
    <label for="exampleInputFile">Admin for this category</label>
    <select class="form-control">
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
    </select>
    <p class="help-block">Example block-level help text here.</p>
  </div>
  <div class="checkbox">
    <label>
      <input type="checkbox"> Active for Submit
    </label>
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>