<?php
$showm = 'mod_article';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'افزودن خبر جدید';

$action = $_GET['do'];
if ( ! in_array($action, array('edit', 'new', 'delete')) )
{
	$action = 'new';	//	default action
}

if ($action == 'edit')
{
	$_page_title = 'ویرایش مقاله';
}
include('header.php');

?>
<script type="text/javascript">
$(document).ready(function(){
	$("img[name='article_thumbnail']").click(function() {
		var img = $(this);
		var ul = img.parents('.thumbs_ul');
		var li = img.parent();
		var input = $("input[name='post_thumb_show']");
		
		if ( ! li.hasClass('art-thumb-selected'))
		{
			ul.children().removeClass('art-thumb-selected').addClass('art-thumb-default');
			li.addClass('art-thumb-selected');
			input.val(img.attr('src'));
		}
	});
});
</script>

<div id="adminPrimary">
    <div class="content">
		<?php if ($action == 'edit') : ?>
		<h2>ویراش کردن مقاله</h2>
		<?php else : ?>
		<h2>مقاله جدید بفرست</h2>
		<?php endif; ?>
		<div id="display_result" style="display:none;"></div>

<?php
if ( ! $config['mod_article'])
{
  ?>
   <div class="alert alert-info">
	ماژول مقالات هم اکنون غیرفعال شده است. لطفا آنرا از '<a href="settings.php">تنظیمات</a>فعال کنید / ماژول های در دسترس'.
   </div>
  </div>
  <?php
  include('footer.php');
  exit();
}

$inputs = array();

if ('' != $_POST['submit'])
{
	$_POST['title'] = after_post_filter($_POST['title']);
	$_POST['tags'] = after_post_filter($_POST['tags']);
	
	if ($action == 'new')
	{
        $modframework->trigger_hook('admin_article_insert_before');
		$result = insert_new_article($_POST);
        $modframework->trigger_hook('admin_article_insert_after');
	}
	else if ($action == 'edit')
	{
        $modframework->trigger_hook('admin_article_update_before');
		$result = update_article($_POST['id'], $_POST);
        $modframework->trigger_hook('admin_article_update_after');
	}
	
	if ($result['type'] == 'error')
	{
		echo '<div class="alert alert-error"><strong>'. $result['msg'] .'</strong></div>';
	}
	else
	{
		if ($action == 'new')
		{
			echo '<div class="alert alert-success"><strong>'. $result['msg'] .'.</strong> <a href="'. _URL .'/article_read.php?a='.$result['article_id'].'&mode=preview" target="_blank" title="View article">ببین چه طوری به نظر میرسه!</a></div>';

			echo '<input name="continue" type="button" value="&larr; مدیریت کردن مقالات" onClick="location.href=\'articles.php\'" class="btn" /> ';
			echo ' <input name="add_new" type="button" value="مقاله جدیدی بفرست &rarr;" onClick="location.href=\'article_manager.php?do=new\'" class="btn btn-success" />';
			echo '</div></div>';
			
			include('footer.php');
			exit();
		}
		else
		{
			echo '<div class="alert alert-success"><strong>'. $result['msg'] .'</strong> <a href="'. _URL .'/article_read.php?a='. $_POST['id'] .'&mode=preview" target="_blank">ببین که چگونه کار می کند.</a></div>';
		}
	}	
}

if ($action == 'edit')
{
	$id = (int) $_GET['id'];
	if ($id == 0)
	{
		$action = 'new';
		$inputs = array();
		$inputs['allow_comments'] = 1;
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
		$inputs['category_as_arr'] = array();
	}
	else
	{
		$inputs = get_article($id);
	}
	$meta_data = get_all_meta_data($inputs['id'], IS_ARTICLE);
	
	if ($inputs['article_slug'] == '')
	{
		$inputs['article_slug'] = 'read-'. sanitize_title($inputs['title']);
		$inputs['article_slug'] = preg_replace('/-video$/', '_video', $inputs['article_slug']);
	}
	
}
else if ($action == 'new')
{
	if ('' != $_POST['submit'])
	{
		$inputs = $_POST;
	}
	else
	{
		$inputs['allow_comments'] = 1;
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
	}
	if ( ! is_array($inputs['category_as_arr']))
	{
		$inputs['category_as_arr'] = array();
	}
}

$categories = art_get_categories();

//	Filter some fields before output
$inputs['title'] = pre_post_filter($inputs['title']);
$inputs['tags'] = pre_post_filter($inputs['tags']);

?>


<form name="write_article" method="post" action="article_manager.php?do=<?php echo $action; ?>&id=<?php echo $_GET['id'];?>" onsubmit="return validateFormOnSubmit(this, 'لطفا فیلدهای اجباری را پر نمایید (برجسته شده).')">

<div class="container row-fluid" id="post-page">

    <div class="span9">
    <div class="widget border-radius4 shadow-div">
    <h4>توضیحات &amp; عنوان</h4>
    <div class="control-group">
	<input name="title" type="text" id="must" value="<?php echo $inputs['title']; ?>" style="width: 99%;" />

    <div class="permalink-field">

	<?php if (_SEOMOD) : ?>
		<strong>لینک ثابت : </strong> <?php echo _URL .'/articles/';?><input class="permalink-input" type="text" name="article_slug" value="<?php echo urldecode($inputs['article_slug']);?>" /><?php echo  '_'. (($inputs['id'] == '') ? 'ID' : $inputs['id']) .'.html';?>
	<?php endif; ?>

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
    <textarea name="content" cols="100" id="textarea-WYSIWYG" class="tinymce" style="height: 350px;width:100%"><?php echo $inputs['content']; ?></textarea>
    <span class="autosave-message">&nbsp;</span>

    </div>
    </div>
    </div>
	
	<div class="widget border-radius4 shadow-div" id="custom-fields">
	<h4>فیلدهای سفارشی <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank"><i class="icon-question-sign"></i></a></h4>
    	<div class="control-group">
		<div class="row-fluid">
			<div class="span3"><strong>نام</strong></div>
			<div class="span9"><strong>مقدار</strong></div>
		</div>		
		<?php 
		if ($action == 'new') :
			if (count($_POST['meta']) > 0) :
				foreach ($_POST['meta'] as $meta_id => $meta) : 
					$meta['meta_key'] = $meta['key'];
					$meta['meta_value'] = $meta['value'];
					
					echo admin_custom_fields_row($meta_id, $meta);
				endforeach;
			endif;
			
			echo admin_custom_fields_add_form(0, IS_ARTICLE);
		else :
			if (count($meta_data) > 0) :
			 	foreach ($meta_data as $meta_id => $meta) : 
					echo admin_custom_fields_row($meta_id, $meta);
				endforeach;
			endif;
			
			echo admin_custom_fields_add_form($inputs['id'], IS_ARTICLE);
		endif; ?>
		
		</div>
	</div>
    
    </div><!-- .span8 -->

    <div class="span3">


		<div class="widget border-radius4 shadow-div">
		<h4>دسته</h4>
            <div class="control-group">
            <div class="controls">
            <input type="hidden" name="categories_old" value="<?php echo $inputs['category'];?>"  />
            <?php 
            //$checklist_options = array('selected' => explode(',', $inputs['category']), 'ul_wrapper' => false);
            //echo art_cats_checklist($categories, $checklist_options);

            $categories_dropdown_options = array(
                        'db_table' => 'art_categories',
                        'attr_name' => 'categories[]',
                        'attr_id' => 'main_select_category',
						'attr_class' => 'category_dropdown span12',
                        'select_all_option' => false,
                        'spacer' => '&mdash;',
                        'selected' => explode(',', $inputs['category']), 
                        'other_attr' => 'multiple="multiple" size="3"',
                        'option_attr_id' => 'check_ignore'
                        );
            echo categories_dropdown($categories_dropdown_options);
            ?>
            </div>
			<a href="#" id="inline_add_new_category" />+ دسته جدید</a>
			<div id="inline_add_new_category_form" class="hide">
				<input name="add_category_name" type="text" placeholder="Category name" id="add_category_name" /> 
				<input name="add_category_slug" type="text" placeholder="Slug" /> <a href="#" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."><i class="icon-info-sign" rel="tooltip" title="اسلاگ ها در آدرس استفاده می شوند و می توانند شامل اعداد ، حروف ، خط تیره و خط زیر باشند."></i></a>
                <label>ایجاد در (<em>اختیاری</em>)</label>
				<?php 
					$categories_dropdown_options = array(
											'db_table' => 'art_categories',
											'first_option_text' => '&ndash; Parent Category &ndash;', 
											'first_option_value' => '-1',
											'attr_name' => 'add_category_parent_id',
											'attr_id' => '',
											'attr_class' => '',
											'select_all_option' => true,
											'spacer' => '&mdash;'
											);
					echo categories_dropdown($categories_dropdown_options); 
				?>
				<br />
				<button name="add_category_submit_btn" value="اضافه کردن دسته" class="btn btn-mini btn-normal" />اضافه کردن دسته جدید</button>
				<span id="add_category_response"></span>
			</div>
            </div>
            <?php
        $modframework->trigger_hook('admin_article_publishfields');
        ?>
		</div><!-- .widget -->
        
        <div class="widget border-radius4 shadow-div">
        <h4>انتشار</h4>
            <div class="control-group">
            <label>نظرات :<span id="value-comments"><strong><?php if ($inputs['allow_comments'] == 1) { echo 'فعال'; } else { echo 'غیر فعال'; } ?></strong></span> <a href="#" id="show-comments">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-comments">
                <label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($inputs['allow_comments'] == 1) echo 'checked="checked"';?> />برای این مقاله اجازه نظر دادن وجود داشته باشد</label>
                <?php if ($config['comment_system'] == 'off') : ?>
                <div class="alert">
                نظردهی بصورت کلی غیرفعال شده.
                <br />
                برای نظردهی کلی ، برو به <a href="settings.php?view=comment" title="صفحه تنظیمات" target="_blank">تنظیمات < تنظیمات نظرات</a>.
                </div>
                <?php endif;?>
            </div>
            </div>
            
            <div class="control-group">
            <label class="control-label" for="">وضعیت پست  <span id="value-visibility"><strong><?php if ($inputs['status'] == 0) { echo 'پیشنویس'; } else { echo 'انتشار عمومی'; } ?></strong></span> <a href="#" id="show-visibility">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-visibility">
                <label class="checkbox inline"><input type="radio" name="status" id="visibility" value="0" <?php if ($inputs['status'] == 0) echo 'checked="checked"'; ?> /> پیش نویس</label> 
                <label class="checkbox inline"><input type="radio" name="status" id="visibility" value="1" <?php if ($inputs['status'] == 1) echo 'checked="checked"'; ?> /> عمومی</label>
            </div>
            </div>

            <div class="control-group">
            <label>موقعیت: <span id="value-featured"><strong><?php if($inputs['featured'] == 1) { echo 'بله'; } else { echo 'خیر'; } ?></strong></span> <a href="#" id="show-featured">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-featured">
                <label><input type="checkbox" name="featured" id="featured" value="1" <?php if($inputs['featured'] == 1) echo 'checked="checked"';?> /> بله,در صفحه اصلی</label>
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">ثبت نام اجباری : <span id="value-register"><strong><?php if($inputs['restricted'] == 1) { echo 'بله'; } else { echo 'خیر'; } ?></strong></span> <a href="#" id="show-restriction">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-restriction">
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($inputs['restricted'] == 0) echo 'checked="checked"'; ?> /> خیر</label> 
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($inputs['restricted'] == 1) echo 'checked="checked"'; ?> /> بله</label>
            </div>
            </div>
                        
            <div class="control-group">
            <label class="control-label" for="">انتشار : <span id="value-publish"><strong><?php if(empty($inputs['date'])) { echo 'بلافاصله بعد از ثبت'; } else { echo date('M d, Y @ G:i',$inputs['date']); }?> </strong></span> <a href="#" id="show-publish">ویرایش کردن</a></label>
            <div class="controls" id="show-opt-publish">
            <?php echo show_form_item_date($inputs['date']); ?>
            </div>
            </div>
        </div><!-- .widget -->

		<div class="widget border-radius4 shadow-div">
		<h4>تگ ها</h4>
            <div class="control-group">
            <div class="controls">
                <div class="tagsinput" style="width: 100%;">
                <input name="tags" type="text" value="<?php echo $inputs['tags']; ?>"  id="tags_addvideo_1" size="50" />
                </div>
            </div>
            </div>
        </div><!-- .widget -->


		<div class="widget border-radius4 shadow-div">
		<h4>ارسال تصویر</h4>
            <div class="control-group">
            <div class="controls">
                <?php
            
                    $all_meta = $inputs['meta']['*'];
                    $total_thumbs = count($all_meta['_post_thumb']);
                          
                    if ($total_thumbs > 0)
                    { 
                        echo '<ul class="thumbs_ul">';
                        
                        // display current selected thumbnail
                        if ($inputs['meta']['_post_thumb_show'] != '')
                        {
                            echo '<li class="art-thumb-selected"><img src="img/bg-selected.gif" alt="" border="0" style="display:none" /><img src="'. _ARTICLE_ATTACH_DIR . $inputs['meta']['_post_thumb_show'] .'" width="'. THUMB_W_ARTICLE .'" height="'. THUMB_H_ARTICLE .'" alt="Thumb 1" name="article_thumbnail" /></li>';	
                        }
                        
                        // display next thumbnails available for this post.
                        $limit = 10;
                        for ($i = 0; $i < $limit; $i++)
                        {
                            if (strlen($all_meta['_post_thumb'][$i]) > 0)
                            {
                                if ($all_meta['_post_thumb'][$i] != $inputs['meta']['_post_thumb_show'])
                                {
                                    echo '<li class="art-thumb-default"><img src="img/bg-selected.gif" alt="" border="0" style="display:none" /><img src="'. _ARTICLE_ATTACH_DIR . $all_meta['_post_thumb'][$i] .'" width="'. THUMB_W_ARTICLE .'" height="'. THUMB_H_ARTICLE .'"  alt="Thumb '. ($i + 2) .'" name="article_thumbnail" /></li>';
                                }
                                else
                                {
                                    $limit++;
                                }
                                
                                if ($limit > 99)
                                {
                                    break;
                                }
                            }
                        }
                        echo '</ul>';
                    } 
                    else
                    {
                        echo '<em>هیچ تامبنیلی برای این مقاله وجود ندارد. برای ایجاد یک تامبنیل برای این مقاله ابتدا در داخل این پست عکس ها را آپلود کن و سپس بر روی "ذخیره کردن" کلیک کن.</em>';
                    }
                ?>
                <div class="clearfix"></div>
                    <input type="hidden" name="post_thumb_show" value="<?php if ($inputs['meta']['_post_thumb_show'] != '') echo $inputs['meta']['_post_thumb_show'];?>" />
            </div>
            </div>
        </div><!-- .widget -->
        <?php
        $modframework->trigger_hook('admin_article_fields');
        ?>
    </div>
    
</div>
<div class="clearfix"></div>


<input type="hidden" name="author" value="<?php  echo $inputs['author'];?>" />
<input type="hidden" name="id" value="<?php echo $inputs['id'];?>" />

<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
    	<button name="cancel" type="button" value="Cancel" onClick="location.href='articles.php'" class="btn btn-small btn-normal btn-strong">بی خیال</button>
    </div>
    <div class="btn-group">
    	<button name="submit" type="submit" <?php echo ($action == 'edit') ? 'value="ذخیره کردن"' : 'value="انتشار دادن"';?> class="btn btn-small btn-success btn-strong"><?php echo ($action == 'edit') ? 'ذخیره کردن' : 'انتشار دادن';?></button>
	</div>
</div>
</div><!-- #list-controls -->
    
</form>


    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>