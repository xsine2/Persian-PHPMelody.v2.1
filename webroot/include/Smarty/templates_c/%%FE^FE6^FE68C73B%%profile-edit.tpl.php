<?php /* Smarty version 2.6.20, created on 2014-06-24 06:57:45
         compiled from profile-edit.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('p' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
            <nav id="second-nav" class="tabbable" role="navigation">
                <ul class="nav nav-tabs pull-right">
                <li class="active"><a href="<?php echo @_URL; ?>
/edit_profile.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['edit_profile']; ?>
</a></li>
                <li><a href="<?php echo @_URL; ?>
/upload_avatar.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['update_avatar']; ?>
</a></li>
                <li><a href="<?php echo @_URL; ?>
/favorites.<?php echo @_FEXT; ?>
?a=show"><?php echo $this->_tpl_vars['lang']['my_favorites']; ?>
</a></li>
                <?php if (@_ALLOW_USER_SUGGESTVIDEO == '1'): ?>
                <li><a href="<?php echo @_URL; ?>
/suggest.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['suggest']; ?>
</a></li>
                <?php endif; ?>
                <?php if (@_ALLOW_USER_UPLOADVIDEO == '1'): ?>
                <li><a href="<?php echo @_URL; ?>
/upload.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['upload_video']; ?>
</a></li>
                <?php endif; ?>
                <li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['members_list']; ?>
</a></li>
				<?php if (isset ( $this->_tpl_vars['mm_profilemenu_insert'] )): ?><?php echo $this->_tpl_vars['mm_profilemenu_insert']; ?>
<?php endif; ?>
                </ul>
            </nav><!-- #site-navigation -->
        </div>
      </div>
      
      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary">
        
		<h1><?php echo $this->_tpl_vars['lang']['update_profile']; ?>
</h1>
        <hr />
		<?php if ($this->_tpl_vars['success'] == 1): ?>
		<div class="alert alert-success"><?php echo $this->_tpl_vars['lang']['ep_msg1']; ?>
</div>
            <?php if ($this->_tpl_vars['changed_pass'] == 1): ?>
            <div class="alert alert-success"><?php echo $this->_tpl_vars['lang']['ep_msg2']; ?>
</div>
            <meta http-equiv="refresh" content="5;URL=<?php echo @_URL; ?>
">
            <?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'profile-edit-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php else: ?>
		 	<?php if ($this->_tpl_vars['errors']['failure'] != ''): ?>
		 		<?php echo $this->_tpl_vars['errors']['failure']; ?>

			<?php endif; ?>
        
        <?php if ($this->_tpl_vars['nr_errors'] > 0): ?>
        <div class="alert alert-error">
            <ul class="subtle-list">
            <?php $_from = $this->_tpl_vars['errors']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['error']):
?>
                <li><?php echo $this->_tpl_vars['error']; ?>
</li>
            <?php endforeach; endif; unset($_from); ?>
            </ul>
        </div>
        <?php endif; ?> 
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'profile-edit-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
        
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 