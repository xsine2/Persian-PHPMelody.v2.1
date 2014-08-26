<?php /* Smarty version 2.6.20, created on 2014-06-29 18:08:01
         compiled from profile-upload-avatar.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('no_index' => '1','p' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
            <nav id="second-nav" class="tabbable" role="navigation">
                <ul class="nav nav-tabs pull-right">
                <li><a href="<?php echo @_URL; ?>
/edit_profile.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['edit_profile']; ?>
</a></li>
                <li class="active"><a href="<?php echo @_URL; ?>
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
        
        <h1><?php echo $this->_tpl_vars['lang']['update_avatar']; ?>
</h1>
        <hr />
        <?php if (! empty ( $this->_tpl_vars['err_msg'] )): ?>
            <div class="alert alert-warning">
            <?php echo $this->_tpl_vars['err_msg']; ?>

            </div>
        <?php endif; ?>
        <?php if (! empty ( $this->_tpl_vars['success_msg'] )): ?>
            <div class="alert alert-success">
            <?php echo $this->_tpl_vars['success_msg']; ?>

            <a href="<?php echo @_URL; ?>
/profile.<?php echo @_FEXT; ?>
?u=<?php echo $this->_tpl_vars['s_username']; ?>
" rel="nofollow"><?php echo $this->_tpl_vars['lang']['return_to_profile']; ?>
</a>
            </div>
        <?php endif; ?>
		<?php if (empty ( $this->_tpl_vars['success_msg'] )): ?>
        <form class="form-horizontal" name="update-avatar-form" method="post" action="<?php echo @_URL; ?>
/upload_avatar.php" enctype="multipart/form-data">
          <fieldset>
            <div class="row-fluid">
              <div class="span2">
              <img src="<?php echo $this->_tpl_vars['avatar']; ?>
" border="0" alt="" class="img-polaroid">
              </div>
              <div class="span10">
                <div class="control-group">
                  <label class="control-label" for="input01"><?php echo $this->_tpl_vars['lang']['ua_msg2']; ?>
</label>
                  <div class="controls">
                  <input name="imagefile" type="file" class="span7" size="20"> 
                  </div>
                </div>
                <div class="">
                    <div class="controls">
                    <button name="submit" type="submit" value="<?php echo $this->_tpl_vars['lang']['submit_upload']; ?>
" class="btn btn-success" data-loading-text="<?php echo $this->_tpl_vars['lang']['submit_upload']; ?>
"><?php echo $this->_tpl_vars['lang']['submit_upload']; ?>
</button>
                    </div>
                </div>
              </div>
            </div>
          </fieldset>
        </form>
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