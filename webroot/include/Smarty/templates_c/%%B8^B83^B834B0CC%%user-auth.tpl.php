<?php /* Smarty version 2.6.20, created on 2014-06-28 15:35:31
         compiled from user-auth.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'header.tpl', 'smarty_include_vars' => array('p' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
<div id="wrapper">
  <div class="container-fluid">
    <div class="row-fluid">
    <div class="span12 extra-space">
    <nav id="second-nav" class="tabbable" role="navigation">
      <ul class="nav nav-tabs pull-right">
        <li<?php if ($this->_tpl_vars['display_form'] == 'forgot_pass'): ?> class="active"<?php endif; ?>><a href="#pm-reset" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['forgot_pass']; ?>
</a></li>
        <?php if ($this->_tpl_vars['allow_registration'] == '1'): ?>
		<li<?php if ($this->_tpl_vars['display_form'] == 'register'): ?> class="active"<?php endif; ?>><a href="#pm-register" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['create_account']; ?>
</a></li>
		<?php endif; ?>
        <li<?php if ($this->_tpl_vars['display_form'] == 'login'): ?> class="active"<?php endif; ?>><a href="#pm-login" data-toggle="tab"><?php echo $this->_tpl_vars['lang']['login']; ?>
</a></li>
      </ul>
    </nav><!-- #site-navigation -->

    <div id="primary" class="extra-space">
        <div class="tab-content">
          	<div class="tab-pane<?php if ($this->_tpl_vars['display_form'] == 'register'): ?> active<?php endif; ?>" id="pm-register">
		    <?php if ($this->_tpl_vars['display_form'] == 'register'): ?>
				<?php if ($this->_tpl_vars['success']): ?>
	                <h2><?php echo $this->_tpl_vars['lang']['register_msg1']; ?>
</h2>
					<hr />
	                <div class="alert alert-info">
	                    <?php echo $this->_tpl_vars['lang']['register_msg2']; ?>
 <?php echo $this->_tpl_vars['inputs']['email']; ?>
. <br /><?php echo $this->_tpl_vars['msg']; ?>
<br />
	                </div>
	            <?php else: ?>
            		<p class="lean"></p>
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
	            	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-register-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	            <?php endif; ?>
			<?php else: ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-register-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
            </div>
            
            <div class="tab-pane<?php if ($this->_tpl_vars['display_form'] == 'login'): ?> active<?php endif; ?>" id="pm-login">
			<?php if ($this->_tpl_vars['display_form'] == 'login'): ?>
				<?php if ($this->_tpl_vars['success']): ?>
				<?php else: ?>
                <p class="lean"></p>
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
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-auth-login-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
			<?php else: ?>
            	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-auth-login-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?> 
            </div>
            
           
            <div class="tab-pane<?php if ($this->_tpl_vars['display_form'] == 'forgot_pass'): ?> active<?php endif; ?>" id="pm-reset">
			<?php if ($this->_tpl_vars['display_form'] == 'forgot_pass'): ?>
				<?php if ($this->_tpl_vars['success']): ?>
					<div class="alert alert-info">
						<?php echo $this->_tpl_vars['lang']['fp_msg']; ?>

					</div>
				<?php else: ?>
                <p class="lean"></p>
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
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-auth-forgot-pass-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	 			<?php endif; ?>
			<?php else: ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-auth-forgot-pass-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
			</div>


            <div class="tab-pane<?php if ($this->_tpl_vars['display_form'] == 'activate_acc'): ?> active<?php endif; ?>" id="pm-reset">
            <h1><?php echo $this->_tpl_vars['lang']['activate_account']; ?>
</h1>
            <hr />
			<?php if ($this->_tpl_vars['display_form'] == 'activate_acc'): ?>
				<?php if ($this->_tpl_vars['success']): ?>
					<div class="alert alert-success">
						<?php echo $this->_tpl_vars['lang']['activate_account_msg1']; ?>

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
	 			<?php endif; ?>
			<?php endif; ?>
			</div>
            
            <div class="tab-pane<?php if ($this->_tpl_vars['display_form'] == 'pwdreset'): ?> active<?php endif; ?>" id="pm-reset">
            <h1><?php echo $this->_tpl_vars['lang']['activate_pass']; ?>
</h1>
            <hr />
			<?php if ($this->_tpl_vars['display_form'] == 'pwdreset'): ?>
				<?php if ($this->_tpl_vars['success']): ?>
					<div class="alert alert-success">
						<?php echo $this->_tpl_vars['lang']['activate_pass_msg1']; ?>

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
	 			<?php endif; ?>
			<?php endif; ?>
			</div>
            
		</div><!-- .tab-content -->
	</div><!-- #primary -->
    </div><!-- #content --> 
    </div><!-- .row-fluid --> 
  </div><!-- .container-fluid -->

<div class="modal hide" id="terms">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3><?php echo $this->_tpl_vars['lang']['toa']; ?>
</h3>
  </div>
  <div class="modal-body">
	<?php if ($this->_tpl_vars['terms_page']['content'] != ''): ?>
		<h1><?php echo $this->_tpl_vars['terms_page']['title']; ?>
</h1>
		<hr />
		<?php echo $this->_tpl_vars['terms_page']['content']; ?>

	<?php else: ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'terms.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?> 
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-success" data-dismiss="modal"><?php echo $this->_tpl_vars['lang']['close']; ?>
</a>
  </div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'footer.tpl', 'smarty_include_vars' => array('p' => 'auth')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 