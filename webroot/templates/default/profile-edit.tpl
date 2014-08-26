{include file='header.tpl' p="general"} 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
            <nav id="second-nav" class="tabbable" role="navigation">
                <ul class="nav nav-tabs pull-right">
                <li class="active"><a href="{$smarty.const._URL}/edit_profile.{$smarty.const._FEXT}">{$lang.edit_profile}</a></li>
                <li><a href="{$smarty.const._URL}/upload_avatar.{$smarty.const._FEXT}">{$lang.update_avatar}</a></li>
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
        
		<h1>{$lang.update_profile}</h1>
        <hr />
		{if $success == 1}
		<div class="alert alert-success">{$lang.ep_msg1}</div>
            {if $changed_pass == 1}
            <div class="alert alert-success">{$lang.ep_msg2}</div>
            <meta http-equiv="refresh" content="5;URL={$smarty.const._URL}">
            {/if}
		{include file='profile-edit-form.tpl'}
        {else}
		 	{if $errors.failure != ''}
		 		{$errors.failure}
			{/if}
        
        {if $nr_errors > 0}
        <div class="alert alert-error">
            <ul class="subtle-list">
            {foreach from=$errors item=error}
                <li>{$error}</li>
            {/foreach}
            </ul>
        </div>
        {/if} 
        {include file='profile-edit-form.tpl'}
		{/if}

		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
        
{include file='footer.tpl'} 