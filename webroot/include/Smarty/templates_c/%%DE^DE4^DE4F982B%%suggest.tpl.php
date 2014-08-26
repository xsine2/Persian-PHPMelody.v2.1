<?php /* Smarty version 2.6.20, created on 2014-07-02 04:37:24
         compiled from suggest.tpl */ ?>
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
                <li><a href="<?php echo @_URL; ?>
/upload_avatar.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['update_avatar']; ?>
</a></li>
                <li><a href="<?php echo @_URL; ?>
/favorites.<?php echo @_FEXT; ?>
?a=show"><?php echo $this->_tpl_vars['lang']['my_favorites']; ?>
</a></li>
                <?php if (@_ALLOW_USER_SUGGESTVIDEO == '1'): ?>
                <li class="active"><a href="<?php echo @_URL; ?>
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
		<div id="primary" class="extra-space">
        
        <h1><?php echo $this->_tpl_vars['lang']['suggest']; ?>
</h1>
        <hr />
        
        <?php if ($this->_tpl_vars['success'] == 3): ?>
            <div class="alert alert-info">
            <?php echo $this->_tpl_vars['lang']['suggest_msg1']; ?>

            </div>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['success'] == 4): ?>
            <div class="alert alert-info">
            <?php echo $this->_tpl_vars['lang']['suggest_msg2']; ?>

            </div>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['success'] == 5): ?>
            <div class="alert alert-error">
            <?php echo $this->_tpl_vars['lang']['suggest_msg3']; ?>

            </div>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['success'] == 1): ?>
            <div class="alert alert-success">
            <?php echo $this->_tpl_vars['lang']['suggest_msg4']; ?>

            <a href="suggest.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['add_another_one']; ?>
</a> | <a href="index.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['return_home']; ?>
</a>
            </div>
        <?php else: ?>
        
        <?php if (count ( $this->_tpl_vars['errors'] ) > 0): ?>
        <div class="alert alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="subtle-list">
            <?php $_from = $this->_tpl_vars['errors']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
            	<li><?php echo $this->_tpl_vars['v']; ?>
</li>
            <?php endforeach; endif; unset($_from); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form class="form-horizontal" id="suggest-form" name="suggest-form" method="post" action="<?php echo $this->_tpl_vars['form_action']; ?>
">
          <fieldset>
            <div class="control-group">
              <label class="control-label" for="pm_sources"><?php echo $this->_tpl_vars['lang']['_videourl']; ?>
</label>
              <div class="controls">
                <input type="text" class="span8" name="yt_id" value="<?php echo $_POST['yt_id']; ?>
" placeholder="http://"> <span class="hide" id="loading-gif-top"><img src="<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/img/ajax-loading.gif" width="" height="" alt=""></span>
              </div>
            </div>
            <div class="hide" id="suggest-video-extra">
                <div class="hide" id="video-thumb-placeholder"></div>
                <div class="control-group">
                  <label class="control-label" for="video_title"><?php echo $this->_tpl_vars['lang']['video']; ?>
</label>
                  <div class="controls">
                  <input type="text" class="span5" name="video_title" value="<?php echo $_POST['video_title']; ?>
">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="category"><?php echo $this->_tpl_vars['lang']['category']; ?>
</label>
                  <div class="controls">
                    <?php echo $this->_tpl_vars['categories_dropdown']; ?>

                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="description"><?php echo $this->_tpl_vars['lang']['description']; ?>
</label>
                  <div class="controls">
                    <textarea name="description" class="span5" rows="5"><?php if ($_POST['description']): ?><?php echo $_POST['description']; ?>
<?php endif; ?></textarea>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="tags"><?php echo $this->_tpl_vars['lang']['tags']; ?>
</label>
                  <div class="controls">
                    <div class="tagsinput">
                      <input id="tags_suggest" type="text" class="tags" name="tags" value="<?php echo $_POST['tags']; ?>
">  <span class="help-inline"><a href="#" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['suggest_ex']; ?>
"><i class="icon-info-sign"></i></a></span>
                    </div>
                  </div>
                </div>
                <div class="">
                  <div class="controls">
                    <button class="btn btn-success" name="Submit" id="Submit" value="<?php echo $this->_tpl_vars['lang']['submit_submit']; ?>
" type="submit"><?php echo $this->_tpl_vars['lang']['submit_submit']; ?>
</button> <span class="hide" id="loading-gif-bottom"><img src="<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/img/ajax-loading.gif" width="" height="" alt=""></span>
                  </div>
                </div>
            </div><!-- #suggest-video-extra -->
            <div class="alert hide" id="ajax-error-placeholder"></div>
            <div class="alert alert-success hide" id="ajax-success-placeholder"></div>
			<input type="hidden" name="source_id" value="-1" />
			<input type="hidden" name="p" value="suggest" />
			<input type="hidden" name="do" value="submitvideo" />
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