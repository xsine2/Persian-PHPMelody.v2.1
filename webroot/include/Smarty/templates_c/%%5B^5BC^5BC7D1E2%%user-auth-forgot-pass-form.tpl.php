<?php /* Smarty version 2.6.20, created on 2014-06-23 15:22:55
         compiled from user-auth-forgot-pass-form.tpl */ ?>
<form class="form-horizontal" name="forgot-pass" id="reset-form" method="post" action="<?php echo @_URL; ?>
/login.php?do=forgot_pass">
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="input01"><?php echo $this->_tpl_vars['lang']['your_username_or_email']; ?>
</label>
      <div class="controls"><input type="text" class="input-large" name="username_email" placeholder="" value="<?php echo $this->_tpl_vars['inputs']['username_email']; ?>
"></div>
    </div>
    <div class="">
        <div class="controls">
        <button type="submit" name="Send" value="<?php echo $this->_tpl_vars['lang']['submit_send']; ?>
" class="btn btn-blue" data-loading-text="<?php echo $this->_tpl_vars['lang']['submit_send']; ?>
"><?php echo $this->_tpl_vars['lang']['submit_send']; ?>
</button>
        </div>
    </div>
  </fieldset>
</form>