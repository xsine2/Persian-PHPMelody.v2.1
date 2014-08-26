<a id="back-top" class="hidden-phone hidden-tablet" title="{$lang.top}" style="margin-right: 0px;">
    <i class="icon-chevron-up" style="margin-right: 12px;"></i>
    <span></span>
</a>
<div style="display:none;">
<div class="floating_ad_left sticky_ads">
{$ad_6}
</div>
<div class="floating_ad_right sticky_ads">
{$ad_7}
</div>
</div>
<div style="clear: both;"></div>
</div><!-- end wrapper -->

<div class="row-fluid fixed960" style="width: 150px;float: left;">
    <div class="row-fluid">
    {if $tpl_name == "video-category"}
    <a href="{$smarty.const._URL}/rss.php?c={$cat_id}" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    {elseif $tpl_name == "video-new"}
    <a href="{$smarty.const._URL}/rss.php" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    {elseif $tpl_name == "video-top"}
    <a href="{$smarty.const._URL}/rss.php?feed=topvideos" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    {elseif $tpl_name == "article-category" || $tpl_name == "article-read"}
    <a href="{$smarty.const._URL}/rss.php?c={$cat_id}&feed=articles" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    {else}
    <a href="{$smarty.const._URL}/rss.php" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    {/if}
    </div>
</div>

{if $ad_2 != ''}
<div class="pm-ad-zone" align="center">{$ad_2}</div>
{/if}
    
<footer>
<div class="row-fluid fixed960">
	<div class="span8">
    <ul>
    	{if $smarty.const.MOBILE_MELODY && $smarty.const.USER_DEVICE == 'mobile'}
			<li><a href="{$_footer_switch_ui_link}" rel="nofollow">{$lang.switch_to_mobile_ui}</a></li>
    	{/if}
		<li><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}">{$lang.homepage}</a></li> &nbsp; &bull; &nbsp;
        <li><a href="{$smarty.const._URL}/contact_us.{$smarty.const._FEXT}">{$lang.contact_us}</a></li> &nbsp; &bull; &nbsp;
        {if $logged_in != '1' && $allow_registration == '1'}<li><a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}">{$lang.register}</a></li>{/if}
        {if $logged_in == '1' && $s_power == '1'}<li><a href="{$smarty.const._URL}/admin/">{$lang.admin_area}</a></li>{/if}
        {if is_array($links_to_pages)}
          {foreach from=$links_to_pages key=k item=page_data}
            <li><a href="{$page_data.page_url}">{$page_data.title}</a></li>
          {/foreach}
        {/if}
    </ul>
    
     <p style="font:16px 'byekan';">
    {if $smarty.const._POWEREDBY == 1}{$lang.powered_by}<br />{/if}
    {$lang.rights_reserved}
     </p>

    </div>
</div>
</footer>
<div id="lights-overlay"></div>

{literal}
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<script src="http://jwpsrv.com/library/xcO5aEqjEeKrIyIACp8kUw.js"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/mep-feature-loop.js"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/mep-feature-sourcechooser.js"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/main.videoplayer.js"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.cookee.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.validate.min.js" type="text/javascript"></script>
{/literal}
{if $p == "index"}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.carouFredSel.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.touchwipe.min.js" type="text/javascript"></script>
{/literal}
{/if}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery-scrolltofixed-min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.uniform.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.ba-dotimeout.min.js" type="text/javascript"></script>
{/literal}{if $tpl_name == "upload"}{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}/main_js/swfupload.js{literal}" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}/main_js/swfupload.queue.js{literal}" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}/main_js/jquery.swfupload.js{literal}" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}/main_js/upload.js{literal}" type="text/javascript"></script>
{/literal}{/if}
{if $smarty.const._SEARCHSUGGEST == 1}{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}/main_js/jquery.typewatch.js{literal}" type="text/javascript"></script>
{/literal}{/if}{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/main_js/melody.dev.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/melody.dev.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/lightbox.min.js" type="text/javascript"></script>
{/literal}

{if $smarty.const._SEARCHSUGGEST == 1}
{literal}
<script type="text/javascript">
$(document).ready(function () {
		// live search 
		$('#appendedInputButton').typeWatch({
			callback: function() {
					var a = $('#appendedInputButton').val();
					
					$.ajax({
						type: "POST",
			            url: MELODYURL2 + "/ajax_search.php",
			            data: {
							"queryString": a
			            },
			            dataType: "html",
			            success: function(b){
							if (b.length > 0) {
			                    $("#suggestions").show();
			                } else {
								$("#suggestions").hide();
							}
							$("#autoSuggestionsList").html(b);		
						}
					});
				},
		    	wait: 400,
		    	highlight: true,
		    	captureLength: 3
		});
});
</script>
{/literal}
{/if}

{if $p == "detail"}
{literal}
<script type="text/javascript">
$(document).ready(function () {
		var pm_elastic_player = $.cookie('pm_elastic_player');
		if (pm_elastic_player == null) {
			$.cookie('pm_elastic_player', 'normal');
		}
		else if (pm_elastic_player == 'wide') {
			$('#player_extend').find('i').addClass('icon-resize-small');
			$('#secondary').addClass('secondary-wide');
			$('#video-wrapper').addClass('video-wrapper-wide');
			$('.pm-video-head').addClass('pm-video-head-wide');
		} else {
			$('#secondary').removeClass('secondary-wide');
			$('#video-wrapper').removeClass('video-wrapper-wide');
			$('.pm-video-head-wide').removeClass('pm-video-head-wide');
		}

	$("#player_extend").click(function() {	
		if ($(this).find('i').hasClass("icon-resize-full")) {
			$(this).find('i').removeClass("icon-resize-full").addClass("icon-resize-small");
		} else {
			$(this).find('i').removeClass("icon-resize-small").addClass("icon-resize-full");
		}
		$('#secondary').animate({
			}, 10, function() {
				$('#secondary').toggleClass("secondary-wide");
		});
		$('#video-wrapper').animate({
			}, 150, function() {
				$('#video-wrapper').toggleClass("video-wrapper-wide");
				$('.pm-video-head').toggleClass('pm-video-head-wide');
		});
		if ($.cookie('pm_elastic_player') == 'normal') {
			$.cookie('pm_elastic_player','wide');
			$('#player_extend').find('i').removeClass('icon-resize-full').addClass('icon-resize-small');
		} else {
			$.cookie('pm_elastic_player', 'normal');
			$('#player_extend').find('i').removeClass('icon-resize-small').addClass('icon-resize-full');
		}
	return false;
  });
});
</script>
{/literal}
{/if}
{if $p == "index"}
{literal}
<script type="text/javascript">
$(document).ready(function() {
	$("#pm-ul-wn-videos").carouFredSel({
		items				: 4,
		circular			: false,
		direction			: "left",
		height				: null,
		width       		: null,
		infinite			: false,
		responsive			: true,
		prev	: {	
			button	: "#pm-slide-prev",
			key		: "left"
		},
		next	: { 
			button	: "#pm-slide-next",
			key		: "right"
		},
	scroll		: {
		items			: null,		//	items.visible
		fx				: "scroll",
		easing			: "swing",
		duration		: 500,
		wipe			: true,
		event			: "click",
	},
	auto: false
				
	});	
});

$(document).ready(function() {
	$("#pm-ul-top-videos").carouFredSel({
	items: 5,
	direction: "up",
	width: "variable",
	height:  "variable",
	circular: false,
	infinite: false,
	scroll: {
		fx: "fade",
		event: "click",
		wipe: true,
		duration: 150
	},
	auto: false,
		prev	: {	
			button	: "#pm-slide-top-prev",
			key		: "left"
		},
		next	: { 
			button	: "#pm-slide-top-next",
			key		: "right"
		}
	});	
});
</script>
{/literal}
{/if}
{if ! $logged_in}
    {literal}
    <script type="text/javascript">
    
        $('#header-login-form').on('shown', function () {
            $('.hocusfocus').focus();
        });
    
    </script>
    {/literal}
{/if}
{if $smarty.const._MOD_SOCIAL == '1'}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/waypoints.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/melody.social.min.js" type="text/javascript"></script> 
{/literal}
{/if}

{if $display_preroll_ad == true}
{literal}
<script src="{$smarty.const._URL}/templates/{$template_dir}/main_js/jquery.timer.min.js" type="text/javascript"></script>
<script type="text/javascript">

function timer_pad(number, length) {
	var str = '' + number;
	while (str.length < length) {str = '0' + str;}
	return str;
}

var preroll_timer;
var preroll_player_called = false;
var skippable = {/literal}{if $preroll_ad_data.skip != 1}0{else}1{/if}{literal}; 
var skippable_timer_current = {/literal}{if $preroll_ad_data.skip_delay_seconds}{$preroll_ad_data.skip_delay_seconds}{else}0{/if}{literal} * 1000;
var preroll_disable_stats = {/literal}{if $preroll_ad_data.disable_stats == 1}1{else}0{/if}{literal};
	
$(document).ready(function(){
	if (skippable == 1) {
		$('#preroll_skip_btn').hide();
	}
	
	var preroll_timer_current = {/literal}{$preroll_ad_data.duration}{literal} * 1000;
	
	preroll_timer = $.timer(function(){
	
		var seconds = parseInt(preroll_timer_current / 1000);
		var hours = parseInt(seconds / 3600);
		var minutes = parseInt((seconds / 60) % 60);
		var seconds = parseInt(seconds % 60);
		
		var output = "00";
		if (hours > 0) {
			output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
		} else if (minutes > 0) { 
			output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
		} else {
			output = timer_pad(seconds, 1);
		}
		
		$('.preroll_timeleft').html(output);
		
		if (preroll_timer_current == 0 && preroll_player_called == false) {

			$.ajax({
		        type: "GET",
		        url: MELODYURL2 + "/ajax.php",
				dataType: "html",
		        data: {
					"p": "video",
					"do": "getplayer",
					"vid": "{/literal}{$preroll_ad_player_uniq_id}{literal}",
					"aid": "{/literal}{$preroll_ad_data.id}{literal}",
					"player": "{/literal}{$preroll_ad_player_page}{literal}"
		        },
		        dataType: "html",
		        success: function(data){
					$('#preroll_placeholder').replaceWith(data);
		        }
			});
			
			preroll_player_called = true;
			preroll_timer.stop();
		} else {
			preroll_timer_current -= 1000;
			if(preroll_timer_current < 0) {
				preroll_timer_current = 0;
			}
		}
	}, 1000, true);
	
	if (skippable == 1) {
		
		skippable_timer = $.timer(function(){
	
			var seconds = parseInt(skippable_timer_current / 1000);
			var hours = parseInt(seconds / 3600);
			var minutes = parseInt((seconds / 60) % 60);
			var seconds = parseInt(seconds % 60);
			
			var output = "00";
			if (hours > 0) {
				output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
			} else if (minutes > 0) { 
				output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
			} else {
				output = timer_pad(seconds, 1);
			}
			
			$('.preroll_skip_timeleft').html(output);
			
			if (skippable_timer_current == 0 && preroll_player_called == false) {
				$('#preroll_skip_btn').show();
				$('.preroll_skip_countdown').hide();
				skippable_timer.stop();
			} else {
				skippable_timer_current -= 1000;
				if(skippable_timer_current < 0) {
					skippable_timer_current = 0;
				}
			}
		}, 1000, true);
		
		$('#preroll_skip_btn').click(function(){
			preroll_timer_current = 0;
			skippable_timer_current = 0;

			if (preroll_disable_stats == 0) {
				$.ajax({
			        type: "GET",
			        url: MELODYURL2 + "/ajax.php",
					dataType: "html",
			        data: {
						"p": "stats",
						"do": "skip",
						"aid": "{/literal}{$preroll_ad_data.id}{literal}",
						"at": "{/literal}{$smarty.const._AD_TYPE_PREROLL}{literal}",
			        },
			        dataType: "html",
			        success: function(data){}
				});
			}
			return false;
		});
	}
});
</script>
{/literal}
{/if}
{$smarty.const._HTMLCOUNTER}
</body>
</html>