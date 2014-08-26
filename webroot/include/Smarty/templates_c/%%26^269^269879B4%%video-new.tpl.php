<?php /* Smarty version 2.6.20, created on 2014-06-29 11:24:38
         compiled from video-new.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'smarty_fewchars', 'video-new.tpl', 39, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('p' => 'general','tpl_name' => "video-new")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
        <div id="primary">
		<h1><?php echo $this->_tpl_vars['lang']['recently_added']; ?>
</h1>
        
        <div class="btn-group btn-group-sort">
        <button class="btn btn-small" id="list"><i class="icon-th"></i> </button>
        <button class="btn btn-small" id="grid"><i class="icon-th-list"></i> </button>
        </div>

		<form class="form-inline li-dropdown-inside opac7">
		<label><?php echo $this->_tpl_vars['lang']['added']; ?>
</label>
		<select name="categories" class="inp-small" size="1" onChange="javascript:document.location=this.value;">
		<option value="" selected="selected"><?php echo $this->_tpl_vars['lang']['select']; ?>
</option>
		<option value="<?php echo @_URL; ?>
/newvideos.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['any_time']; ?>
</option>
		<option value="<?php echo @_URL; ?>
/newvideos.<?php echo @_FEXT; ?>
?d=today"><?php echo $this->_tpl_vars['lang']['today']; ?>
</option>
		<option value="<?php echo @_URL; ?>
/newvideos.<?php echo @_FEXT; ?>
?d=yesterday"><?php echo $this->_tpl_vars['lang']['yesterday']; ?>
</option>
		<option value="<?php echo @_URL; ?>
/newvideos.<?php echo @_FEXT; ?>
?d=month"><?php echo $this->_tpl_vars['lang']['this_month']; ?>
</option>
		</select>
        </form>
<hr />
<ul class="pm-ul-new-videos thumbnails" id="pm-grid">
<?php $_from = $this->_tpl_vars['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
?>
  <li>
	<div class="pm-li-video">
	    <span class="pm-video-thumb pm-thumb-138 pm-thumb border-radius2">
	    <span class="pm-video-li-thumb-info">
	    <?php if ($this->_tpl_vars['video_data']['yt_length'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['video_data']['duration']; ?>
</span><?php endif; ?>
	    <?php if ($this->_tpl_vars['video_data']['mark_new']): ?><span class="label label-new"><?php echo $this->_tpl_vars['lang']['_new']; ?>
</span><?php endif; ?>
		<?php if ($this->_tpl_vars['video_data']['mark_popular']): ?><span class="label label-pop"><?php echo $this->_tpl_vars['lang']['_popular']; ?>
</span><?php endif; ?>
		</span>
	    
	    <a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-145"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
" width="145"><span class="vertical-align"></span></span></a>
	    </span>
	    
	    <h3 dir="rtl"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link" title="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['video_data']['video_title'],'length' => 25), $this);?>
</a></h3>
	    <div class="pm-video-attr">
	        <span class="pm-video-attr-author"><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_name']; ?>
</a></span>
	        <span class="pm-video-attr-since"><small><?php echo $this->_tpl_vars['lang']['added']; ?>
 <time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time></small></span>
	        <span class="pm-video-attr-numbers"><small><?php echo $this->_tpl_vars['video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
</small></span>
		</div>		
		<?php if ($this->_tpl_vars['video_data']['featured']): ?>
	    <span class="pm-video-li-info">
	        <span class="label label-featured"><?php echo $this->_tpl_vars['lang']['_feat']; ?>
</span>
	    </span>
		<?php endif; ?>
	</div>
  </li>
		
<?php endforeach; else: ?>
	<?php echo $this->_tpl_vars['lang']['top_videos_msg2']; ?>

<?php endif; unset($_from); ?>
</ul>

<?php if ($this->_tpl_vars['empty_results']): ?>
	<p class="alert"><?php echo $this->_tpl_vars['lang']['nv_page_sorry_msg']; ?>
</p>
<?php endif; ?>

<div class="clearfix"></div>
<?php if (is_array ( $this->_tpl_vars['pagination'] )): ?>
<div class="pagination pagination-centered">
    <ul>
		<?php $_from = $this->_tpl_vars['pagination']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['pagination_data']):
?>
		<li<?php $_from = $this->_tpl_vars['pagination_data']['li']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['attr'] => $this->_tpl_vars['attr_val']):
?> <?php echo $this->_tpl_vars['attr']; ?>
="<?php echo $this->_tpl_vars['attr_val']; ?>
"<?php endforeach; endif; unset($_from); ?>>
			<a<?php $_from = $this->_tpl_vars['pagination_data']['a']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['attr'] => $this->_tpl_vars['attr_val']):
?> <?php echo $this->_tpl_vars['attr']; ?>
="<?php echo $this->_tpl_vars['attr_val']; ?>
"<?php endforeach; endif; unset($_from); ?>><?php echo $this->_tpl_vars['pagination_data']['text']; ?>
</a>
		</li>
		<?php endforeach; endif; unset($_from); ?>
	</ul>
</div>
<?php endif; ?>
		</div><!-- #primary -->
        </div><!-- #content -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array('tpl_name' => "video-new")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>