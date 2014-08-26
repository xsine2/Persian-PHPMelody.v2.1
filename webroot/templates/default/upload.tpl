{include file="header.tpl" no_index="1" p="upload" tpl_name="upload"}
<div id="wrapper">
  <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12 extra-space">
            <nav id="second-nav" class="tabbable" role="navigation">
                <ul class="nav nav-tabs pull-right">
                <li><a href="{$smarty.const._URL}/edit_profile.{$smarty.const._FEXT}">{$lang.edit_profile}</a></li>
                <li><a href="{$smarty.const._URL}/upload_avatar.{$smarty.const._FEXT}">{$lang.update_avatar}</a></li>
                <li><a href="{$smarty.const._URL}/favorites.{$smarty.const._FEXT}?a=show">{$lang.my_favorites}</a></li>
                {if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
                <li><a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a></li>
                {/if}
                {if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
                <li class="active"><a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload_video}</a></li>
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

		<h1>{$lang.upload_video}</h1>
 		<hr />
		{if $success == 1}
			<div class="alert alert-success">
			{$lang.suggest_msg4}
			<br />
			<a href="upload.{$smarty.const._FEXT}">{$lang.add_another_one}</a> یا <a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
			</div>
		{elseif $success == 2}
			<div class="alert alert-success">
			{$lang.upload_errmsg11} 
			<a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
			</div>
		{elseif $success == 'custom'}
			<div class="alert alert-success">
			{$success_custom_message} 
			<a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
			</div>
		{else}
			{if count($errors) > 0}
		        <div class="alert alert-warning">
		        <button type="button" class="close" data-dismiss="alert">&times;</button>
		        <ul class="subtle-list">
		            {foreach from=$errors item=v}
		            	<li>{$v}</li>                        
		            {/foreach}
		        </ul>
		        </div>
			{/if}
			<form class="form-horizontal" name="upload-video-form" id="upload-video-form" enctype="multipart/form-data" method="post" action="{$form_action}">
			<div class="alert hide" id="error-placeholder"></div>
			<fieldset>
			    <div class="control-group">
			      <label class="control-label" for="mediafile">{$lang.upload_video1}</label>
			      <div class="controls">
					<span class="btn-upload border-radius4" rel="tooltip" title="*.wmv,*.mov,*.qt,*.3gp,*.3gpp,*.3g2,*.3gp2,*.mpg,*.mpeg,*.mp1,
                        *.mp2,*.m1v,*.m1a,*.m2a,*.mpa,*.mpv,*.mpv2,*.mpe,*.mp4,*.m4a,
						*.m4p,*.m4b,*.m4r,*.m4v,*.avi,*.flv,*.f4v,*.f4p,*.f4a,*.f4b,
						*.vob,*.lsf,*.lsx,*.asf,*.asr,*.asx,*.webm,*.mkv<br /> حداکثر حجم فایل: {$upload_limit}"><span id="uploadButtonPlaceholder"></span></span>
					<div>
					<small><div id="uploadProgressBar"></div></small>
					<div id="divStatus"></div>
					<ol id="uploadLog"></ol>
					</div>
					<input type="hidden" name="form_id" value="{$form_id}" />
					<input type="hidden" name="_pmnonce_t" value="{$form_csrfguard_token}" />
					<input type="hidden" name="temp_id" id="temp_id" value="" />


			      </div>
			    </div>
			    <div class="control-group">
			      <label class="control-label" for="capture">{$lang.upload_video2}</label>
			      <div class="controls">
						<input type="file" name="capture" value="" size="40">
						<input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}">
						<span class="help-inline"><a href="#" rel="tooltip" title="*.jpg, *.jpeg, *.gif, *.png"><i class="icon-info-sign"></i></a></span>
			      </div>
			    </div>
				<div  id="upload-video-extra">
					<div class="control-group">
				      <label class="control-label" for="video_title">{$lang.video}</label>
				      <div class="controls">
				      <input name="video_title" type="text" value="{$smarty.post.video_title}" class="input-large">
				      </div>
				    </div>
					<div class="control-group">
				      <label class="control-label" for="duration">{$lang._duration}</label>
				      <div class="controls">
				      <input name="duration" id="duration" type="text" value="{$smarty.post.duration}" class="input-mini" style="text-align: center;">
                      <span class="help-inline"><a href="#" rel="tooltip" title="{$lang.duration_format}"><i class="icon-info-sign"></i></a></span>
				      </div>
				    </div>
				    <div class="control-group">
				      <label class="control-label" for="category">{$lang.category}</label>
				      <div class="controls">
						{$categories_dropdown}
				      </div>
				    </div>
				    <div class="control-group">
				      <label class="control-label" for="description">{$lang.description}</label>
				      <div class="controls">
						<textarea name="description" style="margin: 0px;height: 150px;width: 620px;max-width: 620px;max-height: 300px;" class="span5" rows="3">{if $smarty.post.description}{$smarty.post.description}{/if}</textarea>
				      </div>
				    </div>
				    <div class="control-group">
				      <label class="control-label" for="tags">{$lang.tags}</label>
				      <div class="controls">
						<div class="tagsinput">
				          <input id="tags_upload" name="tags" type="text" class="tags" value="{$smarty.post.tags}"> <span class="help-inline"><a href="#" rel="tooltip" title="{$lang.suggest_ex}"><i class="icon-info-sign"></i></a></span>
				        </div>
				      </div>
				    </div>
				    {if isset($mm_upload_fields_inject)}{$mm_upload_fields_inject}{/if}
				    <div class="">
				      <div class="controls">
						<button name="Submit" type="submit" id="upload_btn" value="{$lang.submit_upload}" class="btn btn-success" data-loading-text="{$lang.submit_send}">{$lang.submit_upload}</button>
						<span id="uploading_gif">
						</span>
				      </div>
				    </div>
				</div><!-- #upload-video-extra -->
				
			</fieldset>
			</form>
		{/if}
        </div><!-- #primary -->
        </div><!-- .span12 -->
    </div><!-- .row-fluid --> 
  </div>
  </div>
  <!-- .container-fluid -->
{include file="footer.tpl" tpl_name="upload"}