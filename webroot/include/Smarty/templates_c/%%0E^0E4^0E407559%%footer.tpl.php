<?php /* Smarty version 2.6.20, created on 2014-07-02 04:35:58
         compiled from footer.tpl */ ?>
<a id="back-top" class="hidden-phone hidden-tablet" title="<?php echo $this->_tpl_vars['lang']['top']; ?>
" style="margin-right: 0px;">
    <i class="icon-chevron-up" style="margin-right: 12px;"></i>
    <span></span>
</a>
<div style="display:none;">
<div class="floating_ad_left sticky_ads">
<?php echo $this->_tpl_vars['ad_6']; ?>

</div>
<div class="floating_ad_right sticky_ads">
<?php echo $this->_tpl_vars['ad_7']; ?>

</div>
</div>
<div style="clear: both;"></div>
</div><!-- end wrapper -->

<div class="row-fluid fixed960" style="width: 150px;float: left;">
    <div class="row-fluid">
    <?php if ($this->_tpl_vars['tpl_name'] == "video-category"): ?>
    <a href="<?php echo @_URL; ?>
/rss.php?c=<?php echo $this->_tpl_vars['cat_id']; ?>
" title="<?php echo $this->_tpl_vars['meta_title']; ?>
 RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    <?php elseif ($this->_tpl_vars['tpl_name'] == "video-new"): ?>
    <a href="<?php echo @_URL; ?>
/rss.php" title="<?php echo $this->_tpl_vars['meta_title']; ?>
 RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    <?php elseif ($this->_tpl_vars['tpl_name'] == "video-top"): ?>
    <a href="<?php echo @_URL; ?>
/rss.php?feed=topvideos" title="<?php echo $this->_tpl_vars['meta_title']; ?>
 RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    <?php elseif ($this->_tpl_vars['tpl_name'] == "article-category" || $this->_tpl_vars['tpl_name'] == "article-read"): ?>
    <a href="<?php echo @_URL; ?>
/rss.php?c=<?php echo $this->_tpl_vars['cat_id']; ?>
&feed=articles" title="<?php echo $this->_tpl_vars['meta_title']; ?>
 RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    <?php else: ?>
    <a href="<?php echo @_URL; ?>
/rss.php" title="<?php echo $this->_tpl_vars['meta_title']; ?>
 RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> خروجی فید</a>
    <?php endif; ?>
    </div>
</div>

<?php if ($this->_tpl_vars['ad_2'] != ''): ?>
<div class="pm-ad-zone" align="center"><?php echo $this->_tpl_vars['ad_2']; ?>
</div>
<?php endif; ?>
    
<footer>
<div class="row-fluid fixed960">
	<div class="span8">
    <ul>
    	<?php if (@MOBILE_MELODY && @USER_DEVICE == 'mobile'): ?>
			<li><a href="<?php echo $this->_tpl_vars['_footer_switch_ui_link']; ?>
" rel="nofollow"><?php echo $this->_tpl_vars['lang']['switch_to_mobile_ui']; ?>
</a></li>
    	<?php endif; ?>
		<li><a href="<?php echo @_URL; ?>
/index.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['homepage']; ?>
</a></li> &nbsp; &bull; &nbsp;
        <li><a href="<?php echo @_URL; ?>
/contact_us.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['contact_us']; ?>
</a></li> &nbsp; &bull; &nbsp;
        <?php if ($this->_tpl_vars['logged_in'] != '1' && $this->_tpl_vars['allow_registration'] == '1'): ?><li><a href="<?php echo @_URL; ?>
/register.<?php echo @_FEXT; ?>
"><?php echo $this->_tpl_vars['lang']['register']; ?>
</a></li><?php endif; ?>
        <?php if ($this->_tpl_vars['logged_in'] == '1' && $this->_tpl_vars['s_power'] == '1'): ?><li><a href="<?php echo @_URL; ?>
/admin/"><?php echo $this->_tpl_vars['lang']['admin_area']; ?>
</a></li><?php endif; ?>
        <?php if (is_array ( $this->_tpl_vars['links_to_pages'] )): ?>
          <?php $_from = $this->_tpl_vars['links_to_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['page_data']):
?>
            <li><a href="<?php echo $this->_tpl_vars['page_data']['page_url']; ?>
"><?php echo $this->_tpl_vars['page_data']['title']; ?>
</a></li>
          <?php endforeach; endif; unset($_from); ?>
        <?php endif; ?>
    </ul>
    
     <p style="font:16px 'byekan';">
    <?php if (@_POWEREDBY == 1): ?><?php echo $this->_tpl_vars['lang']['powered_by']; ?>
<br /><?php endif; ?>
    <?php echo $this->_tpl_vars['lang']['rights_reserved']; ?>

     </p>

    </div>
</div>
</footer>
<div id="lights-overlay"></div>

<?php echo '
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<script src="http://jwpsrv.com/library/xcO5aEqjEeKrIyIACp8kUw.js"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/mep-feature-loop.js"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/mep-feature-sourcechooser.js"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/main.videoplayer.js"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/bootstrap.min.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.cookee.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.validate.min.js" type="text/javascript"></script>
'; ?>

<?php if ($this->_tpl_vars['p'] == 'index'): ?>
<?php echo '
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.carouFredSel.min.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.touchwipe.min.js" type="text/javascript"></script>
'; ?>

<?php endif; ?>
<?php echo '
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery-scrolltofixed-min.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.uniform.min.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/jquery.ba-dotimeout.min.js" type="text/javascript"></script>
'; ?>
<?php if ($this->_tpl_vars['tpl_name'] == 'upload'): ?><?php echo '
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/main_js/swfupload.js<?php echo '" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/main_js/swfupload.queue.js<?php echo '" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/main_js/jquery.swfupload.js<?php echo '" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/main_js/upload.js<?php echo '" type="text/javascript"></script>
'; ?>
<?php endif; ?>
<?php if (@_SEARCHSUGGEST == 1): ?><?php echo '
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
/main_js/jquery.typewatch.js<?php echo '" type="text/javascript"></script>
'; ?>
<?php endif; ?><?php echo '
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/main_js/melody.dev.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/melody.dev.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/lightbox.min.js" type="text/javascript"></script>
'; ?>


<?php if (@_SEARCHSUGGEST == 1): ?>
<?php echo '
<script type="text/javascript">
$(document).ready(function () {
		// live search 
		$(\'#appendedInputButton\').typeWatch({
			callback: function() {
					var a = $(\'#appendedInputButton\').val();
					
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
'; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['p'] == 'detail'): ?>
<?php echo '
<script type="text/javascript">
$(document).ready(function () {
		var pm_elastic_player = $.cookie(\'pm_elastic_player\');
		if (pm_elastic_player == null) {
			$.cookie(\'pm_elastic_player\', \'normal\');
		}
		else if (pm_elastic_player == \'wide\') {
			$(\'#player_extend\').find(\'i\').addClass(\'icon-resize-small\');
			$(\'#secondary\').addClass(\'secondary-wide\');
			$(\'#video-wrapper\').addClass(\'video-wrapper-wide\');
			$(\'.pm-video-head\').addClass(\'pm-video-head-wide\');
		} else {
			$(\'#secondary\').removeClass(\'secondary-wide\');
			$(\'#video-wrapper\').removeClass(\'video-wrapper-wide\');
			$(\'.pm-video-head-wide\').removeClass(\'pm-video-head-wide\');
		}

	$("#player_extend").click(function() {	
		if ($(this).find(\'i\').hasClass("icon-resize-full")) {
			$(this).find(\'i\').removeClass("icon-resize-full").addClass("icon-resize-small");
		} else {
			$(this).find(\'i\').removeClass("icon-resize-small").addClass("icon-resize-full");
		}
		$(\'#secondary\').animate({
			}, 10, function() {
				$(\'#secondary\').toggleClass("secondary-wide");
		});
		$(\'#video-wrapper\').animate({
			}, 150, function() {
				$(\'#video-wrapper\').toggleClass("video-wrapper-wide");
				$(\'.pm-video-head\').toggleClass(\'pm-video-head-wide\');
		});
		if ($.cookie(\'pm_elastic_player\') == \'normal\') {
			$.cookie(\'pm_elastic_player\',\'wide\');
			$(\'#player_extend\').find(\'i\').removeClass(\'icon-resize-full\').addClass(\'icon-resize-small\');
		} else {
			$.cookie(\'pm_elastic_player\', \'normal\');
			$(\'#player_extend\').find(\'i\').removeClass(\'icon-resize-small\').addClass(\'icon-resize-full\');
		}
	return false;
  });
});
</script>
'; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['p'] == 'index'): ?>
<?php echo '
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
'; ?>

<?php endif; ?>
<?php if (! $this->_tpl_vars['logged_in']): ?>
    <?php echo '
    <script type="text/javascript">
    
        $(\'#header-login-form\').on(\'shown\', function () {
            $(\'.hocusfocus\').focus();
        });
    
    </script>
    '; ?>

<?php endif; ?>
<?php if (@_MOD_SOCIAL == '1'): ?>
<?php echo '
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/waypoints.min.js" type="text/javascript"></script>
<script src="'; ?>
<?php echo @_URL; ?>
/templates/<?php echo $this->_tpl_vars['template_dir']; ?>
<?php echo '/js/melody.social.min.js" type="text/javascript"></script> 
'; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['display_preroll_ad'] == true): ?>
<?php echo '
<script src="{$smarty.const._URL}/templates/{$template_dir}/main_js/jquery.timer.min.js" type="text/javascript"></script>
<script type="text/javascript">

function timer_pad(number, length) {
	var str = \'\' + number;
	while (str.length < length) {str = \'0\' + str;}
	return str;
}

var preroll_timer;
var preroll_player_called = false;
var skippable = '; ?>
<?php if ($this->_tpl_vars['preroll_ad_data']['skip'] != 1): ?>0<?php else: ?>1<?php endif; ?><?php echo '; 
var skippable_timer_current = '; ?>
<?php if ($this->_tpl_vars['preroll_ad_data']['skip_delay_seconds']): ?><?php echo $this->_tpl_vars['preroll_ad_data']['skip_delay_seconds']; ?>
<?php else: ?>0<?php endif; ?><?php echo ' * 1000;
var preroll_disable_stats = '; ?>
<?php if ($this->_tpl_vars['preroll_ad_data']['disable_stats'] == 1): ?>1<?php else: ?>0<?php endif; ?><?php echo ';
	
$(document).ready(function(){
	if (skippable == 1) {
		$(\'#preroll_skip_btn\').hide();
	}
	
	var preroll_timer_current = '; ?>
<?php echo $this->_tpl_vars['preroll_ad_data']['duration']; ?>
<?php echo ' * 1000;
	
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
		
		$(\'.preroll_timeleft\').html(output);
		
		if (preroll_timer_current == 0 && preroll_player_called == false) {

			$.ajax({
		        type: "GET",
		        url: MELODYURL2 + "/ajax.php",
				dataType: "html",
		        data: {
					"p": "video",
					"do": "getplayer",
					"vid": "'; ?>
<?php echo $this->_tpl_vars['preroll_ad_player_uniq_id']; ?>
<?php echo '",
					"aid": "'; ?>
<?php echo $this->_tpl_vars['preroll_ad_data']['id']; ?>
<?php echo '",
					"player": "'; ?>
<?php echo $this->_tpl_vars['preroll_ad_player_page']; ?>
<?php echo '"
		        },
		        dataType: "html",
		        success: function(data){
					$(\'#preroll_placeholder\').replaceWith(data);
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
			
			$(\'.preroll_skip_timeleft\').html(output);
			
			if (skippable_timer_current == 0 && preroll_player_called == false) {
				$(\'#preroll_skip_btn\').show();
				$(\'.preroll_skip_countdown\').hide();
				skippable_timer.stop();
			} else {
				skippable_timer_current -= 1000;
				if(skippable_timer_current < 0) {
					skippable_timer_current = 0;
				}
			}
		}, 1000, true);
		
		$(\'#preroll_skip_btn\').click(function(){
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
						"aid": "'; ?>
<?php echo $this->_tpl_vars['preroll_ad_data']['id']; ?>
<?php echo '",
						"at": "'; ?>
<?php echo @_AD_TYPE_PREROLL; ?>
<?php echo '",
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
'; ?>

<?php endif; ?>
<?php echo @_HTMLCOUNTER; ?>

</body>
</html>