{if ! $profile_data.am_following}
	<button id="btn_follow_{$profile_user_id}" class="btn btn-small btn-follow border-radius4">{$lang.follow}</button>
{else}
	<button id="btn_unfollow_{$profile_user_id}" class="btn btn-unfollow btn-small border-radius4"><i class="icon-ok icon-white"></i> {$lang.following}</button>
{/if}