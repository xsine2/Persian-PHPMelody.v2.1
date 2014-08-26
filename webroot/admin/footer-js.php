<script type="text/javascript">

	// Loading ...
	document.getElementById("loading").style.display="block";
	
	function addLoadEvent(func) {
	  var oldonload = window.onload;
	  if (typeof window.onload != 'function') {
		window.onload = func;
	  } else {
		window.onload = function() {
		  if (oldonload) {
			oldonload();
		  }
		  func();
		}
	  }
	}
	
	addLoadEvent(function() {
	  document.getElementById("loading").style.display="none";
	});

	var adminPrimary = $('.content').height()+100;
	$('#adminSecondary').css({'min-height': ''+ adminPrimary +'px' });
	
	//===== Color picker =====//
<?php if($load_colorpicker == 1): ?>

	$("#bg_bar").colorpicker().on("changeColor", function(ev){
		hex = ev.color.toHex();
		$("#bg_bar").val(hex);
	});
	$("#play_timer").colorpicker().on("changeColor", function(ev){
		hex = ev.color.toHex();
		$("#play_timer").val(hex);
	});
<?php endif; ?>

<?php if($load_uniform == 1): ?>
	$("input:file").uniform();
<?php endif; ?>

	$('#test-email').click(function(event){
		event.preventDefault();
		$('#loader').show();
		$.ajax({
			url: 'admin-ajax.php',
			data: {
				p: 'settings',
				"do": 'testmail',
				mail_server	: $('input[name=mail_server]').val(),
				mail_port	: $('input[name=mail_port]').val(),
				mail_user	: $('input[name=mail_user]').val(),
				mail_pass	: $('input[name=mail_pass]').val(),
				mail_smtp	: $('input[name=issmtp]:checked').val(),
				contact_email: $('input[name=contact_mail]').val()
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#test-email-response').html(data['message']).show();
				$('#loader').hide();
			}
		});
		return false;
	});

	$(".editadzone").click(function(){ // Click to only happen on announce links
		$("#adzoneid").val($(this).data('id'));
	});

<?php if($load_scrolltofixed == 1): ?>
	$('#stack-controls').scrollToFixed({ 
		bottom: 0,
		limit: $('#stack-controls').offset().top, //.top
		preFixed: function() { $(this).css({'background-color' : '#FFF', 'box-shadow' : '0 -2px 4px #ddd', 'padding' : '5px 15px', 'border-radius' : '4px 4px 0 0', 'border' : '1px solid #ddd', 'border-bottom' : 'none', 'background-image' : '-moz-linear-gradient(top, #fff, #f4f4f4)' }); },
		postFixed: function() { $(this).css({'background-color': '', 'box-shadow' : 'none', 'padding' : '10px 0px', 'border' : 'none', 'background-image' : 'none'}); }
	});

	$('#import-nav').scrollToFixed({ 
        preFixed: function() { $(this).css({'background-color' : '#FFF', 'box-shadow' : '2px 0 6px #CCC', 'padding-right' : '10px', 'border-radius' : '0 0 4px 4px' }); $(this).find('h2').css('visibility', 'hidden'); },
        postFixed: function() { $(this).css({'background-color': '', 'box-shadow' : 'none', 'padding-right' : '0px'}); $(this).find('h2').css('visibility', 'visible'); }
	});
	/*$('#sideNav').scrollToFixed({ bottom: 0, limit: $('#wrapper').offset().top });*/

<?php endif; ?>
<?php if($load_tagsinput == 1): ?>
	var tidyTags = function(e){
		var tags = ($(e.target).val() + ',' + e.tags).split(',');
		var target = $(e.target);
		target.importTags('');
		for (var i = 0, z = tags.length; i<z; i++) {
			var tag = $.trim(tags[i]);
			if (!target.tagExist(tag)) {
				target.addTag(tag);
			}
		}
	}
	$('input[id^="tags_addvideo_"]').tagsInput({
		onAddTag : function(tag){
		if(tag.indexOf(',') > 0){
			tidyTags({target: 'input[id^="tags_addvideo_"]', tags : tag});
			 }
		 },
		'removeWithBackspace' : true,
		'height':'auto',
		'width':'auto',
		'defaultText':'',
		'minChars' : 3,
		'maxChars' : 90
	});
<?php endif; ?>

	$("img[name='video_thumbnail']").click(function() {
		
		var img = $(this);
		var row_id = $(this).attr('rowid');
		var ul = img.parents('.thumbs_ul_import');
		var li = img.parent();
		var tr = img.parents('div');	
		var input = $('#thumb_url_'+ row_id);
	
		if ( ! li.hasClass('stack-thumb-selected'))
		{
			ul.children().removeClass('stack-thumb-selected').addClass('stack-thumb');
			li.addClass('stack-thumb-selected');
			input.val(img.attr('src'));
		}
	});


<?php if($load_ibutton == 1): ?>
	$('.on_off :checkbox').iButton({
		duration: 80,
		labelOn: "",
		labelOff: "",
		enableDrag: false 
	});

    $("#checkall").click(function () {
		$('.on_off :checkbox').iButton("repaint");
		if($('.on_off :checkbox').is(":checked")) {
		  $('.video-stack').addClass("stack-selected");
		}else {
		  $('.video-stack').removeClass("stack-selected");
		}
    });
<?php endif; ?>

	$('.on_off :checkbox').change(function () {
		if ($(this).attr("checked")) {
			$(this).closest('.video-stack').addClass("stack-selected");
		} 
		else {
		$(this).closest('.video-stack').removeClass("stack-selected");
		}
	});


	$(document).ready(function () {
		 $("input[id^='featured'][type=checkbox]").change(function () { $('#value-featured').text('updated').addClass('label label-success'); });
		 $("input[id^='visibility'][type=radio]").change(function () { $('#value-visibility').text('updated').addClass('label label-success'); });
		 $("input[id^='restricted'][type=radio]").change(function () { $('#value-register').text('updated').addClass('label label-success'); });
		 $("input[class^='pubDate']").change(function () { $('#value-publish').text('updated').addClass('label label-success'); });
		 $("select[class^='pubDate']").change(function () { $('#value-publish').text('updated').addClass('label label-success'); });
		 $("input[id^='site_views_input']").change(function () { $('#value-views').text('updated').addClass('label label-success'); });
		 $("input[id^='submitted']").change(function () { $('#value-submitted').text('updated').addClass('label label-success'); });
		 $("input[id^='allow_comments']").change(function () { $('#value-comments').text('updated').addClass('label label-success'); });
		 $("input[id^='yt_length']").change(function () { $('#value-yt_length').text('updated').addClass('label label-success'); });
		 $("input[id^='show_in_menu'][type=radio]").change(function () { $('#value-showinmenu').text('updated').addClass('label label-success'); });	 
	
         $("input[id^='import-'][type=checkbox]").each(function(){
             $(this).change(updateCount);
         });
         
         $("#checkall").each(function(){
             $(this).change(updateCount);
         });
         updateCount();
         
         function updateCount(){
             var count = $("input[id^='import-'][type=checkbox]:checked").size();
             
             $("#count").text(count);
             $("#status").toggle(count > 0);
         };

		var cc = $.cookie('list_grid');
		if (cc == 'g') {
			$('#vs-grid').addClass('vs-grid');
		} else {
			$('#vs-grid').removeClass('vs-grid');
		}
/*
		var list_filter = $.cookie('list_filter');
		if (list_filter == null) {
			$('#showfilter-content').show();
		} else {
			$('#showfilter-content').hide();
		}	
*/
	});

	$('#stacks').click(function() {
		$('#vs-grid').fadeOut(200, function() {
			$(this).addClass('vs-grid').fadeIn(200);
			$.cookie('list_grid', 'g');
		});
		return false;
	});
	
	$('#list').click(function() {
		$('#vs-grid').fadeOut(200, function() {
			$(this).removeClass('vs-grid').fadeIn(200);
			$.cookie('list_grid', null);
		});
		return false;
	});


	$("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();

	$('#myModal').modal({
	  keyboard: true,
	  show: false
	});

	$('#searchVideos').click(function() {
		$(".searchLoader").css({"display" : "inline"});
	});

	$('#addvideo_direct_submit').click(function() {
		$(".addLoader").css({"display" : "inline"});
	});	

	$('#submitImport').click(function() {
		$('#loading').show();
		$(".video-stack").css({"opacity" : "0.5"});
		$(".importLoader").css({"display" : "inline"});
	});

	$('#submitFind').click(function() {
		$('#loading').show();
		$(".pm-tables").css({"opacity" : "0.5"});
		$(".findIcon").css({"display" : "none"});
		$(".findLoader").css({"display" : "inline"});
	});

	$('.pagination > ul > li > a').click(function() {
		$('#loading').show();
		$(".pm-tables td").css({"opacity" : "0.5"});
		$(".tableFooter").css({"opacity" : "1.0"});
		$("#vs-grid").css({"opacity" : "0.5"});
	});

	$('.btn-success').click(function() {
		$('#loading').show().delay(1000).fadeOut();
	});

	
<?php if($load_chzn_drop == 1): ?>
	$('.category_dropdown').addClass("chzn-select");
	$(".chzn-select").chosen();
	$(".chzn-select-deselect").chosen({allow_single_deselect:true});
<?php endif; ?>

	$('#adminSecondary').css({'height': (($('#wrapper').height()))+'px'});
	$(window).resize(function () {
		$('#adminSecondary').css({'height': (($('#wrapper').height()))+'px'});
	});

   /* $('.content').css({'height': (($('#adminSecondary').height()))+'px'});	*/


	$(document).ready(function() {	
		
		$('li.has-subcats').hover(function(){
			if ( ! $(this).hasClass('active')) {
				//$('ul', this).stop().doTimeout( 'hover', 500, 'slideDown', 250 );
				$('ul', this).stop().doTimeout( 'hover', 250, 'slideDown', 250 ); //@since v2.1 
			}
		}, function(){
			if ( ! $(this).hasClass('active')) {
				//$('ul', this).stop().doTimeout( 'hover', 0, 'slideUp', 300 );
				$('ul', this).stop().doTimeout( 'hover', 200, 'slideUp', 300 ); //@since v2.1
			}
		});
	});

	//$('#showfilter').click(function() { $('#showfilter-content').slideToggle(100, function() { $.cookie('list_filter', 'open'); }); }); 
	/*
	$('#showfilter').click(function() {
		$('#showfilter-content').slideToggle(100, function() {
			if ($.cookie('list_filter') == null) {
				$.cookie('list_filter','close');
			} else {
				$.cookie('list_filter', null);
			}
		});
		return false;
	});
	*/
	$('#import-options').click(function() { $('#import-opt-content').slideToggle('fast'); });
	//$('#import-options').click(function() { $('#import-opt-content').slideToggle('fast'); });

	$('#show-comments').click(function() { $('#show-opt-comments').slideToggle('fast'); return false; });
	$('#show-restriction').click(function() { $('#show-opt-restriction').slideToggle('fast'); return false; });
	$('#show-visibility').click(function() { $('#show-opt-visibility').slideToggle('fast'); return false; });
	$('#show-publish').click(function() { $('#show-opt-publish').slideToggle('fast'); return false; });
	$('#show-thumb').click(function() { $('#show-opt-thumb').slideToggle(50); return false; });
	$('#show-featured').click(function() { $('#show-opt-featured').slideToggle('fast'); return false; });
	$('#show-user').click(function() { $('#show-opt-user').slideToggle('fast'); return false; });
	$('#show-views').click(function() { $('#show-opt-views').slideToggle('fast'); return false; });
	$('#show-vs1').click(function() { $('#show-opt-vs1').slideToggle('fast'); return false; });
	$('#show-vs2').click(function() { $('#show-opt-vs2').slideToggle('fast'); return false; });
	$('#show-vs3').click(function() { $('#show-opt-vs3').slideToggle('fast'); return false; });
	$('#show-duration').click(function() { $('#show-opt-duration').slideToggle('fast'); return false; });
	$('#show-showinmenu').click(function() { $('#show-opt-showinmenu').slideToggle('fast'); return false; });
	
	$('#show-help-assist').click(function() { $('#help-assist').slideToggle('fast'); $('#show-help-assist').toggleClass('opac5'); return false; });
	$('#show-help-link-assist').click(function() { $('#help-assist').slideToggle('fast'); $('#show-help-link-assist').toggleClass('opac5'); return false; });
	
	$('#show-playlists').click(function() { $('#playlists').slideToggle('normal'); $('#content-to-hide').fadeToggle(300); });

<?php if($load_prettypop == 1): ?>
	$("a[rel^='prettyPop']").prettyPhoto({
		animationSpeed: 'fast', /* fast/slow/normal */
		padding: 40, /* padding for each side of the picture */
		opacity: 0.70, /* Value betwee 0 and 1 */
		showTitle: false, /* true/false */
		allowresize: false, /* true/false */
		counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
		theme: 'dark_rounded', /* light_rounded / dark_rounded / light_square / dark_square */
		width: 1024,
		height: 744,
		// flowplayer settings - start
		fp_bgcolor: '<?php echo '0x' . _BGCOLOR;?>',
		fp_timecolor: '<?php echo '0x' . _TIMECOLOR;?>',
		fp_swf_loc: '<?php echo _URL .'/player.swf';  ?>',
		// flowplayer settings - end 
		callback: function(){}
	});
<?php endif; ?>

	$('a[id^="show-more-"]').click(function(){
		var id = $(this).attr('id').match(/\d+$/);
		$(this).hide();
		$('#excerpt-'+id).hide();
		$('#full-text-'+id).show();
		$('#show-less-'+id).show();
		return false;
	});
	$('a[id^="show-less-"]').click(function(){
		var id = $(this).attr('id').match(/\d+$/);
		$(this).hide();
		$('#full-text-'+id).hide();
		$('#show-more-'+id).show();
		$('#excerpt-'+id).show();
		return false;
	});
	
	$(document).ready(function() {
		$('[placeholder]').focus(function() {
		  var input = $(this);
		  if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		  }
		}).blur(function() {
		  var input = $(this);
		  if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		  }
		}).blur();
		$('[placeholder]').parents('form').submit(function() {
		  $(this).find('[placeholder]').each(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
			  input.val('');
			}
		  })
		});
	});
	
	function validateFormOnSubmit(theForm, say_reason) {	
	 
	 var reason = say_reason;
	 var counter = 0;
	 
	 $("input,textarea").each(function(){
	 
		if($(this).attr('id') == "must")
		{
			if ($(this).attr("value").length == 0)
			{
				$(this).css("background", "#FFD2D2");
				counter++;
			}
			else
			{
				$(this).css("background", "#FFFFFF");
			}	
		}
	 });
	 
	 if (counter > 0) {
	   alert(reason);
	   return false;
	 }
	 return true;
	}


	function validateSearch(b_on_submit){
		if(document.forms['search'].keywords.value == '' || document.forms['search'].keywords.value == 'search'){
			alert('شما شرط جستجویی وارد نکردید. لطفا از دوباره تلاش کنید.');
			if(b_on_submit == 'true')
				return false;
		}
		else{
			document.forms['search'].submit();
		}
	}
	function confirm_delete_all() {
		var confirm_msg = "شما در حال حذف کردن تمام آیتم های انتخاب شده هستید. لطفا برای توقف بر روی 'کنسل' یا برای ادامه بر روی 'تایید' کلیک کنید";	 // refers to articles, videos and users
		var response = false;
		if (confirm(confirm_msg)) {
		} else {
			return false;
		}
	}
<?php if ($load_settings_theme_resources) : ?>
 $(document).ready(function(){
	$('#btn-remove-logo').click(function(){
		 if (confirm('ایا از این که لوگو کنونی را می خواهید حذف کنید مطمئن هستید؟')) {
			$.ajax({
				url: '<?php echo _URL .'/admin/admin-ajax.php'?>',
				data: {
					p: "layout-settings",
					"do": "delete-logo"
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#btn-remove-logo').hide();
					$('#show-logo').html(" ");
				}
			});
		}
		return false;
	});
});
<?php endif;?>

$(document).ready(function() {
 $('.tablesorter tr')
  .filter(':has(:checkbox:checked)')
	.addClass('selected')
  .end()
 .click(function(event) {
	var disallow = { "A":1, "IMG":1, "INPUT":1, "I":1, "TH":1, "TEXTAREA":1, "SPAN":1 }; 
  if(!disallow[event.target.tagName]) {
   $(':checkbox', this).trigger('click');
  }
 })
  .find(':checkbox')
  .click(function(event) {
   $(this).parents('tr:first').toggleClass('selected');
  });
  $("#selectall").click(function (event) {
	  if($('.tablesorter tr').filter(':has(:checkbox:checked)').removeClass('selected').end()) {
		$('.tablesorter tr').toggleClass("selected");
	  }
  });
  
  // inline add new category
  $('#inline_add_new_category').click(function(){
  	$('#inline_add_new_category_form').slideToggle(0);
	$('#add_category_name').focus();
	return false;
  });
  
  $('button[name="add_category_submit_btn"]').click(function(event){
  	event.preventDefault();
	
	$('#add_category_response').html();
	var current_page = "<?php $tmp_parts = explode('/', $_SERVER['SCRIPT_NAME']); $tmp_script = array_pop($tmp_parts); echo $tmp_script; ?>";
	var category_name = $('input[name="add_category_name"]').val();
	var category_slug = $('input[name="add_category_slug"]').val();
	var parent_id = $('input[name="add_category_parent_id"]').val();
	var chzn_is_on = false;
	<?php if ($load_chzn_drop) : ?>
	chzn_is_on = true;
	<?php endif;?>

	// check if required fields are set
	if (category_name === "" || category_name == $('input[name="add_category_name"]').attr('placeholder')) {
		$('input[name="add_category_name"]').trigger('focus');
	} else if (category_slug === "" || category_slug == "Slug") {
		$('input[name="add_category_slug"]').trigger('focus');
	} else {
		if (current_page == "article_manager.php") {
			var ajax_page = "article-category-mgr";
			var ajax_do = "inline-add-category";
			var parent_select_name = 'categories[]';
		} else {
			var ajax_page = "video-category-mgr";
			var ajax_do = "add-category";
			var parent_select_name = 'category[]';
		}

		$.ajax({
			url: "admin-ajax.php",
			data: {
			    p: ajax_page,
			    name: $('input[name=add_category_name]').val(),
			    tag: $('input[name=add_category_slug]').val(),
			    category: $('select[name=add_category_parent_id]').val(),
				current_selection: $('select[name="'+ parent_select_name +'"]').val(),
				"do": ajax_do
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				if (data['type'] == "error") {
					$('#add_category_response').html(data['message']);
				} else {
					// remove current Chosen instance html (no destroy method provided)
					if (chzn_is_on) {
						$(".chzn-select").removeAttr("style", "").removeClass("chzn-done").data("chosen", null).next().remove();
					}
					
					// update parent category dropdown   
					$('select.category_dropdown').replaceWith(data['message']);

					$('#add_category_response').html('');
					
					// update Create-in category dropdown
					$('select[name=add_category_parent_id]').replaceWith(data['create-category-select-html']);
					
					// clear Create-new-category form data
					$('input[name=add_category_name]').val("");
					$('input[name=add_category_slug]').val("");
					
					// create new Chosen instance for updated parent category dropdown
					if (chzn_is_on) {
						$('.category_dropdown').addClass("chzn-select");
						$(".chzn-select").chosen();
						$(".chzn-select-deselect").chosen({allow_single_deselect:true});
					}
				}
			}
		});
	}
	return false;
  });
});

<?php if ($load_swfupload_upload_image_handlers): ?>
$(document).ready(function() {
	
	// WYSIWYG Editor image uploader
	$('#ButtonPlaceHolder').swfupload({
	    upload_url: "upload_image.php",
	    
	    file_size_limit: "<?php echo ($upload_max_filesize > 0) ? $upload_max_filesize.'' : '0';?>",
	    file_types: "*.jpg;*.jpeg;*.png;*.gif",
	    file_types_description: "Image files",
	    file_upload_limit: 30,
	    flash_url: "js/swfupload/swfupload.swf",
	    button_width: 114,
	    button_height: 29,
	    custom_settings: {
	        progressTarget: "fsUploadProgress"
	    },
	    post_params: {
	        "PHPSESSID": "<?php echo session_id(); ?>"
	    },
	    // Button settings
	    //button_image_url: "js/swfupload/upload.png",
	    button_placeholder_id: "ButtonPlaceHolder",
	    button_width: "110",
	    button_height: "20",
	    button_text: 'Upload images',
	    button_text_style: '.button-text { text-align: center; font-size: 11px; font-weight: bold;font-family: Arial, Geneva, Verdana, sans-serif; letter-spacing:-0.045em; text-shadow: 0 1px 0 #FFF; }',
	    button_text_top_padding: 2,
	    button_text_left_padding: 0,
	    //button_window_mode: "window",
	    button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
	    button_cursor: SWFUpload.CURSOR.HAND,
	    debug: false
	})
	.bind('fileQueued', function(event, file){
	    var listitem = '<li id="' + file.id + '" >' +
	    'File: <em>' +
	    file.name +
	    '</em> (' +
	    Math.round(file.size / 1024) +
	    ' KB) <span class="progressvalue" ></span>' +
	    '<div class="progressbar" ><div class="progress" ></div></div>' +
	    '<p class="status" >Pending</p>' +
	    '<span class="cancel" >&nbsp;</span>' +
	    '</li>';
	    $('#uploadLog').append(listitem);
	    $('li#' + file.id + ' .cancel').bind('click', function(){
	        var swfu = $.swfupload.getInstance('#swfupload-control');
	        swfu.cancelUpload(file.id);
	        $('li#' + file.id).slideUp('fast');
	    });
	    // start the upload since it's queued
	    $(this).swfupload('startUpload');
	})
	.bind('fileQueueError', function(event, file, errorCode, message){
	    alert('اندازه فایل ' + file.name + ' بزرگ تر از محدودیت ایجاد شده است');
	})
	.bind('fileDialogStart', function(event){})
	.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
	    $('#fsUploadProgress').text('Uploaded: ' + numFilesSelected + ' file(s)');
	})
	.bind('uploadStart', function(event, file){
	    $('#uploadLog li#' + file.id).find('p.status').text('Uploading...');
	    $('#uploadLog li#' + file.id).find('span.progressvalue').text('0%');
	    $('#uploadLog li#' + file.id).find('span.cancel').hide();
	})
	.bind('uploadProgress', function(event, file, bytesLoaded){
	    //Show Progress
	    var percentage = Math.round((bytesLoaded / file.size) * 100);
	    $('#uploadLog li#' + file.id).find('div.progress').css('width', percentage + '%');
	    $('#uploadLog li#' + file.id).find('span.progressvalue').text(percentage + '%');
	})
	.bind('uploadSuccess', function(event, file, serverData){
	    var item = $('#uploadLog li#' + file.id);
	    item.find('div.progress').css('width', '100%');
	    item.find('span.progressvalue').text('100%');
	    var pathtofile = '<a href="uploads/' + file.name + '" target="_blank" >view &raquo;</a>';
	    item.addClass('success').find('p.status').html('Uploaded!');
	    if ($("#wysiwygtextarea-WYSIWYG").length > 0) {
	        $("#wysiwygtextarea-WYSIWYG").contents().find("body").append(serverData);
	    }
	    else 
	        if ($("#textarea-WYSIWYG").length > 0) {
	            var textarea = $("#textarea-WYSIWYG").val();
	            $("#textarea-WYSIWYG").val(textarea + serverData);
	        }
	    setTimeout(function(){
	        $('#uploadLog li#' + file.id).fadeOut('slow');
	    }, 2000);
	})
	.bind('uploadComplete', function(event, file){
	    // upload has completed, try the next one in the queue
	    $(this).swfupload('startUpload');
	})
	.bind('uploadError', function(event, file, errorCode, message){
		//file.name
		alert("خطای آپلود: " + message);
	});

	
	// video thumbnail image uploader/editor
	$('#thButtonPlaceholder').swfupload({
	    upload_url: "upload_image.php",
	    
	    file_size_limit: "<?php echo ($upload_max_filesize > 0) ? $upload_max_filesize.'' : '0';?>",
	    file_types: "*.jpg;*.jpeg;*.png;*.gif",
	    file_types_description: "Image files",
	    file_upload_limit: 0,
	    file_queue_limit: 1,
	    flash_url: "js/swfupload/swfupload.swf",
	    button_width: 114,
	    button_height: 24,
	    custom_settings: {
	        progressTarget: "thUploadProgress"
	    },
	    post_params: {
	        "PHPSESSID": "<?php echo session_id(); ?>",
	        "uniq_id": "<?php echo $uniq_id; ?>",
	        "doing": "video-thumb"
	    },
	    // Button settings
	    //button_image_url: "js/swfupload/upload.png",
	    button_placeholder_id: "thButtonPlaceholder",
	    button_width: "60",
	    button_height: "24",
	    button_text: 'Change',
	    button_text_style: '.button-text { text-align: center; font-size: 11px; font-weight: bold;font-family: Arial, Geneva, Verdana, sans-serif; letter-spacing:-0.045em; text-shadow: 0 1px 0 #FFF; }',
	    button_text_top_padding: 5,
	    button_text_left_padding: 0,
	    //button_window_mode: "window",
	    button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
	    button_cursor: SWFUpload.CURSOR.HAND,
	    debug: false
	})
	.bind('fileQueued', function(event, file){
	    var listitem = '<li id="' + file.id + '" >' +
	    //  'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
	    '<div class="progressbar" ><div class="progress" ></div></div>' +
	    '<p class="status" >در حالت انتظار</p>' +
	    '<span class="cancel" >&nbsp;</span>' +
	    '</li>';
	    $('#uploadThumbLog').append(listitem);
	    $('li#' + file.id + ' .cancel').bind('click', function(){
	        var swfu = $.swfupload.getInstance('#swfupload-control');
	        swfu.cancelUpload(file.id);
	        $('li#' + file.id).slideUp('fast');
	    });
	    // start the upload since it's queued
	    $(this).swfupload('startUpload');
	})
	.bind('fileQueueError', function(event, file, errorCode, message){
	    alert('اندازه فایل ' + file.name + ' بزرگ تر از محدودیت ایجاد شده است');
	})
	.bind('fileDialogStart', function(event){})
	.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
	    //$('#thUploadProgress').text('Uploaded: '+numFilesSelected+' file(s)');
	})
	.bind('uploadStart', function(event, file){
	    $('#uploadThumbLog li#' + file.id).find('p.status').text('Uploading...');
	    $('#uploadThumbLog li#' + file.id).find('span.progressvalue').text('0%');
	    $('#uploadThumbLog li#' + file.id).find('span.cancel').hide();
	})
	.bind('uploadProgress', function(event, file, bytesLoaded){
	    //Show Progress
	    var percentage = Math.round((bytesLoaded / file.size) * 100);
	    $('#uploadThumbLog li#' + file.id).find('div.progress').css('width', percentage + '%');
	    $('#uploadThumbLog li#' + file.id).find('span.progressvalue').text(percentage + '%');
	})
	.bind('uploadSuccess', function(event, file, serverData){
	    var item = $('#uploadThumbLog li#' + file.id);
	    item.find('div.progress').css('width', '100%');
	    item.find('span.progressvalue').text('100%');
	    var pathtofile = '<a href="uploads/' + file.name + '" target="_blank" >view &raquo;</a>';
	    item.addClass('success').find('p.status').html('آپلود شد! برای اعمال تغییرات این صفحه بر روی "ذخیره کردن" کلیک کنید.');
	    $('#showThumb').html(serverData);
	    setTimeout(function(){
	        $('#uploadThumbLog li#' + file.id).fadeOut('slow');
	    }, 2000);
	})
	.bind('uploadComplete', function(event, file){
	    // upload has completed, try the next one in the queue
	    $(this).swfupload('startUpload');
	})
	.bind('uploadError', function(event, file, errorCode, message){
		//file.name
		alert("خطای آپلود: " + message);
	});
	
	$('#changeFlvButtonPlaceholder').swfupload({
	    upload_url: "upload_file.php",
	    
	    file_size_limit: "<?php echo ($upload_max_filesize > 0) ? $upload_max_filesize.'' : '0';?>",
	    file_types: "*.flv;*.mp4;*.mov;*.wmv;*.divx;*.avi;*.mkv;*.asf;*.wma;*.mp3;*.m4v;*.m4a;*.3gp;*.3g2",
	    file_types_description: "Video files",
	    file_upload_limit: 0,
	    file_queue_limit: 1,
	    flash_url: "js/swfupload/swfupload.swf",
	    button_width: 114,
	    button_height: 20,
	    custom_settings: {
	        progressTarget: "changeFlvUploadProgress"
	    },
	    post_params: {
	        "PHPSESSID": "<?php echo session_id(); ?>",
	        "uniq_id": "<?php echo $uniq_id; ?>",
			"doing": "modify"
	    },
	    // Button settings
	    //button_image_url: "js/swfupload/upload.png",
	    button_placeholder_id: "changeFlvButtonPlaceholder",
	    button_width: "60",
	    button_height: "24",
	    button_text: 'Change',
	    button_text_style: '.button-text { text-align: center; font-size: 11px; font-weight: bold;font-family: Arial, Geneva, Verdana, sans-serif; letter-spacing:-0.045em; text-shadow: 0 1px 0 #FFF; }',
	    button_text_top_padding: 5,
	    button_text_left_padding: 0,
	    //button_window_mode: "window",
	    button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
	    button_cursor: SWFUpload.CURSOR.HAND,
	    debug: false
	})
	.bind('fileQueued', function(event, file){
		var listitem = '<li id="' + file.id + '" >' +
	    //  'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
	    '<div class="progressbar" ><div class="progress" ></div></div>' +
	    '<p class="status" >Pending</p>' +
	    '<span class="cancel" >&nbsp;</span>' +
	    '</li>';
	    $('#uploadFlvLog').append(listitem);
	    $('li#' + file.id + ' .cancel').bind('click', function(){
	        var swfu = $.swfupload.getInstance('#swfupload-control');
	        swfu.cancelUpload(file.id);
	        $('li#' + file.id).slideUp('fast');
	    });
	    // start the upload since it's queued
	    $(this).swfupload('startUpload');
	})
	.bind('fileQueueError', function(event, file, errorCode, message){
	    alert('اندازه فایل ' + file.name + ' بزرگ تز از محدودیت ایجاد شده است');
	})
	.bind('fileDialogStart', function(event){})
	.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
	    //$('#thUploadProgress').text('Uploaded: '+numFilesSelected+' file(s)');
	})
	.bind('uploadStart', function(event, file){
	    $('#uploadFlvLog li#' + file.id).find('p.status').text('Uploading...');
	    $('#uploadFlvLog li#' + file.id).find('span.progressvalue').text('0%');
	    $('#uploadFlvLog li#' + file.id).find('span.cancel').hide();
	})
	.bind('uploadProgress', function(event, file, bytesLoaded){
	    //Show Progress
	    var percentage = Math.round((bytesLoaded / file.size) * 100);
	    $('#uploadFlvLog li#' + file.id).find('div.progress').css('width', percentage + '%');
	    $('#uploadFlvLog li#' + file.id).find('span.progressvalue').text(percentage + '%');
	})
	.bind('uploadSuccess', function(event, file, serverData){
	    var item = $('#uploadFlvLog li#' + file.id);
	    item.find('div.progress').css('width', '100%');
	    item.find('span.progressvalue').text('100%');
	    var pathtofile = '<a href="uploads/' + file.name + '" target="_blank" >view &raquo;</a>';
	    item.addClass('success').find('p.status').html('آپلود شد! برای اعمال تغییرات این صفحه بر روی "ذخیره کردن" کلیک کنید.');
	    $('#showFlv').html(serverData);
	    setTimeout(function(){
	        $('#uploadFlvLog li#' + file.id).fadeOut('slow');
	    }, 2000);
	})
	.bind('uploadComplete', function(event, file){
	    // upload has completed, try the next one in the queue
	    $(this).swfupload('startUpload');
	})
	.bind('uploadError', function(event, file, errorCode, message){
		//file.name
		alert("خطای آپلود: " + message);
	});
});
<?php endif; ?>


<?php if($load_tinymce == 1 && _SEOMOD): ?>

	// Permalink Adjuster
	
	//var inputJ = $('input[class="permalink-input"]').val().length;

	//$('input[class="permalink-input"]').attr('size', inputJ);

	var inputPermalink = $('input[class="permalink-input"]').val().length;
	if(inputPermalink > 0) {
		$('input[class="permalink-input"]').attr('size', inputPermalink).css('width', inputPermalink * 6.5 +'px');
	}
	// Permalink Adjuster
	/*

    var input = document.querySelectorAll('input[class="permalink-input"]');
	for(i=0; i<input.length; i++){
		input[i].setAttribute('size',input[i].getAttribute('placeholder').length);
	}


	function resizeInput() {
		if($(this).val().length > inputPermalink) {
			$(this).attr('size', $(this).val().length);
		}
	}
	$('input[class="permalink-input"]').keypress(resizeInput);
	*/


<?php endif; ?>
<?php if($config['keyboard_shortcuts'] == 1) : ?>
$(document).bind('keydown', 'shift+/', function() {
	$('#seeShortcuts').modal('show');
});
$(document).bind('keydown', 'c', function() {
	$('#addVideo').modal('show');
	$('#addVideo').on('shown', function () {
    	$('#yt_query').focus();
	});
});
$(document).bind('keydown', 'alt+s', function() {
	window.location = 'settings.php';
	return false;
});
$(document).bind('keydown', 'alt+l', function() {
	window.location = 'settings_theme.php';
	return false;
});
$(document).bind('keydown', 'alt+v', function() {
	window.location = 'videos.php';
	return false;
});
$(document).bind('keydown', 'alt+a', function() {
	window.location = 'articles.php';
	return false;
});
$(document).bind('keydown', 'alt+p', function() {
	window.location = 'pages.php';
	return false;
});
$(document).bind('keydown', 'alt+c', function() {
	window.location = 'comments.php';
	return false;
});
$(document).bind('keydown', 'shift+a', function() {
	$(".pm-tables").each(function(){
		if ( $('input:checkbox').attr('checked')) {
			$('input:checkbox').attr('checked', false);
		} else {
			$('input:checkbox').attr('checked', 'checked');
		}
	});
	if($('.tablesorter tr').filter(':has(:checkbox:checked)').removeClass('selected').end()) {
		$('.tablesorter tr').toggleClass("selected");
	}
	<?php if($load_ibutton == 1): ?>
	$("#import-opt-content").each(function(){
	
		if ( $('input[name^="video_ids"]:checkbox').attr('checked')) {
			$('input[name^="video_ids"]:checkbox').attr('checked', false);
		} else {
			$('input[name^="video_ids"]:checkbox').attr('checked', 'checked');
		}

		$('.on_off :checkbox').iButton("repaint");
		if($('.on_off :checkbox').is(":checked")) {
		  $('.video-stack').addClass("stack-selected");
		}else {
		  $('.video-stack').removeClass("stack-selected");
		}

	});
	<?php endif; ?>

});
$(document).bind('keydown', 'shift+s', function() {
	$('#form-search-input').focus();
	$('#form-search-input').css({"border":"1px solid #ddd"});	
	return false;
});
<?php endif; ?>

$(document).ready(function(){
	$('#meta_switch_select_input').click(function(event){
		event.preventDefault();
		
		$('#meta_key_select').hide();
		$(this).hide();
		$('input[name="meta_key"]').show();
		$('#meta_switch_input_select').show('50');
		
		return false;
	});
	
	$('#meta_switch_input_select').click(function(event){
		event.preventDefault();
		
		$('#meta_key_select').show();
		$(this).hide();
		$('input[name="meta_key"]').hide();
		$('#meta_switch_select_input').show('50');
		
		return false;
	});
	
	$('#add_meta_btn').click(function(event){
		event.preventDefault();
		
		$('#new-meta-error').html('');

		if (($('input[name="meta_key"]').val() != "" 
			 && $('input[name="meta_key"]').val() != $('input[name="meta_key"]').attr('placeholder')) 
			|| ($('select[name="meta_key_select"]').val() != "_nokey"))
		{
			var input_key = '';
			var input_value = '';
			
			if ($('input[name="meta_key"]').val() != "" 
				 && $('input[name="meta_key"]').val() != $('input[name="meta_key"]').attr('placeholder'))
			{
				input_key = $('input[name="meta_key"]').val();
			} else {
				input_key = $('select[name="meta_key_select"]').val();
			}
			
			if ($('input[name="meta_value"]').val() != $('input[name="meta_value"]').attr('placeholder')) {
				input_value = $('input[name="meta_value"]').val();
			}
			
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": "metadata",
					"do": "add-meta",
				    "meta_key": input_key,
				    "meta_value": input_value,
					"item_id": $('input[name="meta_item_id"]').val(),
				    "item_type": $('input[name="meta_item_type"]').val(),
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data['type'] == "error") {
						$('#new-meta-error').html(data['html']);
					} else {
					 
						$('#new-meta-placeholder').append(data['html']);
						
						// clear form
						$('input[name="meta_key"]').val("");
						$('input[name="meta_value"]').val("");
						
						bind_metadata_actions(data['meta_id']);
					}
				}
			});
		}
		else {
			$('input[name="meta_key"]').trigger('focus');
		}
		return false;
	});
	
	$('div[id^="meta-row-"]').click(function() {
			$(this).find('button.btn-normal').css('box-shadow','0 1px 3px #bee1be').removeClass("btn-normal").addClass("btn-success");
			$('input').change(function() {
				$(this).css('border', '1px solid #96ce96');
			});
	});
	
	bind_metadata_actions("");
});

function bind_metadata_actions(selector_meta_id)
{
	var update_btn_selector = '[id^="update_meta_btn_"]';
	var delete_btn_selector = '[id^="delete_meta_btn_"]';
	
	if (selector_meta_id != "")
	{
		update_btn_selector = '#update_meta_btn_' + selector_meta_id;
		delete_btn_selector = '#delete_meta_btn_' + selector_meta_id;
	}
	
	$(update_btn_selector).click(function(event){
		event.preventDefault();
		
		var meta_id = $(this).attr('id').replace( /^\D+/g, '');
		
		$('#update-response-'+ meta_id).html('');

		if ($('input[name="meta['+ meta_id +'][key]"]').val() === "") {
			$('input[name="meta['+ meta_id +'][key]"]').trigger('focus');
		} else {
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": "metadata",
					"do": "update-meta",
				    "meta_id": meta_id,
					meta_key: $('input[name="meta['+ meta_id +'][key]"]').val(),
				    meta_value: $('input[name="meta['+ meta_id +'][value]"]').val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data['type'] == 'success') {
						$('#update-response-'+ meta_id).html(data['html']).show().delay(2000).fadeOut("slow");
					} else { 
						$('#update-response-'+ meta_id).html(data['html']).show();
					}
				}
			});
		}
		return false;
	});
	
	$(delete_btn_selector).click(function(event){
		event.preventDefault();
		
		var meta_id = $(this).attr('id').replace( /^\D+/g, '');
		
		$.ajax({
			url: 'admin-ajax.php',
			data: {
				"p": "metadata",
				"do": "delete-meta",
			    "meta_id": meta_id
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#meta-row-'+ meta_id).css('border-bottom', '5px solid #f4543c').slideUp();
			}
		});

		return false;
	});
}
</script>
