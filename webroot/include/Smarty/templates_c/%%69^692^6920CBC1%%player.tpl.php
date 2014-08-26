<?php /* Smarty version 2.6.20, created on 2014-07-02 04:36:43
         compiled from player.tpl */ ?>
<?php if ($this->_tpl_vars['video_data']['restricted'] == '1' && ! $this->_tpl_vars['logged_in']): ?>
<div class="restricted-video border-radius4">
    <h2><?php echo $this->_tpl_vars['lang']['restricted_sorry']; ?>
</h2>
	<p><?php echo $this->_tpl_vars['lang']['restricted_register']; ?>
</p>
	<div class="restricted-login">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'user-auth-login-form.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>
</div>
<?php else: ?>
<?php if ($this->_tpl_vars['page'] == 'detail'): ?>
		<?php echo '
        <video width="659" poster="'; ?>
<?php echo @_URL2; ?>
/uploads/thumbs/<?php echo $this->_tpl_vars['video_data']['yt_thumb']; ?>
<?php echo '" id="player2" controls>
           <source src="'; ?>
<?php echo @_URL2; ?>
/uploads/videos/<?php echo $this->_tpl_vars['video_data']['url_flv']; ?>
<?php echo '" />
        </video>
		'; ?>

<?php endif; ?>


<?php if ($this->_tpl_vars['page'] == 'index'): ?>
		<?php echo '
        <video width="659" poster="'; ?>
<?php echo @_URL2; ?>
/uploads/thumbs/<?php echo $this->_tpl_vars['video_data']['yt_thumb']; ?>
<?php echo '" id="player2" controls>
           <source src="'; ?>
<?php echo @_URL2; ?>
/uploads/videos/<?php echo $this->_tpl_vars['video_data']['url_flv']; ?>
<?php echo '" />
        </video>   
		'; ?>

<?php endif; ?>


<?php if ($this->_tpl_vars['page'] == 'favorites'): ?>

		<?php echo '
        <video width="659" poster="'; ?>
<?php echo @_URL2; ?>
/uploads/thumbs/<?php echo $this->_tpl_vars['video_data']['yt_thumb']; ?>
<?php echo '" id="player2" controls>
           <source src="'; ?>
<?php echo @_URL2; ?>
/uploads/videos/<?php echo $this->_tpl_vars['video_data']['url_flv']; ?>
<?php echo '" />
        </video>
		'; ?>


<?php endif; ?>
<?php endif; ?>