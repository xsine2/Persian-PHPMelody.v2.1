<?php /* Smarty version 2.6.20, created on 2014-06-28 15:35:31
         compiled from user-register-form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'echo_securimage_sid', 'user-register-form.tpl', 45, false),)), $this); ?>
<?php if ($this->_tpl_vars['allow_registration'] == '1'): ?>
<form class="form-horizontal" id="register-form" name="register-form" method="post" action="<?php echo @_URL; ?>
/register.php">
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="name"><?php echo $this->_tpl_vars['lang']['your_name']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="name" value="<?php echo $this->_tpl_vars['inputs']['name']; ?>
"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="username"><?php echo $this->_tpl_vars['lang']['username']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="username" value="<?php echo $this->_tpl_vars['inputs']['username']; ?>
"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="email"><?php echo $this->_tpl_vars['lang']['your_email']; ?>
</label>
      <div class="controls"><input type="email" class="input-large" id="email" name="email" value="<?php echo $this->_tpl_vars['inputs']['email']; ?>
" autocomplete="off"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="pass"><?php echo $this->_tpl_vars['lang']['password']; ?>
</label>
      <div class="controls"><input type="password" class="input-large" id="pass" name="pass" maxlength="32" autocomplete="off"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="confirm_pass"><?php echo $this->_tpl_vars['lang']['password_retype']; ?>
</label>
      <div class="controls">
      <input type="password" class="input-large" id="confirm_pass" name="confirm_pass" maxlength="32">
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
" <?php if ($this->_tpl_vars['inputs']['country'] == $this->_tpl_vars['k']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['v']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
		<?php endif; ?>
		<input type="text" name="website" class="input-large botmenot" maxlength="32">
      </div>
    </div>
<?php if (isset ( $this->_tpl_vars['mm_register_fields_inject'] )): ?><?php echo $this->_tpl_vars['mm_register_fields_inject']; ?>
<?php endif; ?>
	<?php if ($this->_tpl_vars['spambot_prevention'] == 'securimage'): ?>
    <div class="control-group">
        <div class="controls">
        	<input type="text" name="imagetext" class="input-large" autocomplete="off" placeholder="<?php echo $this->_tpl_vars['lang']['enter_captcha']; ?>
">
            <img src="<?php echo @_URL; ?>
/include/securimage_show.php?sid=<?php echo smarty_echo_securimage_sid(array(), $this);?>
" id="image" align="absmiddle" alt="" class="img-rounded">
            <button class="btn btn-link btn-large" onclick="document.getElementById('image').src = '<?php echo @_URL; ?>
/include/securimage_show.php?sid=' + Math.random(); return false">
            <i class="icon-refresh"></i>
            </button>
        </div>
    </div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['spambot_prevention'] == 'recaptcha'): ?>
	<div class="control-group">
        <div class="controls">
			<?php echo $this->_tpl_vars['recaptcha_html']; ?>

		</div>
	</div>
	<?php endif; ?>
	
    <div class="control-group">
      <div class="controls">
      <label class="checkbox">
      <input type="checkbox" class="checkbox" id="agree" name="agree" <?php if ($this->_tpl_vars['inputs']['agree'] == 'on'): ?>checked="checked"<?php endif; ?>> <span class="help-inline">من با  <a data-toggle="modal" href="#terms" id="element" >قوانین و مقررات سایت</a> آشنا و موافق هستم .</span>
      </label>
      </div>
    </div>
    
    <div class="">
        <div class="controls">
        <input type="hidden" class="input-large" name="gender" value="male">
        <button type="submit" name="Register" value="<?php echo $this->_tpl_vars['lang']['register']; ?>
" class="btn btn-blue" data-loading-text="<?php echo $this->_tpl_vars['lang']['register']; ?>
"><?php echo $this->_tpl_vars['lang']['register']; ?>
</button>
        </div>
    </div>
  </fieldset>
</form>
<?php else: ?>
<?php echo $this->_tpl_vars['lang']['registration_closed']; ?>

<?php endif; ?>