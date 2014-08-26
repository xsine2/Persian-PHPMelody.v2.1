<?php /* Smarty version 2.6.20, created on 2014-06-28 17:18:12
         compiled from user-follow-button.tpl */ ?>
<?php if (! $this->_tpl_vars['profile_data']['am_following']): ?>
	<button id="btn_follow_<?php echo $this->_tpl_vars['profile_user_id']; ?>
" class="btn btn-small btn-follow border-radius4"><?php echo $this->_tpl_vars['lang']['follow']; ?>
</button>
<?php else: ?>
	<button id="btn_unfollow_<?php echo $this->_tpl_vars['profile_user_id']; ?>
" class="btn btn-unfollow btn-small border-radius4"><i class="icon-ok icon-white"></i> <?php echo $this->_tpl_vars['lang']['following']; ?>
</button>
<?php endif; ?>