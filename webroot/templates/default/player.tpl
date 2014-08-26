{if $video_data.restricted == '1' && ! $logged_in}
<div class="restricted-video border-radius4">
    <h2>{$lang.restricted_sorry}</h2>
	<p>{$lang.restricted_register}</p>
	<div class="restricted-login">
	{include file='user-auth-login-form.tpl'}
    </div>
</div>
{else}
{if $page == "detail"}
		{literal}
        <video width="659" poster="{/literal}{$smarty.const._URL2}/uploads/thumbs/{$video_data.yt_thumb}{literal}" id="player2" controls>
           <source src="{/literal}{$smarty.const._URL2}/uploads/videos/{$video_data.url_flv}{literal}" />
        </video>
		{/literal}
{/if}


{if $page == "index"}
		{literal}
        <video width="659" poster="{/literal}{$smarty.const._URL2}/uploads/thumbs/{$video_data.yt_thumb}{literal}" id="player2" controls>
           <source src="{/literal}{$smarty.const._URL2}/uploads/videos/{$video_data.url_flv}{literal}" />
        </video>   
		{/literal}
{/if}


{if $page == "favorites"}

		{literal}
        <video width="659" poster="{/literal}{$smarty.const._URL2}/uploads/thumbs/{$video_data.yt_thumb}{literal}" id="player2" controls>
           <source src="{/literal}{$smarty.const._URL2}/uploads/videos/{$video_data.url_flv}{literal}" />
        </video>
		{/literal}

{/if}
{/if}