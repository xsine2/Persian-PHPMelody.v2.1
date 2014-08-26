{include file='header.tpl' no_index='1' p="general"} 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
            <nav id="second-nav" class="tabbable" role="navigation">
                <ul class="nav nav-tabs pull-right">
                <li><a href="{$smarty.const._URL}/edit_profile.{$smarty.const._FEXT}">{$lang.edit_profile}</a></li>
                <li class="active"><a href="{$smarty.const._URL}/upload_avatar.{$smarty.const._FEXT}">{$lang.update_avatar}</a></li>
                <li><a href="{$smarty.const._URL}/favorites.{$smarty.const._FEXT}?a=show">{$lang.my_favorites}</a></li>
                {if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
                <li><a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a></li>
                {/if}
                {if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
                <li><a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload_video}</a></li>
                {/if}
                <li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}">{$lang.members_list}</a></li>
				{if isset($mm_profilemenu_insert)}{$mm_profilemenu_insert}{/if}
                </ul>
            </nav><!-- #site-navigation -->
        </div>
      </div>
      
      <div class="row-fluid">
       <div class="span12 extra-space">
		<div id="primary">
        
        <h1>{$lang.update_avatar}</h1>
        <hr />
        {if !empty($err_msg)}
            <div class="alert alert-warning">
            {$err_msg}
            </div>
        {/if}
        {if !empty($success_msg)}
            <div class="alert alert-success">
            {$success_msg}
            <a href="{$smarty.const._URL}/profile.{$smarty.const._FEXT}?u={$s_username}" rel="nofollow">{$lang.return_to_profile}</a>
            </div>
        {/if}
		{if empty($success_msg)}
        <form class="form-horizontal" name="update-avatar-form" method="post" action="{$smarty.const._URL}/upload_avatar.php" enctype="multipart/form-data">
          <fieldset>
            <div class="row-fluid">
              <div class="span2">
              <img src="{$avatar}" border="0" alt="" class="img-polaroid">
              </div>
              <div class="span10">
                <div class="control-group">
                  <label class="control-label" for="input01">{$lang.ua_msg2}</label>
                  <div class="controls">
                  <input name="imagefile" type="file" class="span7" size="20"> 
                  </div>
                </div>
                <div class="">
                    <div class="controls">
                    <button name="submit" type="submit" value="{$lang.submit_upload}" class="btn btn-success" data-loading-text="{$lang.submit_upload}">{$lang.submit_upload}</button>
                    </div>
                </div>
              </div>
            </div>
          </fieldset>
        </form>
		{/if}

		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
        
{include file='footer.tpl'} 