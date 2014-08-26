<?php /* Smarty version 2.6.20, created on 2014-06-28 21:47:13
         compiled from article-read.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'smarty_fewchars', 'article-read.tpl', 59, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('p' => 'article','tpl_name' => "article-read")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
<?php if ($this->_tpl_vars['show_addthis_widget'] == '1'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'widget-addthis.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span8">
		<div id="primary" itemscope itemtype="http://schema.org/Article">
        <?php if (is_array ( $this->_tpl_vars['article'] )): ?>
        <article class="post">
        <header class="entry-header">
        <?php if ($this->_tpl_vars['logged_in'] && $this->_tpl_vars['is_admin'] == 'yes'): ?>
        <a href="<?php echo @_URL; ?>
/admin/article_manager.php?do=edit&id=<?php echo $this->_tpl_vars['article']['id']; ?>
" rel="tooltip" class="btn btn-mini pull-right" title="<?php echo $this->_tpl_vars['lang']['edit']; ?>
 (<?php echo $this->_tpl_vars['lang']['_admin_only']; ?>
)" target="_blank"><?php echo $this->_tpl_vars['lang']['edit']; ?>
</a>
        <?php endif; ?>
        <h1 class="entry-title" itemprop="name"><?php echo $this->_tpl_vars['article']['title']; ?>
</h1>
		<meta itemprop="interactionCount" content="UserComments:<?php echo $this->_tpl_vars['article']['comment_count']; ?>
"/>

		<?php if ($this->_tpl_vars['article']['meta']['_post_thumb_show'] != ''): ?>
		<meta itemprop="thumbnailUrl" content="<?php echo @_ARTICLE_ATTACH_DIR; ?>
/<?php echo $this->_tpl_vars['article']['meta']['_post_thumb_show']; ?>
"/>
		<?php endif; ?>
        </header><!-- .entry-header -->
        <div style="clear:both;"></div>
        <div class="pm-article-info">
            <strong><?php echo $this->_tpl_vars['lang']['articles_published']; ?>
</strong>: <time class="entry-date" datetime="<?php echo $this->_tpl_vars['article']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['article']['full_datetime']; ?>
" pubdate><?php echo $this->_tpl_vars['article']['date']; ?>
</time> <?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo @_URL; ?>
/profile.<?php echo @_FEXT; ?>
?u=<?php echo $this->_tpl_vars['article']['username']; ?>
" itemprop="author"><?php echo $this->_tpl_vars['article']['name']; ?>
</a> 
            <strong><?php echo $this->_tpl_vars['lang']['articles_filedunder']; ?>
</strong>: 
            <?php $_from = $this->_tpl_vars['article']['pretty_cats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat_name'] => $this->_tpl_vars['cat_href']):
?>
                <a href="<?php echo $this->_tpl_vars['cat_href']; ?>
" title="<?php echo $this->_tpl_vars['cat_name']; ?>
"><?php echo $this->_tpl_vars['cat_name']; ?>
</a> 
            <?php endforeach; endif; unset($_from); ?> 
           - <strong><?php echo $this->_tpl_vars['article']['views_formatted']; ?>
</strong> <?php echo $this->_tpl_vars['lang']['views']; ?>

        </div>

        <div class="entry-post">
        <?php if ($this->_tpl_vars['article']['restricted'] == '1' && ! $this->_tpl_vars['logged_in']): ?>
        	<div class="restricted-video border-radius4">
			    <h2><?php echo $this->_tpl_vars['lang']['article_restricted_sorry']; ?>
</h2>
				<p><?php echo $this->_tpl_vars['lang']['article_restricted_register']; ?>
</p>
				<div class="restricted-login">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-auth-login-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			    </div>
			</div>
        <?php else: ?>
        	<div itemprop="articleBody"><?php echo $this->_tpl_vars['article']['content']; ?>
</div>
		<?php endif; ?>
        </div>
        </article>
        <?php else: ?>
        <article class="post">
        <h1><?php echo $this->_tpl_vars['article']; ?>
</h1>
        </article>
        <?php endif; ?>

        <div class="row-fluid pm-author-data pm-article-author">       
            <div class="span2">
                <span class="pm-avatar"><a href="<?php echo $this->_tpl_vars['article']['author_profile_href']; ?>
"><img src="<?php echo $this->_tpl_vars['article']['avatar_url']; ?>
" height="50" width="50" alt="" class="img-polaroid" border="0"></a></span>
            </div>
            <div class="span10">
                <div class="pm-submit-data"><a href="<?php echo @_URL; ?>
/profile.<?php echo @_FEXT; ?>
?u=<?php echo $this->_tpl_vars['article']['author_username']; ?>
"><?php echo $this->_tpl_vars['article']['name']; ?>
</a></div>
                <div class="pm-author-about"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['article']['author_about'],'length' => 200), $this);?>
</div>         
                <?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == '1' && $this->_tpl_vars['article']['author'] != $this->_tpl_vars['s_user_id']): ?>
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "user-follow-button.tpl", 'smarty_include_vars' => array('profile_data' => $this->_tpl_vars['article'],'profile_user_id' => $this->_tpl_vars['article']['author'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                <?php endif; ?>
            </div>
        </div><!--.pm-author-data-->

		<div class="clearfix"></div>
        <?php if (! empty ( $this->_tpl_vars['article']['tags'] )): ?>
        <div class="pm-article-info"><strong><?php echo $this->_tpl_vars['lang']['tags']; ?>
</strong>: 
            <?php $_from = $this->_tpl_vars['article']['tags']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['tag_links'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['tag_links']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['t']):
        $this->_foreach['tag_links']['iteration']++;
?>
             <?php if (($this->_foreach['tag_links']['iteration'] == $this->_foreach['tag_links']['total'])): ?>
              <a href="<?php echo $this->_tpl_vars['t']['link']; ?>
" title="<?php echo $this->_tpl_vars['t']['tag']; ?>
"><?php echo $this->_tpl_vars['t']['tag']; ?>
</a> 
             <?php else: ?>
              <a href="<?php echo $this->_tpl_vars['t']['link']; ?>
" title="<?php echo $this->_tpl_vars['t']['tag']; ?>
"><?php echo $this->_tpl_vars['t']['tag']; ?>
</a>, 
             <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
        </div>
        <hr />
        <?php endif; ?>

		<?php if ($this->_tpl_vars['article']['allow_comments'] == '1'): ?>
		<h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['post_comment']; ?>
</h2>
        <div style="clear:both;"></div>
        <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'comment-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php if ($this->_tpl_vars['logged_in'] != '1' && $this->_tpl_vars['guests_can_comment'] != 1): ?>
			<?php echo $this->_tpl_vars['must_sign_in']; ?>

		<?php endif; ?>
		<?php endif; ?>
		
		<h2 class="upper-blue"><?php echo $this->_tpl_vars['lang']['comments']; ?>
</h2>
        <div style="clear:both;"></div>
        <div style=" border-bottom: 5px #477E84 solid; margin-top: -10px;"></div> 
		<div class="pm-comments comment_box">
		<?php if ($this->_tpl_vars['article']['allow_comments'] == '1'): ?>
		<?php if ($this->_tpl_vars['comment_count'] == 0): ?>
		    <ul class="pm-ul-comments">
		    	<li id="preview_comment"></li>
		    </ul>
		    <div id="be_the_first"><?php echo $this->_tpl_vars['lang']['be_the_first']; ?>
</div>
		<?php else: ?>
			<span id="comment-list-container">
			    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "comment-list.tpl", 'smarty_include_vars' => array('tpl_name' => "article-read")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				
				<!-- comment pagination -->
				<?php if ($this->_tpl_vars['comment_pagination_obj'] != ''): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "comment-pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
			</span>
		<?php endif; ?>
		<?php else: ?>
			<div><?php echo $this->_tpl_vars['lang']['comments_disabled']; ?>
</div>
		<?php endif; ?>
		</div>
		</div><!-- #primary -->
        </div><!-- #content -->
        <div class="span4">
		<div id="secondary">
        <?php if ($this->_tpl_vars['ad_4'] != ''): ?><?php echo $this->_tpl_vars['ad_4']; ?>
<?php endif; ?>
        <?php if (is_array ( $this->_tpl_vars['related_articles'] ) && count ( $this->_tpl_vars['related_articles'] ) > 0): ?>
        <div class="widget">
			<h4><?php echo $this->_tpl_vars['lang']['articles_related']; ?>
</h4>
            <ul class="pm-ul-home-articles" id="pm-ul-home-articles">
            <?php $_from = $this->_tpl_vars['related_articles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['related']):
?>
                <li>
                    <article>
                    <?php if ($this->_tpl_vars['related']['meta']['_post_thumb_show'] != ''): ?>
					<div class="pm-article-thumb">
                    <a href="<?php echo $this->_tpl_vars['related']['link']; ?>
"><img src="<?php echo @_ARTICLE_ATTACH_DIR; ?>
/<?php echo $this->_tpl_vars['related']['meta']['_post_thumb_show']; ?>
" align="left" width="55" height="55" border="0" alt="<?php echo $this->_tpl_vars['related']['title']; ?>
"></a>
					</div>
                    <?php endif; ?>

                    <h6 dir="ltr" class="ellipsis"><a href="<?php echo $this->_tpl_vars['related']['link']; ?>
" class="pm-title-link"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['related']['title'],'length' => 92), $this);?>
</a></h6>
                    <p class="pm-article-preview">
                    <?php if ($this->_tpl_vars['related']['meta']['_post_thumb_show'] == ''): ?>
                        <div class="minDesc"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['related']['excerpt'],'length' => 125), $this);?>
</div>
                    <?php else: ?>
                        <div class="minDesc"><?php echo smarty_fewchars(array('s' => $this->_tpl_vars['related']['excerpt'],'length' => 125), $this);?>
</div>
                    <?php endif; ?>
                    </p>
                    </article>
                </li>
			<?php endforeach; endif; unset($_from); ?>
            </ul>
        </div>
		<?php endif; ?>
        
		<div class="widget" id="sticky">
		<h4><?php echo $this->_tpl_vars['lang']['_categories']; ?>
</h4>
		<ul class="pm-browse-ul-subcats">
 			<?php echo $this->_tpl_vars['article_categories']; ?>

        </ul>
        </div>

        
		</div><!-- #secondary -->
        </div><!-- #sidebar -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array('tpl_name' => "article-read")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>