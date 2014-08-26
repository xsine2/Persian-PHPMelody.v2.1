// upload.js
$(document).ready(function(){
	  
  var swfuploader = $('#uploadButtonPlaceholder').swfupload({
		upload_url: MELODYURL2 + "/ajax.php",
		file_size_limit: $('input[name="MAX_FILE_SIZE"]').val() + " B",
	    file_types: "*.flv;*.mp4;*.mov;*.wmv;*.divx;*.avi;*.mkv;*.asf;*.wma;*.mp3;*.m4v;*.m4a;*.3gp;*.3g2",
	    file_types_description: "Video files",
	    file_upload_limit: 1,
	    file_queue_limit: 1,
	    flash_url: "js/swfupload.swf",
	    custom_settings: {
	        progressTarget: "uploadProgressBar"
	    },
	    post_params: {
			"SID": $.cookie('PHPSESSID'),
			"p": "upload",
			"do": "useruploadvideo",
			"form_id": $('input[name="form_id"]').val(),
			"_pmnonce_t": $('input[name="_pmnonce_t"]').val()
	    },
	    // Button settings
	    //button_image_url: "js/swfupload/upload.png",
	    button_placeholder_id: "uploadButtonPlaceholder",
	    button_width: 120,
	    button_height: 30,
	    button_text: pm_lang.swfupload_btn_select,
	    button_text_style: '.button-text { font-size: 11px; font-weight: bold; font-family: Arial, Geneva, Verdana, sans-serif; letter-spacing:-0.045em; text-align: center; }',
	    button_text_top_padding: 5,
	    button_text_left_padding: 0,
	    //button_window_mode: "window",
	    button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
	    button_cursor: SWFUpload.CURSOR.HAND,
	    debug: false
	})
	.bind('swfuploadLoaded', function(event){
	})
	.bind('fileQueued', function(event, file){
		var size_kb = Math.round(file.size / 1024);
		var listitem = '<li id="' + file.id + '" >' +
						pm_lang.swfupload_file + ': <em>' +
						file.name +
						'</em> (' +
						size_kb +
						' KB)' +
						' <span class="progressvalue"></span>' +
						' <span class="cancel">'+ pm_lang.swfupload_btn_cancel +'</span>' +
						'<div class="progressbar" style="display:none;"><div class="progress" style="width:0%;"></div></div>' +
						'<p class="status">'+ pm_lang.swfupload_status_pending +'</p>' +
						'</li>';
		$('#uploadLog').html(listitem);
		
		if (size_kb >= 204800) { //200MB
			$("#duration").mask("9:99:99");
		} else {
			$("#duration").mask("99:99");
		}

		$('li#' + file.id + ' .cancel').bind('click', function(){
			var swfu = $.swfupload.getInstance(swfuploader);
			swfu.cancelUpload(file.id);
			$('li#' + file.id).slideUp('fast');
		});
		//$(this).swfupload('startUpload');
	})
	.bind('fileQueueError', function(event, file, errorCode, message){
    	if (pm_lang.swfupload_error_oversize != "") {
			//$("#error-placeholder").html(pm_lang.swfupload_error_oversize +" (" + pm_lang.swfupload_friendly_maxsize +").").show();
			$('#uploadLog').html(pm_lang.swfupload_error_oversize +" (" + pm_lang.swfupload_friendly_maxsize +").");
			//$.swfupload.getInstance(swfuploader).cancelUpload();
		} else {
			//$("#error-placeholder").html("Size of the file is greater than the limit allowed ("+ $('input[name="MAX_FILE_SIZE"]').val() + " B).").show();
			$('#uploadLog').html("Size of the file(s) is greater than allowed limit ("+ $('input[name="MAX_FILE_SIZE"]').val() + " B).");
		}
	})
	.bind('fileDialogStart', function(event,  numFilesSelected, numFilesQueued){
		var swfu = $.swfupload.getInstance(swfuploader);
		swfu.cancelQueue();
	})
	.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
		//$('#uploadProgressBar').text('Queued ' + numFilesSelected + ' file(s)');
	})
	.bind('uploadStart', function(event, file){
		// disable submit button
		$('#upload_btn').attr("disabled", "disabled").addClass("disabled");

		$('#uploadLog li#' + file.id).find('p.status').text(pm_lang.swfupload_status_uploading);
		$('#uploadLog li#' + file.id).find('span.progressvalue').text('0%');
		$('#uploadLog li#' + file.id).find('span.cancel').hide();
	})
	.bind('uploadProgress', function(event, file, bytesLoaded){
	    //Show Progress
		var percentage = Math.round((bytesLoaded / file.size) * 100);
		$('#uploadLog li#' + file.id).find('div.progressbar').css('display', 'block');
	    $('#uploadLog li#' + file.id).find('div.progress').css('width', percentage + '%');
	    $('#uploadLog li#' + file.id).find('span.progressvalue').text(percentage + '%');
	})
	.bind('uploadSuccess', function(event, file, serverData){
	    var item = $('#uploadLog li#' + file.id);
	    item.find('div.progress').css('width', '100%');
	    item.find('span.progressvalue').text('100%');
	    item.addClass('success').find('p.status').html(pm_lang.swfupload_status_uploaded);
	    
		var temp_id = parseInt(serverData.replace( /^\D+/g, ''));

		if (serverData.indexOf('__success__') >= 0) {
			$('#upload_btn').attr('disabled', 'disabled');
			$('#temp_id').val(temp_id);
			$('#upload-video-form').submit();
		} else { // server returned an error
			$('#upload_btn').attr("disabled", "").removeClass("disabled");
			//$("#error-placeholder").html(serverData);
			$('#uploadLog li#' + file.id).find('p.status').html(pm_lang.swfupload_status_error +': '+ serverData);
		}
	})
	.bind('uploadComplete', function(event, file){
    	// upload has completed, try the next one in the queue
    	//$(this).swfupload('startUpload');
	})
	.bind('uploadError', function(event, file, errorCode, message){
		$('#uploadLog li#' + file.id).find('p.status').html(pm_lang.swfupload_status_error +': '+ message);
		$('#upload_btn').attr("disabled", "").removeClass("disabled");
	});

	$("#duration").mask("99:99");
    $('input[name="mediafile"]').change(function(){
        $("#error-placeholder").hide();
        //$("#upload-video-extra").slideDown()
    });
	
	$('#upload_btn').click(function(){
        var a = $("#error-placeholder");
		var swfu = $.swfupload.getInstance(swfuploader);
		var swfu_stats = swfu.getStats();
		
		if (swfu_stats.files_queued == 0) {
           a.html(pm_lang.validate_select_file).show();
		   //swfu.SelectFile(); SelectFile deprecated in Flash 10.
           return false
        }
        if ($('input[name="video_title"]').val() == "") {
            a.html(pm_lang.validate_video_title).show();
			$('input[name="video_title"]').trigger('focus');
            return false
        }
        if ($('select[name="category"]').val() == "-1") {
            a.html(pm_lang.choose_category).show();
            $('select[name="category"]').trigger('focus');
			return false
        }

		swfuploader.swfupload('startUpload');
		return false;
	});
});