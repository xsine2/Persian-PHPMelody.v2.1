<?php /* Smarty version 2.6.20, created on 2014-06-28 17:18:56
         compiled from video-top.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('p' => 'general','tpl_name' => "video-top")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
        <div id="primary">
		<?php if ($this->_tpl_vars['cat_name']): ?>
			<h1><?php echo $this->_tpl_vars['cat_name']; ?>
 <?php echo $this->_tpl_vars['lang']['top_m_videos']; ?>
</h1>
		<?php else: ?>
			<h1><?php echo $this->_tpl_vars['lang']['top_m_videos']; ?>
</h1>
		<?php endif; ?>

		<form class="form-inline li-dropdown-out opac7">
		<label><?php echo $this->_tpl_vars['lang']['sorting']; ?>
</label>
		<select name="categories" size="1" onChange="javascript:document.location=this.value;">
		<option value="<?php echo @_URL; ?>
/topvideos.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['select']; ?>
</option>
		<optgroup label="<?php echo $this->_tpl_vars['lang']['most_liked']; ?>
">
			<option value="<?php echo @_URL; ?>
/topvideos.<?php echo @_FEXT; ?>
?do=rating" <?php if ($_GET['do'] == 'rating'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lang']['any_time']; ?>
</option>
		</optgroup>
		<optgroup label="<?php echo $this->_tpl_vars['lang']['by_time']; ?>
">
			<option value="<?php echo @_URL; ?>
/topvideos.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['any_time']; ?>
</option>
			<option value="<?php echo @_URL; ?>
/topvideos.<?php echo @_FEXT; ?>
?do=recent" <?php if ($_GET['do'] == 'recent'): ?>selected="selected"<?php endif; ?>>
				<?php echo $this->_tpl_vars['chart_days']; ?>

			</option>
		</optgroup>
		<optgroup label="<?php echo $this->_tpl_vars['lang']['by_cat']; ?>
">
			<?php echo $this->_tpl_vars['categories_list']; ?>

		</optgroup>
		</select>
        </form>
        <hr />
        <ul class="pm-ul-alltop-videos thumbnails" id="pm-grid">
		<?php $_from = $this->_tpl_vars['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
        $this->_foreach['loop']['iteration']++;
?>
		  <li>
			<div class="pm-li-video">
			    <span class="pm-video-thumb pm-thumb-194 pm-thumb border-radius2">
			    <span class="pm-video-rank"><?php echo $this->_tpl_vars['video_data']['position']; ?>
</span>
			    <span class="pm-video-li-thumb-info">
			    <?php if ($this->_tpl_vars['video_data']['yt_length'] != 0): ?><span class="pm-label-duration border-radius3 opac7"><?php echo $this->_tpl_vars['video_data']['duration']; ?>
</span><?php endif; ?>
				<?php if ($this->_tpl_vars['video_data']['mark_new']): ?><span class="label label-new"><?php echo $this->_tpl_vars['lang']['_new']; ?>
</span><?php endif; ?>
				<?php if ($this->_tpl_vars['video_data']['mark_popular']): ?><span class="label label-pop"><?php echo $this->_tpl_vars['lang']['_popular']; ?>
</span><?php endif; ?>
			    </span>
			    
			    <a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-194"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['video_data']['thumb_img_url']; ?>
" alt="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
" width="194"><span class="vertical-align"></span></span></a>
			    </span>
			    
			    <h3 dir="ltr"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link" title="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
"><?php echo $this->_tpl_vars['video_data']['video_title']; ?>
</a></h3>
			    <div class="pm-video-attr">
			        <span class="pm-video-attr-author"><?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo $this->_tpl_vars['video_data']['author_profile_href']; ?>
"><?php echo $this->_tpl_vars['video_data']['author_name']; ?>
</a></span>
			        <span class="pm-video-attr-since"><small><time datetime="<?php echo $this->_tpl_vars['video_data']['html5_datetime']; ?>
"  title="<?php echo $this->_tpl_vars['video_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['video_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time></small></span>
			        <span class="pm-video-attr-numbers"><small><?php echo $this->_tpl_vars['video_data']['views_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['views']; ?>
 / <?php echo $this->_tpl_vars['video_data']['likes_compact']; ?>
 <?php echo $this->_tpl_vars['lang']['_likes']; ?>
</small></span>
				</div>
			    <p class="pm-video-attr-desc"><?php echo $this->_tpl_vars['video_data']['excerpt']; ?>
</p>
			
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
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array('tpl_name' => "video-top")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 