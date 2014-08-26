<?php /* Smarty version 2.6.20, created on 2014-07-02 07:04:26
         compiled from video-category.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('p' => 'general','tpl_name' => "video-category")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span8">
		<div id="primary" style="padding-top: 0px;margin-top: -4px;">
			<h1 class="entry-title compact"><?php echo $this->_tpl_vars['gv_category_name']; ?>
</h1>
            <div style="clear:both"></div>
            <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
            <div style="clear:both"></div>
            <div class="btn-group btn-group-sort" style="margin-left: 15px;">
            <button class="btn btn-small" id="list" style="padding: 5px;"><i class="icon-th"></i> </button>
            <button class="btn btn-small" id="grid" style="padding: 5px;"><i class="icon-th-list"></i> </button>
            <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" data-target="#" href="">
            <?php if ($this->_tpl_vars['gv_sortby'] == ''): ?><?php echo $this->_tpl_vars['lang']['sorting']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['gv_sortby'] == 'date'): ?><?php echo $this->_tpl_vars['lang']['date']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['gv_sortby'] == 'views'): ?><?php echo $this->_tpl_vars['lang']['views']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['gv_sortby'] == 'rating'): ?><?php echo $this->_tpl_vars['lang']['rating']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['gv_sortby'] == 'title'): ?><?php echo $this->_tpl_vars['lang']['title']; ?>
<?php endif; ?>
            <span class="caret"></span>
            </a>
            <ul class="dropdown-menu pull-right">
            <?php if (@_SEOMOD == '1'): ?>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'date'): ?>class="selected"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/browse-<?php echo $this->_tpl_vars['gv_cat']; ?>
-videos-<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
-date.html" rel="nofollow"><?php echo $this->_tpl_vars['lang']['date']; ?>
</a></li>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'views'): ?>class="selected"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/browse-<?php echo $this->_tpl_vars['gv_cat']; ?>
-videos-<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
-views.html" rel="nofollow"><?php echo $this->_tpl_vars['lang']['views']; ?>
</a></li>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'rating'): ?>class="active"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/browse-<?php echo $this->_tpl_vars['gv_cat']; ?>
-videos-<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
-rating.html" rel="nofollow"><?php echo $this->_tpl_vars['lang']['rating']; ?>
</a></li>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'title'): ?>class="active"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/browse-<?php echo $this->_tpl_vars['gv_cat']; ?>
-videos-<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
-title.html" rel="nofollow"><?php echo $this->_tpl_vars['lang']['title']; ?>
</a></li>
            <?php else: ?>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'date'): ?>class="selected"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/category.php?cat=<?php echo $this->_tpl_vars['gv_cat']; ?>
&page=<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
&sortby=date" rel="nofollow"><?php echo $this->_tpl_vars['lang']['date']; ?>
</a></li>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'views'): ?>class="selected"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/category.php?cat=<?php echo $this->_tpl_vars['gv_cat']; ?>
&page=<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
&sortby=views" rel="nofollow"><?php echo $this->_tpl_vars['lang']['views']; ?>
</a></li>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'rating'): ?>class="selected"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/category.php?cat=<?php echo $this->_tpl_vars['gv_cat']; ?>
&page=<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
&sortby=rating" rel="nofollow"><?php echo $this->_tpl_vars['lang']['rating']; ?>
</a></li>
            <li <?php if ($this->_tpl_vars['gv_sortby'] == 'title'): ?>class="selected"<?php endif; ?>>
            <a href="<?php echo @_URL; ?>
/category.php?cat=<?php echo $this->_tpl_vars['gv_cat']; ?>
&page=<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
&sortby=title" rel="nofollow"><?php echo $this->_tpl_vars['lang']['title']; ?>
</a></li>
			<?php endif; ?>
            </ul>
            </div>
            <?php if ($this->_tpl_vars['gv_category_description']): ?>
			<div class="pm-browse-desc">
            <?php echo $this->_tpl_vars['gv_category_description']; ?>

            <div class="clearfix"></div>
			</div>
			<?php endif; ?>

     		<?php echo $this->_tpl_vars['problem']; ?>


            <ul class="pm-ul-browse-videos thumbnails" id="pm-grid">
			<?php $_from = $this->_tpl_vars['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['video_data']):
?>
			  <li>
				<div class="pm-li-video">
				    <span class="pm-video-thumb pm-thumb-145 pm-thumb border-radius2">
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
				    
				    <h3 dir="ltr"><a href="<?php echo $this->_tpl_vars['video_data']['video_href']; ?>
" class="pm-title-link " title="<?php echo $this->_tpl_vars['video_data']['attr_alt']; ?>
"><?php echo $this->_tpl_vars['video_data']['video_title']; ?>
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
			<?php endforeach; endif; unset($_from); ?>
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
        <div class="span4">
		<div id="secondary">
		
        <?php if (! empty ( $this->_tpl_vars['list_subcats'] )): ?>
		<div class="widget">
           <div id="list_subcats">
           <h4><?php echo $this->_tpl_vars['lang']['related_cats']; ?>
</h4>
           <ul class="pm-browse-ul-subcats">
            <?php echo $this->_tpl_vars['list_subcats']; ?>

           </ul>
           </div>
		</div>
		<?php endif; ?>

        <div class="widget" id="sticky">
		<h4><?php echo $this->_tpl_vars['lang']['_categories']; ?>
</h4>
        <div style="clear:both"></div>
        <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
        <div style="clear:both"></div>
 		<ul class="pm-browse-ul-subcats">
			<?php echo $this->_tpl_vars['list_categories']; ?>

        </ul>
		</div><!-- .widget -->
        
		</div><!-- #secondary -->
        </div><!-- #sidebar -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array('tpl_name' => "video-category")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 