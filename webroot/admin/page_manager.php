<?php
// +------------------------------------------------------------------------+
// | PHP Melody version 1.7 ( www.96down.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.96down.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@96down.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.96down.com) / support@96down.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = 'mod_pages';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'Create new page';

$action = $_GET['do'];
if ( ! in_array($action, array('edit', 'new', 'delete')) )
{
	$action = 'new';	//	default action
}
if ($action == 'edit')
{
	$_page_title = 'Edit page';
}
include('header.php');

if ( ! function_exists('pre_post_filter'))
{
	require_once(ABSPATH .'include/article_functions.php');
}
?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>After choosing a title for your page you can use the WYSIWYG editor to design your content. Images can be uploaded as needed using the right hand side button contained within the editor. We mention this because on lower resolutions, such as those found on notebooks, the button might not appear.</p>
            <p>Pages can be saved as drafts and remain unpublished by choosing the right &quot;Status&quot; option.<br />
            Permalink simply indicates how the URL will look in the address bar. You will see a live preview below the input form.<br />
            The meta keywords and description fields are useful for SEO purposes.</p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
    <a href="#" id="show-help-assist">Help</a>
    <h2>Create New Page</h2>
    <div id="display_result" style="display:none;"></div>

<?php 

$inputs = array();

if ('' != $_POST['submit'])
{
	$_POST['page_title'] = after_post_filter($_POST['page_title']);
	$_POST['title'] = $_POST['page_title'];
	
	if ($action == 'new')
	{
		$result = insert_new_page($_POST);
	}
	else if ($action == 'edit')
	{
		$result = update_page($_POST['id'], $_POST);
	}
	
	if ($result['type'] == 'error')
	{
		echo '<div class="alert alert-error"><strong>'. $result['msg'] .'</strong></div>';
	}
	else
	{
		echo '<div class="alert alert-success"><strong>'. $result['msg'] .' <a href="'. _URL .'/page.php?p='. $result['page_id'].'" target="_blank">See how it looks.</a></strong></div>';
	}	
}

if ($action == 'edit')
{
	$id = (int) $_GET['id'];
	if ($id == 0)
	{
		$action = 'new';
		$inputs = array();
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
	}
	else
	{
		$inputs = get_page($id);
	}
}
else if ($action == 'new')
{
	if ('' != $_POST['submit'])
	{
		$inputs = $_POST;
	}
}

//	Filter some fields before output
$inputs['title'] = pre_post_filter($inputs['title']);

?>

 <form name="write_page" method="post" action="page_manager.php?do=<?php echo $action; ?>&id=<?php echo $_GET['id'];?>" 
   onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted).')">


<div class="container row-fluid" id="post-page">
    <div class="span9">
    <div class="widget border-radius4 shadow-div">
    <h4>Title &amp; Description</h4>
    <div class="control-group">
	<input name="page_title" type="text" id="must" value="<?php echo $inputs['title']; ?>" style="width: 99%;" />
    <div class="controls">
    </div>
    </div>
    
    <div class="control-group">
	<div class="pull-right" style=" position: absolute; top: -2px; right: 0px;">
	<span class="btn btn-mini btn-upload"><span id="ButtonPlaceHolder"></span></span>
    <small><div id="fsUploadProgress"></div></small>
    <div id="divStatus"></div>
	<ol id="uploadLog"></ol>
    </div>
	<div class="clear"></div>
    <div class="controls">
    <textarea name="content" cols="100" id="textarea-WYSIWYG" class="tinymce" style="width:100%"><?php echo $inputs['content']; ?></textarea>
    <span class="autosave-message">&nbsp;</span>
    </div>
    </div>
    </div>
    
    </div><!-- .span8 -->

    <div class="span3">

    <div class="widget border-radius4 shadow-div">
    <h4>Publish</h4>
        <div class="control-group">
        <label class="control-label" for="">Status: <span id="value-register"><strong><?php if ($inputs['status'] == '0') { echo 'draft'; } else { echo 'published'; } ?></strong></span> <a href="#" id="show-visibility">Edit</a></label>
        <div class="controls" id="show-opt-visibility">
			<label class="checkbox inline"><input type="radio" name="status" id="restricted" value="1" <?php if ($inputs['status'] == '1' || !$inputs['status']) echo 'checked="checked"'; ?> /> Publish</label>
            <label class="checkbox inline"><input type="radio" name="status" id="restricted" value="0" <?php if ($inputs['status'] == '0') echo 'checked="checked"'; ?> /> Draft</label> 
        </div>
        </div>
        <div class="control-group">
        <label class="control-label" for="">Show page in header menu: <span id="value-showinmenu"><strong><?php if ($inputs['showinmenu'] == '0') { echo 'no'; } else { echo 'yes'; } ?></strong></span> <a href="#" id="show-showinmenu">Edit</a></label>
        <div class="controls" id="show-opt-showinmenu">
			<label class="checkbox inline"><input type="radio" name="showinmenu" id="show_in_menu" value="1" <?php if ($inputs['showinmenu'] == '1' || !$inputs['showinmenu']) echo 'checked="checked"'; ?> /> Yes</label>
            <label class="checkbox inline"><input type="radio" name="showinmenu" id="show_in_menu" value="0" <?php if ($inputs['showinmenu'] == '0') echo 'checked="checked"'; ?> /> No</label> 
        </div>
        </div>
    </div><!-- .widget -->
    
    <?php if ($inputs['page_name'] == '404') : ?>
    <input name="page_name" id="item-slug" type="hidden" value="404" />
	<?php else : ?>
    <div class="widget border-radius4 shadow-div">
    <h4>Permalink <i class="icon-info-sign" rel="tooltip" title="Define how the URL will look in your address bar. No need to include an extension (.html)."></i></h4>
        <div class="control-group">
        <div class="controls">
        <input name="page_name" id="item-slug" type="text" class="default span12" value="<?php echo $inputs['page_name']; ?>" size="50" style="width:95%" />
            
            <div id="preview_url" class="small-ok">
            <?php 
                if(_SEOMOD == 1) 
                {
            ?>
                 <small>Live preview: <?php echo _URL."/pages/"; ?><span id="preview_complete_url"><?php echo ($inputs['page_name'] != '') ? $inputs['page_name'] : '';?></span>.html</small>
            <?php
                } else {
            ?>
                 <small>Live preview: <?php echo _URL."/page.php?name="; ?><span id="preview_complete_url"></span></small>
            <?php			
                }
            ?>
            </div>
            <?php
            if ($action == 'edit' && $inputs['page_name'] != '' && _SEOMOD)
            {
              echo '<br /><small>Updating this field will have an impact on SEO for pages already indexed</small>';
            }
            ?>
        </div>
        </div>
    </div><!-- .widget -->
    <?php endif; ?>
    
    <div class="widget border-radius4 shadow-div">
    <h4>Meta Keywords</h4>
        <div class="control-group">
        <div class="controls">
            <div class="tagsinput" style="width: 100%;">
            <input name="meta_keywords" type="text" value="<?php echo $inputs['meta_keywords']; ?>" id="tags_addvideo_1" size="50" />
            </div>
        </div>
        </div>
    </div><!-- .widget -->

    <div class="widget border-radius4 shadow-div">
    <h4>Meta Description</h4>
        <div class="control-group">
        <div class="controls">
			<textarea name="meta_description" rows="1" style="width:95%" /><?php echo $inputs['meta_description']; ?></textarea>
        </div>
        </div>
    </div><!-- .widget -->

    </div>
</div>
<div class="clearfix"></div>

<input type="hidden" name="author" value="<?php  echo $inputs['author'];?>" />
<input type="hidden" name="id" value="<?php echo $inputs['id'];?>" />


<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
    	<button name="cancel" type="button" value="Cancel" onClick="location.href='pages.php'" class="btn btn-small btn-normal btn-strong">Cancel</button>
	</div>
    <div class="btn-group">
    	<button name="submit" type="submit" <?php echo ($action == 'edit') ? 'value="Save"' : 'value="Publish"';?> class="btn btn-small btn-success btn-strong"><?php echo ($action == 'edit') ? 'Save' : 'Publish';?></button>
    </div>
</div>
</div><!-- #list-controls -->
    
</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>