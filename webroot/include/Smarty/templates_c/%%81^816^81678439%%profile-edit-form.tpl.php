<?php /* Smarty version 2.6.20, created on 2014-06-24 06:57:45
         compiled from profile-edit-form.tpl */ ?>
<form class="form-horizontal" name="register-form" id="register-form" method="post" action="<?php echo $this->_tpl_vars['form_action']; ?>
" enctype="multipart/form-data">
  <fieldset>
    <legend><?php echo $this->_tpl_vars['lang']['about_me']; ?>
</legend>
    <div class="control-group">
      <label class="control-label" for="name"><?php echo $this->_tpl_vars['lang']['your_name']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="name" <?php if (isset ( $this->_tpl_vars['inputs']['name'] )): ?>value="<?php echo $this->_tpl_vars['inputs']['name']; ?>
"<?php else: ?>value="<?php echo $this->_tpl_vars['userdata']['name']; ?>
"<?php endif; ?>></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="email"><?php echo $this->_tpl_vars['lang']['your_email']; ?>
</label>
      <div class="controls">
      <input type="text" class="input-large" name="email" <?php if (isset ( $this->_tpl_vars['inputs']['email'] )): ?>value="<?php echo $this->_tpl_vars['inputs']['email']; ?>
"<?php else: ?>value="<?php echo $this->_tpl_vars['userdata']['email']; ?>
"<?php endif; ?>>
      <a href="#" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['safe_email']; ?>
"><i class="icon-info-sign"></i> </a>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="favorite"><?php echo $this->_tpl_vars['lang']['my_favorites']; ?>
</label>
      <div class="controls">
      <select name="favorite">
      <option value="1" <?php if (isset ( $this->_tpl_vars['inputs']['favorite'] ) && $this->_tpl_vars['inputs']['favorite'] == 1): ?>selected="selected"<?php elseif ($this->_tpl_vars['userdata']['favorite'] == 1): ?>checked<?php endif; ?>><?php echo $this->_tpl_vars['lang']['public']; ?>
</option>
	<option value="0" <?php if (isset ( $this->_tpl_vars['inputs']['favorite'] ) && $this->_tpl_vars['inputs']['favorite'] == 0): ?>selected="selected"<?php elseif ($this->_tpl_vars['userdata']['favorite'] == 0): ?>checked<?php endif; ?>><?php echo $this->_tpl_vars['lang']['private']; ?>
</option>
      </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="gender"><?php echo $this->_tpl_vars['lang']['gender']; ?>
</label>
      <div class="controls">
      <select name="gender">
      <option value="male" <?php if ($this->_tpl_vars['inputs']['gender'] == 'male'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lang']['male']; ?>
</option>
      <option value="female"<?php if ($this->_tpl_vars['inputs']['gender'] == 'female'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['lang']['female']; ?>
</option>
      </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="country"><?php echo $this->_tpl_vars['lang']['country']; ?>
</label>
      <div class="controls">
      <?php if ($this->_tpl_vars['show_countries_list']): ?>
      <select name="country" size="1" >
      <option value="-1"><?php echo $this->_tpl_vars['lang']['select']; ?>
</option>
          <?php $_from = $this->_tpl_vars['countries_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
          <option value="<?php echo $this->_tpl_vars['k']; ?>
" <?php if ($this->_tpl_vars['inputs']['country'] == $this->_tpl_vars['k']): ?>selected<?php elseif ($this->_tpl_vars['userdata']['country'] == $this->_tpl_vars['k']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['v']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
      </select>
      <?php endif; ?>
        </select>
      </div>
    </div>
    		  <?php if (isset ( $this->_tpl_vars['mm_profile_info_inject'] )): ?><?php echo $this->_tpl_vars['mm_profile_info_inject']; ?>
<?php endif; ?>
    
    <div class="control-group">
      <label class="control-label" for="aboutme"><?php echo $this->_tpl_vars['lang']['about_me']; ?>
</label>
      <div class="controls"><textarea name="aboutme" class=""><?php if (isset ( $this->_tpl_vars['inputs']['aboutme'] )): ?><?php echo $this->_tpl_vars['inputs']['aboutme']; ?>
<?php elseif (isset ( $this->_tpl_vars['userdata']['about'] )): ?><?php echo $this->_tpl_vars['userdata']['about']; ?>
<?php endif; ?></textarea></div>
    </div>
  </fieldset>

  <fieldset>
    <legend><?php echo $this->_tpl_vars['lang']['_social']; ?>
</legend>
    <div class="control-group">
      <label class="control-label" for="website"><?php echo $this->_tpl_vars['lang']['profile_social_website']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="website" <?php if (isset ( $this->_tpl_vars['inputs']['website'] )): ?>value="<?php echo $this->_tpl_vars['inputs']['website']; ?>
"<?php else: ?>value="<?php echo $this->_tpl_vars['userdata']['website']; ?>
"<?php endif; ?> placeholder="http://"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="facebook"><?php echo $this->_tpl_vars['lang']['profile_social_fb']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="facebook" <?php if (isset ( $this->_tpl_vars['inputs']['facebook'] )): ?>value="<?php echo $this->_tpl_vars['inputs']['facebook']; ?>
"<?php else: ?>value="<?php echo $this->_tpl_vars['userdata']['facebook']; ?>
"<?php endif; ?> placeholder="http://"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="twitter"><?php echo $this->_tpl_vars['lang']['profile_social_twitter']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="twitter" <?php if (isset ( $this->_tpl_vars['inputs']['twitter'] )): ?>value="<?php echo $this->_tpl_vars['inputs']['twitter']; ?>
"<?php else: ?>value="<?php echo $this->_tpl_vars['userdata']['twitter']; ?>
"<?php endif; ?> placeholder="http://"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="lastfm"><?php echo $this->_tpl_vars['lang']['profile_social_lastfm']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="lastfm" <?php if (isset ( $this->_tpl_vars['inputs']['lastfm'] )): ?>value="<?php echo $this->_tpl_vars['inputs']['lastfm']; ?>
"<?php else: ?>value="<?php echo $this->_tpl_vars['userdata']['lastfm']; ?>
"<?php endif; ?> placeholder="http://"></div>
    </div>
    <?php if (isset ( $this->_tpl_vars['mm_profile_webfields_inject'] )): ?><?php echo $this->_tpl_vars['mm_profile_webfields_inject']; ?>
<?php endif; ?>
  </fieldset>

  <fieldset>
    <legend><?php echo $this->_tpl_vars['lang']['change_pass']; ?>
</legend>
    <div class="control-group error">
      <label class="control-label" for="pass"><?php echo $this->_tpl_vars['lang']['existing_pass']; ?>
</label>
      <div class="controls"><input type="password" class="input-large" name="pass" maxlength="32"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="new_pass"><?php echo $this->_tpl_vars['lang']['new_pass']; ?>
</label>
      <div class="controls">
      <input type="password" class="input-large" name="new_pass" maxlength="32">
      <p class="help-block"><small><?php echo $this->_tpl_vars['lang']['ep_msg5']; ?>
</small></p>
      </div>
    </div>
    
    <div class="">
        <div class="controls">
        <button type="submit" name="save" value="<?php echo $this->_tpl_vars['lang']['submit_save']; ?>
" class="btn btn-success" data-loading-text="<?php echo $this->_tpl_vars['lang']['submit_save']; ?>
"><?php echo $this->_tpl_vars['lang']['submit_save']; ?>
</button>
        </div>
    </div>
  </fieldset>
</form>