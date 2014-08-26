<?php /* Smarty version 2.6.20, created on 2014-06-23 15:22:31
         compiled from article-category.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('p' => 'article','tpl_name' => "article-category")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span8">
        <div id="primary">
        <h1><?php echo $this->_tpl_vars['article_h2']; ?>
</h1>
		<hr />
        <?php if ($this->_tpl_vars['cat_id'] > 0 && $this->_tpl_vars['categories'][$this->_tpl_vars['cat_id']]['description']): ?>
        <div class="pm-browse-desc">
        <?php echo $this->_tpl_vars['categories'][$this->_tpl_vars['cat_id']]['description']; ?>
 
        <div class="clearfix"></div>
        </div>
        <?php endif; ?>

        <ul class="pm-ul-browse-articles">
        <?php if (! is_array ( $this->_tpl_vars['articles'] )): ?>
            <?php echo $this->_tpl_vars['articles']; ?>

        <?php else: ?>
            <?php $_from = $this->_tpl_vars['articles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['article']):
?>
            <li <?php if ($this->_tpl_vars['article']['featured'] == '1'): ?>class="sticky-article"<?php endif; ?>>
            <article class="post">
            <header class="entry-header">
            <?php if ($this->_tpl_vars['logged_in'] && $this->_tpl_vars['is_admin'] == 'yes'): ?>
            <span class="pull-right"><a href="<?php echo @_URL; ?>
/admin/article_manager.php?do=edit&id=<?php echo $this->_tpl_vars['article']['id']; ?>
" title="<?php echo $this->_tpl_vars['lang']['edit']; ?>
" target="_blank" class="btn btn-mini"><?php echo $this->_tpl_vars['lang']['edit']; ?>
</a></span>
            <?php endif; ?>
            <h2 dir="ltr" class="entry-title">
            <a href="<?php echo $this->_tpl_vars['article']['link']; ?>
" title="<?php echo $this->_tpl_vars['article']['title']; ?>
"><?php echo $this->_tpl_vars['article']['title']; ?>
</a>
            </h2>
            </header><!-- .entry-header -->
            
            <div class="pm-article-info">
            <time class="entry-date" datetime="<?php echo $this->_tpl_vars['article']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['article']['full_datetime']; ?>
" pubdate><?php echo $this->_tpl_vars['article']['date']; ?>
</time> 
            <?php echo $this->_tpl_vars['lang']['articles_by']; ?>
 <a href="<?php echo @_URL; ?>
/profile.<?php echo @_FEXT; ?>
?u=<?php echo $this->_tpl_vars['article']['username']; ?>
"><?php echo $this->_tpl_vars['article']['name']; ?>
</a> / <strong><?php echo $this->_tpl_vars['article']['views_formatted']; ?>
</strong> <?php echo $this->_tpl_vars['lang']['views']; ?>

            </div>
            
            <div class="entry-summary">
			<?php if ($this->_tpl_vars['article']['restricted'] == '1' && ! $this->_tpl_vars['logged_in']): ?>
				<?php echo $this->_tpl_vars['lang']['article_restricted_sorry']; ?>

			<?php else: ?>
				<?php echo $this->_tpl_vars['article']['content']; ?>

			<?php endif; ?>
            <span class="entry-summary-nav more-link"><a href="<?php echo $this->_tpl_vars['article']['link']; ?>
"><?php echo $this->_tpl_vars['lang']['read_more']; ?>
 &raquo;</a>
            </div>
            </article>
            </li>
            <?php endforeach; endif; unset($_from); ?>
        <?php endif; ?>
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
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array('tpl_name' => "article-category")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 