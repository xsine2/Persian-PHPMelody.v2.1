<?php /* Smarty version 2.6.20, created on 2014-05-28 11:26:01
         compiled from comment-list-li-body.tpl */ ?>
<span class="pm-comment-avatar">
	<?php if ($this->_tpl_vars['comment_data']['user_id'] == 0): ?>
		<span class="pm-img-avatar"><img src="<?php echo $this->_tpl_vars['comment_data']['avatar_url']; ?>
" height="40" width="40" alt="" class="img-polaroid"></span>
	<?php else: ?>
		<span class="pm-img-avatar"><a href="<?php echo $this->_tpl_vars['comment_data']['user_profile_href']; ?>
"><img src="<?php echo $this->_tpl_vars['comment_data']['avatar_url']; ?>
" height="40" width="40" alt="" class="img-polaroid"></a></span>
	<?php endif; ?>
</span>
<span class="pm-comment-info">
    <span class="pm-comment-author">
    	<?php if ($this->_tpl_vars['comment_data']['user_id'] == 0): ?> 
			<?php echo $this->_tpl_vars['comment_data']['name']; ?>

		<?php else: ?> 
			<?php if ($this->_tpl_vars['comment_data']['user_is_banned']): ?>
				<a href="<?php echo $this->_tpl_vars['comment_data']['user_profile_href']; ?>
">
					<span class="pm-comment-banned"><?php echo $this->_tpl_vars['comment_data']['name']; ?>
</span>
				</a>
			<?php else: ?>
				<a href="<?php echo $this->_tpl_vars['comment_data']['user_profile_href']; ?>
"><?php echo $this->_tpl_vars['comment_data']['name']; ?>
</a>
			<?php endif; ?>
			<span class="label-banned-<?php echo $this->_tpl_vars['comment_data']['user_id']; ?>
 label label-important <?php if (! $this->_tpl_vars['comment_data']['user_is_banned']): ?>hide<?php endif; ?>"><?php echo $this->_tpl_vars['lang']['user_account_banned_label']; ?>
</span>
		<?php endif; ?>

	<?php if ($this->_tpl_vars['can_manage_comments']): ?>
	<span class="opac5"><small>(<?php echo $this->_tpl_vars['comment_data']['user_ip']; ?>
)</small></span><!-- author ip -->
    <?php endif; ?>
	</span>
    <span class="pm-comment-since"><small><?php echo $this->_tpl_vars['lang']['added']; ?>
 <time datetime="<?php echo $this->_tpl_vars['comment_data']['html5_datetime']; ?>
" title="<?php echo $this->_tpl_vars['comment_data']['full_datetime']; ?>
"><?php echo $this->_tpl_vars['comment_data']['time_since_added']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</time></small></span>
    <span class="pm-comment-txt"><?php echo $this->_tpl_vars['comment_data']['comment']; ?>
</span>
</span>
<?php if ($this->_tpl_vars['logged_in']): ?>
<span class="pm-comment-action" id="users-<?php echo $this->_foreach['comment_foreach']['iteration']; ?>
">
	<div class="btn-group pull-right">
		<?php if ($this->_tpl_vars['comment_data']['user_id'] > 0 && $this->_tpl_vars['comment_data']['user_id'] != $this->_tpl_vars['s_user_id'] && $this->_tpl_vars['can_manage_comments'] && $this->_tpl_vars['comment_data']['power'] != @U_ADMIN): ?>
			<?php if ($this->_tpl_vars['comment_data']['user_is_banned']): ?>
				<button class="unban-<?php echo $this->_tpl_vars['comment_data']['user_id']; ?>
 btn btn-mini active" type="button" id="unban-<?php echo $this->_tpl_vars['comment_data']['id']; ?>
" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['user_account_remove_ban']; ?>
"><i class="icon-ban-circle opac7"></i></button>
			<?php else: ?>
				<button class="ban-<?php echo $this->_tpl_vars['comment_data']['user_id']; ?>
 btn btn-mini" type="button" id="ban-<?php echo $this->_tpl_vars['comment_data']['id']; ?>
" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['user_account_add_ban']; ?>
"><i class="icon-ban-circle opac7"></i></button>
			<?php endif; ?>
		<?php endif; ?>
		<button class="btn btn-mini <?php if ($this->_tpl_vars['comment_data']['user_likes_this']): ?>active<?php endif; ?>" type="button" <?php if ($this->_tpl_vars['comment_data']['user_id'] != $this->_tpl_vars['s_user_id']): ?>id="comment-like-<?php echo $this->_tpl_vars['comment_data']['id']; ?>
"<?php endif; ?> rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['_like']; ?>
"><i class="icon-thumbs-up opac7"></i>
		<span id="comment-like-count-<?php echo $this->_tpl_vars['comment_data']['id']; ?>
">
		<?php if ($this->_tpl_vars['comment_data']['up_vote_count'] > 0): ?>
			<?php echo $this->_tpl_vars['comment_data']['up_vote_count']; ?>

		<?php endif; ?>
		</span>
		</button>
		<button class="btn btn-mini <?php if ($this->_tpl_vars['comment_data']['user_dislikes_this']): ?>active<?php endif; ?>" type="button" <?php if ($this->_tpl_vars['comment_data']['user_id'] != $this->_tpl_vars['s_user_id']): ?>id="comment-dislike-<?php echo $this->_tpl_vars['comment_data']['id']; ?>
"<?php endif; ?> rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['_dislike']; ?>
"><i class="icon-thumbs-down opac7"></i>
		<span id="comment-dislike-count-<?php echo $this->_tpl_vars['comment_data']['id']; ?>
">
		<?php if ($this->_tpl_vars['comment_data']['down_vote_count'] > 0): ?>
			<?php echo $this->_tpl_vars['comment_data']['down_vote_count']; ?>

		<?php endif; ?>
		</span>
		</button>
		<button class="btn btn-mini <?php if ($this->_tpl_vars['comment_data']['user_flagged_this']): ?>active<?php endif; ?>" type="button" id="comment-flag-<?php echo $this->_tpl_vars['comment_data']['id']; ?>
" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['report_form5']; ?>
"><i class="icon-flag opac7"></i></button>
	<?php if ($this->_tpl_vars['can_manage_comments']): ?>
	<button class="btn btn-mini btn-warning" onclick="onpage_delete_comment('<?php echo $this->_tpl_vars['comment_data']['id']; ?>
', '<?php echo $this->_tpl_vars['comment_data']['uniq_id']; ?>
', '#comment-<?php echo $this->_foreach['comment_foreach']['iteration']; ?>
'); return false;" rel="tooltip" title="Delete comment"><i class="icon-remove opac7"></i></button>
	<?php endif; ?>
	</div>
</span>
<?php endif; ?>