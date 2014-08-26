<?php /* Smarty version 2.6.20, created on 2014-07-07 13:45:26
         compiled from memberlist.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('p' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
    <div class="container-fluid">
      <?php if ($this->_tpl_vars['logged_in'] == '1'): ?>
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
                <li><a href="<?php echo @_URL; ?>
/upload.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['upload_video']; ?>
</a></li>
                <?php endif; ?>
                <li class="active"><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['members_list']; ?>
</a></li>
				<?php if (isset ( $this->_tpl_vars['mm_profilemenu_insert'] )): ?><?php echo $this->_tpl_vars['mm_profilemenu_insert']; ?>
<?php endif; ?>
                </ul>
            </nav><!-- #site-navigation -->
        </div>
      </div>
      <?php endif; ?>

      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary">
			<h1><?php echo @_SITENAME; ?>
 <?php echo $this->_tpl_vars['lang']['members']; ?>
</h1>
			
			<div id="sorting">
			    <div class="btn-group btn-group-sort">
			    <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                <?php if ($this->_tpl_vars['gv_sortby'] == ''): ?><?php echo $this->_tpl_vars['lang']['sorting']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['gv_sortby'] == 'name'): ?><?php echo $this->_tpl_vars['lang']['name']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['gv_sortby'] == 'lastseen'): ?><?php echo $this->_tpl_vars['lang']['last_seen']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['gv_sortby'] == 'online'): ?><?php echo $this->_tpl_vars['lang']['whois_online']; ?>
<?php endif; ?>
			    <span class="caret"></span>
			    </a>
			    <ul class="dropdown-menu pull-right">
			        <li style="text-align: right;"><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?page=<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
&sortby=name" rel="nofollow" class="<?php if ($this->_tpl_vars['gv_sortby'] == 'name'): ?>selected<?php endif; ?>"><?php echo $this->_tpl_vars['lang']['name']; ?>
</a></li>
			        <li style="text-align: right;"><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?page=<?php echo $this->_tpl_vars['gv_pagenumber']; ?>
&sortby=lastseen" rel="nofollow" class="<?php if ($this->_tpl_vars['gv_sortby'] == 'lastseen'): ?>selected<?php endif; ?>"><?php echo $this->_tpl_vars['lang']['last_seen']; ?>
</a></li>
			        <li style="text-align: right;"><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?do=online&sortby=online" rel="nofollow" class="<?php if ($this->_tpl_vars['gv_sortby'] == 'online'): ?>selected<?php endif; ?>"><?php echo $this->_tpl_vars['lang']['whois_online']; ?>
</a></li>
			    </ul>
			    </div>            
			</div>
            			
			<div class="pagination pagination-centered">
			<ul>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
" rel="nofollow"><?php echo $this->_tpl_vars['lang']['memberlist_all']; ?>
</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=a" rel="nofollow">A</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=b" rel="nofollow">B</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=c" rel="nofollow">C</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=d" rel="nofollow">D</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=e" rel="nofollow">E</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=f" rel="nofollow">F</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=g" rel="nofollow">G</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=h" rel="nofollow">H</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=i" rel="nofollow">I</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=j" rel="nofollow">J</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=k" rel="nofollow">K</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=l" rel="nofollow">L</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=m" rel="nofollow">M</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=n" rel="nofollow">N</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=o" rel="nofollow">O</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=p" rel="nofollow">P</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=q" rel="nofollow">Q</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=r" rel="nofollow">R</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=s" rel="nofollow">S</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=t" rel="nofollow">T</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=u" rel="nofollow">U</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=v" rel="nofollow">V</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=w" rel="nofollow">W</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=x" rel="nofollow">X</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=y" rel="nofollow">Y</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=z" rel="nofollow">Z</a></li>
			<li><a href="<?php echo @_URL; ?>
/memberlist.<?php echo @_FEXT; ?>
?letter=other" rel="nofollow">#</a></li>
			</ul>
			</div>
			
			
			<ul class="pm-ul-memberlist">
			<?php $_from = $this->_tpl_vars['user_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['user_data']):
?>
			  <li>
				<span class="pm-ml-username"><a href="<?php echo $this->_tpl_vars['user_data']['profile_url']; ?>
"><?php echo $this->_tpl_vars['user_data']['name']; ?>
</a><?php if ($this->_tpl_vars['user_data']['user_is_banned']): ?> <span class="label label-important"><?php echo $this->_tpl_vars['lang']['user_account_banned_label']; ?>
</span><?php endif; ?>
				<?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == '1' && $this->_tpl_vars['user_data']['id'] != $this->_tpl_vars['s_user_id']): ?>
				<?php if ($this->_tpl_vars['user_data']['is_following_me']): ?>
					<span class="label pm-follows"><?php echo $this->_tpl_vars['lang']['follow_following_you']; ?>
</span>
				<?php endif; ?>               
				<?php endif; ?>                
                </span>
				<span class="pm-ml-avatar"><a href="<?php echo $this->_tpl_vars['user_data']['profile_url']; ?>
"><img src="<?php echo $this->_tpl_vars['user_data']['avatar_url']; ?>
" alt="<?php echo $this->_tpl_vars['user_data']['username']; ?>
" width="60" height="60" border="0" class="img-polaroid"></a></span>
				<span class="pm-ml-country"><small><i class="icon-map-marker"></i> <?php echo $this->_tpl_vars['user_data']['country_label']; ?>
</small></span>
				<span class="pm-ml-lastseen"><small><i class="icon-eye-open"></i> <?php echo $this->_tpl_vars['user_data']['last_seen']; ?>
</small></span>
                
                <div class="pm-ml-buttons">
				<?php if (@_MOD_SOCIAL && $this->_tpl_vars['logged_in'] == '1' && $this->_tpl_vars['user_data']['id'] != $this->_tpl_vars['s_user_id']): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "user-follow-button.tpl", 'smarty_include_vars' => array('profile_data' => $this->_tpl_vars['user_data'],'profile_user_id' => $this->_tpl_vars['user_data']['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
                </div>
                <div class="clearfix"></div>
			  </li>
			<?php endforeach; else: ?>
				<?php if ($this->_tpl_vars['problem'] != ''): ?>
					<?php echo $this->_tpl_vars['problem']; ?>

				<?php else: ?>
					<?php echo $this->_tpl_vars['lang']['memberlist_msg2']; ?>
 
				<?php endif; ?>
			<?php endif; unset($_from); ?>
			</ul>
	
			<!-- pagination -->
			<div class="clearfix"></div>
			<?php if (is_array ( $this->_tpl_vars['pagination'] )): ?>
			<div class="pagination pagination-centered">
			  <ul>
			    <?php $_from = $this->_tpl_vars['pagination']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['pagination_data']):
?>
					<li<?php $_from = $this->_tpl_vars['pagination_data']['li']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['attr'] => $this->_tpl_vars['attr_val']):
?> <?php echo $this->_tpl_vars['attr']; ?>
="<?php echo $this->_tpl_vars['attr_val']; ?>
"<?php endforeach; endif; unset($_from); ?>>
						<a<?php $_from = $this->_tpl_vars['pagination_data']['a']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['attr'] => $this->_tpl_vars['attr_val']):
?> <?php echo $this->_tpl_vars['attr']; ?>
="<?php echo $this->_tpl_vars['attr_val']; ?>
"<?php endforeach; endif; unset($_from); ?>><?php echo $this->_tpl_vars['pagination_data']['text']; ?>
</a>
					</li>
				<?php endforeach; endif; unset($_from); ?>
			  </ul>
			</div>
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