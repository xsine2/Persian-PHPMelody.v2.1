<?php /* Smarty version 2.6.20, created on 2014-05-28 11:25:49
         compiled from comment-form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'echo_securimage_sid', 'comment-form.tpl', 29, false),)), $this); ?>
<div name="mycommentspan" id="mycommentspan"></div>
<?php if ($this->_tpl_vars['logged_in'] == '1'): ?>
<div class="row-fluid" id="pm-post-form">
    <div class="span1">
    	<span class="pm-avatar"><img src="<?php echo $this->_tpl_vars['s_avatar_url']; ?>
" height="40" width="40" alt="" class="img-polaroid"></span>
    </div>
    <div class="span11">
      <form action="" name="form-user-comment" method="post" id="myform" class="form-inline">
        <textarea name="comment_txt" id="c_comment_txt" rows="2" class="span12" placeholder="<?php echo $this->_tpl_vars['lang']['your_comment']; ?>
"></textarea>
        <input type="hidden" id="c_vid" name="vid" value="<?php echo $this->_tpl_vars['uniq_id']; ?>
">
        <input type="hidden" id="c_user_id" name="user_id" value="<?php echo $this->_tpl_vars['user_id']; ?>
">
        <p></p>
        <button type="submit" id="c_submit" name="Submit" class="btn btn-small" data-loading-text="<?php echo $this->_tpl_vars['lang']['submit_comment']; ?>
"><?php echo $this->_tpl_vars['lang']['submit_comment']; ?>
</button>
      </form>
    </div>
</div>
<?php elseif ($this->_tpl_vars['logged_in'] == 0 && $this->_tpl_vars['guests_can_comment'] == 1): ?>
<div class="row-fluid" id="pm-post-form">
    <div class="span1">
    	<span class="pm-avatar"><img src="<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/img/pm-avatar.png" width="40" height="40" alt="" border="0" class="img-polaroid"></span>
    </div>
    <div class="span11">
      <form action="" name="form-user-comment" method="post" id="myform" class="form-inline">
        <textarea name="comment_txt" id="c_comment_txt" rows="2" class="span12" placeholder="<?php echo $this->_tpl_vars['lang']['your_comment']; ?>
"></textarea>
        <div id="pm-comment-form">
        <input type="text" id="c_username" name="username" value="<?php echo $this->_tpl_vars['guestname']; ?>
" class="span4 inp-small" placeholder="<?php echo $this->_tpl_vars['lang']['your_name']; ?>
">
        <input type="text" id="captcha" name="captcha" class="span3 inp-small" placeholder="<?php echo $this->_tpl_vars['lang']['confirm_code']; ?>
">
        <button class="btn btn-small btn-link" onclick="document.getElementById('captcha-image').src = '<?php echo @_URL; ?>
/include/securimage_show.php?sid=' + Math.random(); return false"><i class="icon-refresh"></i></button>
        <img src="<?php echo @_URL; ?>
/include/securimage_show.php?sid=<?php echo smarty_echo_securimage_sid(array(), $this);?>
" id="captcha-image" align="absmiddle" alt="">
        <input type="hidden" id="c_vid" name="vid" value="<?php echo $this->_tpl_vars['uniq_id']; ?>
">
        <input type="hidden" id="c_user_id" name="user_id" value="0">
        </div>
        <p></p>
        <button type="submit" id="c_submit" name="Submit" class="btn btn-small" data-loading-text="<?php echo $this->_tpl_vars['lang']['submit_comment']; ?>
"><?php echo $this->_tpl_vars['lang']['submit_comment']; ?>
</button>
      </form>
    </div>
</div>
<?php endif; ?>