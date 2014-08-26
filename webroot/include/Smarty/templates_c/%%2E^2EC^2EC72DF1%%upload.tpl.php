<?php /* Smarty version 2.6.20, created on 2014-06-24 07:02:32
         compiled from upload.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('no_index' => '1','p' => 'upload','tpl_name' => 'upload')));
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
                <li><a href="<?php echo @_URL; ?>
/suggest.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['suggest']; ?>
</a></li>
                <?php endif; ?>
                <?php if (@_ALLOW_USER_UPLOADVIDEO == '1'): ?>
                <li class="active"><a href="<?php echo @_URL; ?>
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

		<h1><?php echo $this->_tpl_vars['lang']['upload_video']; ?>
</h1>
 		<hr />
		<?php if ($this->_tpl_vars['success'] == 1): ?>
			<div class="alert alert-success">
			<?php echo $this->_tpl_vars['lang']['suggest_msg4']; ?>

			<br />
			<a href="upload.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['add_another_one']; ?>
</a> یا <a href="index.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['return_home']; ?>
</a>
			</div>
		<?php elseif ($this->_tpl_vars['success'] == 2): ?>
			<div class="alert alert-success">
			<?php echo $this->_tpl_vars['lang']['upload_errmsg11']; ?>
 
			<a href="index.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['return_home']; ?>
</a>
			</div>
		<?php elseif ($this->_tpl_vars['success'] == 'custom'): ?>
			<div class="alert alert-success">
			<?php echo $this->_tpl_vars['success_custom_message']; ?>
 
			<a href="index.<?php echo @_FEXT; ?>
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
			<form class="form-horizontal" name="upload-video-form" id="upload-video-form" enctype="multipart/form-data" method="post" action="<?php echo $this->_tpl_vars['form_action']; ?>
">
			<div class="alert hide" id="error-placeholder"></div>
			<fieldset>
			    <div class="control-group">
			      <label class="control-label" for="mediafile"><?php echo $this->_tpl_vars['lang']['upload_video1']; ?>
</label>
			      <div class="controls">
					<span class="btn-upload border-radius4" rel="tooltip" title="*.wmv,*.mov,*.qt,*.3gp,*.3gpp,*.3g2,*.3gp2,*.mpg,*.mpeg,*.mp1,
                        *.mp2,*.m1v,*.m1a,*.m2a,*.mpa,*.mpv,*.mpv2,*.mpe,*.mp4,*.m4a,
						*.m4p,*.m4b,*.m4r,*.m4v,*.avi,*.flv,*.f4v,*.f4p,*.f4a,*.f4b,
						*.vob,*.lsf,*.lsx,*.asf,*.asr,*.asx,*.webm,*.mkv<br /> حداکثر حجم فایل: <?php echo $this->_tpl_vars['upload_limit']; ?>
"><span id="uploadButtonPlaceholder"></span></span>
					<div>
					<small><div id="uploadProgressBar"></div></small>
					<div id="divStatus"></div>
					<ol id="uploadLog"></ol>
					</div>
					<input type="hidden" name="form_id" value="<?php echo $this->_tpl_vars['form_id']; ?>
" />
					<input type="hidden" name="_pmnonce_t" value="<?php echo $this->_tpl_vars['form_csrfguard_token']; ?>
" />
					<input type="hidden" name="temp_id" id="temp_id" value="" />


			      </div>
			    </div>
			    <div class="control-group">
			      <label class="control-label" for="capture"><?php echo $this->_tpl_vars['lang']['upload_video2']; ?>
</label>
			      <div class="controls">
						<input type="file" name="capture" value="" size="40">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->_tpl_vars['max_file_size']; ?>
">
						<span class="help-inline"><a href="#" rel="tooltip" title="*.jpg, *.jpeg, *.gif, *.png"><i class="icon-info-sign"></i></a></span>
			      </div>
			    </div>
				<div  id="upload-video-extra">
					<div class="control-group">
				      <label class="control-label" for="video_title"><?php echo $this->_tpl_vars['lang']['video']; ?>
</label>
				      <div class="controls">
				      <input name="video_title" type="text" value="<?php echo $_POST['video_title']; ?>
" class="input-large">
				      </div>
				    </div>
					<div class="control-group">
				      <label class="control-label" for="duration"><?php echo $this->_tpl_vars['lang']['_duration']; ?>
</label>
				      <div class="controls">
				      <input name="duration" id="duration" type="text" value="<?php echo $_POST['duration']; ?>
" class="input-mini" style="text-align: center;">
                      <span class="help-inline"><a href="#" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['duration_format']; ?>
"><i class="icon-info-sign"></i></a></span>
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
						<textarea name="description" style="margin: 0px;height: 150px;width: 620px;max-width: 620px;max-height: 300px;" class="span5" rows="3"><?php if ($_POST['description']): ?><?php echo $_POST['description']; ?>
<?php endif; ?></textarea>
				      </div>
				    </div>
				    <div class="control-group">
				      <label class="control-label" for="tags"><?php echo $this->_tpl_vars['lang']['tags']; ?>
</label>
				      <div class="controls">
						<div class="tagsinput">
				          <input id="tags_upload" name="tags" type="text" class="tags" value="<?php echo $_POST['tags']; ?>
"> <span class="help-inline"><a href="#" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['suggest_ex']; ?>
"><i class="icon-info-sign"></i></a></span>
				        </div>
				      </div>
				    </div>
				    <?php if (isset ( $this->_tpl_vars['mm_upload_fields_inject'] )): ?><?php echo $this->_tpl_vars['mm_upload_fields_inject']; ?>
<?php endif; ?>
				    <div class="">
				      <div class="controls">
						<button name="Submit" type="submit" id="upload_btn" value="<?php echo $this->_tpl_vars['lang']['submit_upload']; ?>
" class="btn btn-success" data-loading-text="<?php echo $this->_tpl_vars['lang']['submit_send']; ?>
"><?php echo $this->_tpl_vars['lang']['submit_upload']; ?>
</button>
						<span id="uploading_gif">
						</span>
				      </div>
				    </div>
				</div><!-- #upload-video-extra -->
				
			</fieldset>
			</form>
		<?php endif; ?>
        </div><!-- #primary -->
        </div><!-- .span12 -->
    </div><!-- .row-fluid --> 
  </div>
  </div>
  <!-- .container-fluid -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array('tpl_name' => 'upload')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>