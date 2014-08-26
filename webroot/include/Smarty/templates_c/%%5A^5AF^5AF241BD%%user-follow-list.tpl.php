<?php /* Smarty version 2.6.20, created on 2014-06-28 17:18:55
         compiled from user-follow-list.tpl */ ?>
<ul class="pm-ul-memberlist">
	<?php $_from = $this->_tpl_vars['profile_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['profile_user_id'] => $this->_tpl_vars['profile']):
?>
		<li>
			<span class="pm-ml-username"><a href="<?php echo $this->_tpl_vars['profile']['profile_url']; ?>
"><?php echo $this->_tpl_vars['profile']['name']; ?>
</a> 
              <?php if ($this->_tpl_vars['profile']['is_following_me']): ?>
                  <span class="label pm-follows"><?php echo $this->_tpl_vars['lang']['follow_following_you']; ?>
</span>
              <?php endif; ?>
          	</span>
			<span class="pm-ml-avatar"><a href="<?php echo $this->_tpl_vars['profile']['profile_url']; ?>
"><img src="<?php echo $this->_tpl_vars['profile']['avatar_url']; ?>
" alt="<?php echo $this->_tpl_vars['profile']['username']; ?>
" width="60" height="60" border="0" class="img-polaroid"></a></span>

			<div class="pm-ml-buttons">
			<?php if ($this->_tpl_vars['profile_user_id'] != $this->_tpl_vars['s_user_id']): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "user-follow-button.tpl", 'smarty_include_vars' => array('profile_data' => $this->_tpl_vars['profile'],'profile_user_id' => $this->_tpl_vars['profile_user_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
            </div>
            <div class="clearfix"></div>
		</li>
	<?php endforeach; endif; unset($_from); ?>
	<?php if ($this->_tpl_vars['follow_count'] == 0): ?>
		<?php echo $this->_tpl_vars['lang']['memberlist_msg3']; ?>

	<?php endif; ?>
</ul>
<?php if ($this->_tpl_vars['total_profiles'] == @FOLLOW_PROFILES_PER_PAGE): ?>
	<div class="clearfix"></div>
	<span id="btn_follow_load_more"></span>
<?php endif; ?>