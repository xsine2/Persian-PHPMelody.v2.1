<?php /* Smarty version 2.6.20, created on 2014-06-28 17:18:46
         compiled from activity-stream-item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strtolower', 'activity-stream-item.tpl', 11, false),)), $this); ?>
<li class="pm-li-activity" id="activity-<?php echo $this->_tpl_vars['activity_meta']['activity_id']; ?>
">
	
	<span class="pm-ml-avatar"><a href="<?php echo $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['0']['profile_url']; ?>
"><img src="<?php echo $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['0']['avatar_url']; ?>
" alt="<?php echo $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['0']['username']; ?>
" width="40" height="40" border="0" class="img-polaroid"></a></span>
	<div class="pm-ml-activity">
	<span class="pm-ml-username">
		<a href="<?php echo $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['0']['profile_url']; ?>
"><?php echo $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['0']['name']; ?>
</a>
	</span>
	
	<?php if ($this->_tpl_vars['activity_meta']['actors_count'] > 2): ?>
		<?php echo $this->_tpl_vars['lang']['and']; ?>
 
		<a href="#"><?php echo $this->_tpl_vars['activity_meta']['actors_count']-1; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['lang']['other'])) ? $this->_run_mod_handler('strtolower', true, $_tmp) : strtolower($_tmp)); ?>
</a>
		(
			<?php $_from = $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['actors_foreach'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['actors_foreach']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['kk'] => $this->_tpl_vars['actor']):
        $this->_foreach['actors_foreach']['iteration']++;
?>
				<?php if ($this->_tpl_vars['kk'] > 0): ?>
					<a href="<?php echo $this->_tpl_vars['actor']['profile_url']; ?>
"><?php echo $this->_tpl_vars['actor']['name']; ?>
</a><?php if (! ($this->_foreach['actors_foreach']['iteration'] == $this->_foreach['actors_foreach']['total'])): ?>,<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
		)
	<?php elseif ($this->_tpl_vars['activity_meta']['actors_count'] == 2): ?>
		<?php echo $this->_tpl_vars['lang']['and']; ?>
 <a href="<?php echo $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['1']['profile_url']; ?>
"><?php echo $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['1']['name']; ?>
</a>
	<?php endif; ?>
    
	<?php if ($this->_tpl_vars['activity_meta']['activity_type'] == @ACT_TYPE_STATUS): ?>
    	<div class="clearfix"></div>
		<div class="pm-ml-speech top"><?php echo $this->_tpl_vars['activity_meta']['metadata']['statustext']; ?>
</div>
	<?php endif; ?>
	<?php echo $this->_tpl_vars['activity_meta']['lang']; ?>

	
	<?php if ($this->_tpl_vars['activity_meta']['objects_count'] == 1): ?>
		<?php if ($this->_tpl_vars['activity_meta']['object_type'] == @ACT_OBJ_USER): ?>
			<a href="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['profile_url']; ?>
"><?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['name']; ?>
</a>.
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['activity_meta']['object_type'] == @ACT_OBJ_VIDEO): ?>
			<a href="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['video_href']; ?>
"><?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['video_title']; ?>
</a>
				<span class="pm-video-thumb pm-thumb-76 pm-thumb border-radius2">
			    <a href="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-76"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['thumb_img_url']; ?>
" width="76"><span class="vertical-align"></span></span></a>
			    </span>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['activity_meta']['object_type'] == @ACT_OBJ_ARTICLE): ?>
			<a href="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['link']; ?>
"><?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['title']; ?>
</a>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['activity_meta']['object_type'] == @ACT_OBJ_PROFILE): ?>
			
		<?php endif; ?>	
		
	<?php elseif ($this->_tpl_vars['activity_meta']['objects_count'] == 2): ?>
		<a href="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['profile_url']; ?>
"><?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['name']; ?>
</a> <?php echo $this->_tpl_vars['lang']['and']; ?>
 <a href="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['1']['profile_url']; ?>
"><?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['1']['name']; ?>
</a> 
	<?php else: ?>
		<a href="<?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['profile_url']; ?>
"><?php echo $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]['0']['name']; ?>
</a> <?php echo $this->_tpl_vars['lang']['and']; ?>
 
		<a href="#"><?php echo $this->_tpl_vars['activity_meta']['objects_count']-1; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['lang']['other'])) ? $this->_run_mod_handler('strtolower', true, $_tmp) : strtolower($_tmp)); ?>
</a>
		(
		<?php $_from = $this->_tpl_vars['object_bucket'][$this->_tpl_vars['activity_index']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['objects_foreach'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['objects_foreach']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['kk'] => $this->_tpl_vars['obj']):
        $this->_foreach['objects_foreach']['iteration']++;
?>
			<?php if ($this->_tpl_vars['kk'] > 0): ?>
				<a href="<?php echo $this->_tpl_vars['obj']['profile_url']; ?>
"><?php echo $this->_tpl_vars['obj']['name']; ?>
</a><?php if (! ($this->_foreach['objects_foreach']['iteration'] == $this->_foreach['objects_foreach']['total'])): ?>,<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		)
	<?php endif; ?>
	
	
	<?php if ($this->_tpl_vars['activity_meta']['targets_count'] == 1): ?>
		<?php if ($this->_tpl_vars['activity_meta']['target_type'] == @ACT_OBJ_ARTICLE): ?>
			<a href="<?php echo $this->_tpl_vars['target_bucket'][$this->_tpl_vars['activity_index']]['0']['link']; ?>
"><?php echo $this->_tpl_vars['target_bucket'][$this->_tpl_vars['activity_index']]['0']['title']; ?>
</a>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['activity_meta']['target_type'] == @ACT_OBJ_VIDEO): ?>
			
			<a href="<?php echo $this->_tpl_vars['target_bucket'][$this->_tpl_vars['activity_index']]['0']['video_href']; ?>
"><?php echo $this->_tpl_vars['target_bucket'][$this->_tpl_vars['activity_index']]['0']['video_title']; ?>
</a>
				<span class="pm-video-thumb pm-thumb-76 pm-thumb border-radius2">
			    <a href="<?php echo $this->_tpl_vars['target_bucket'][$this->_tpl_vars['activity_index']]['0']['video_href']; ?>
" class="pm-thumb-fix pm-thumb-76"><span class="pm-thumb-fix-clip"><img src="<?php echo $this->_tpl_vars['target_bucket'][$this->_tpl_vars['activity_index']]['0']['thumb_img_url']; ?>
" width="76"><span class="vertical-align"></span></span></a>
			    </span>
		<?php endif; ?>
	<?php elseif ($this->_tpl_vars['activity_meta']['targets_count'] == 2): ?>
	
	<?php else: ?>
	
	<?php endif; ?>
	</div><!-- .pm-ml-activity -->
	<div class="pm-ml-activity-date">
    <small><?php echo $this->_tpl_vars['activity_meta']['time_since']; ?>
 <?php echo $this->_tpl_vars['lang']['ago']; ?>
</small>
	<?php if ($this->_tpl_vars['s_user_id'] == $this->_tpl_vars['actor_bucket'][$this->_tpl_vars['activity_index']]['0']['user_id']): ?>
		<a href="#" class="pull-right pm-li-activity-hide opac7" id="hide-activity-<?php echo $this->_tpl_vars['activity_meta']['activity_id']; ?>
" rel="tooltip" title="<?php echo $this->_tpl_vars['lang']['hide_from_stream']; ?>
"><i class="icon-remove"></i> </a>
	<?php endif; ?>
    </div>
	<div class="clearfix"></div>
</li>