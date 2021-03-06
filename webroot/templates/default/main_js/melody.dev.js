// watch.js
function onpage_delete_favorite(d, a){
    var b = pm_lang.onpage_delete_favorite_confirm;
    var c = false;
    if (confirm(b)) {
        if (a.length > 0) {
            ajax_request("favorites", "do=onpage_delete_favorite&vid=" + d, "", "", false);
            $(a).fadeOut("slow")
        }
    }
}

function fav_send_request(){
    $.ajax({
        type: "POST",
        url: MELODYURL2 + "/ajax_favorite.php",
        data: {
            video_id: $('input[name="fav_video_id"]').val(),
            user_id: $('input[name="fav_user_id"]').val()
        },
        dataType: "json",
        success: function(a){
            if (a.success == false) {
                alert(a.msg);
                return false
            }
            $("#fav_save_button").replaceWith(a.html);
            $("#fav_save_button").click(function(){
                return fav_send_request()
            });
            return false
        }
    });
    return false
}

$(document).ready(function(){
    $("#addtofavorites").submit(function(){
        return false
    });
    $("#fav_save_button").click(function(){
        return fav_send_request()
    });
    $("#pm-vc-share").click(function(){
        $("#securimage-share").prop("src", MELODYURL2 + "/include/securimage_show.php?sid=" + Math.random())
    });
    $('form[name="sharetofriend"]').submit(function(){
        $("#share-confirmation").hide();
        $.ajax({
            type: "POST",
            url: MELODYURL2 + "/ajax.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function(a){
                $("#share-confirmation").html(a.msg).show();
                if (a.success == true) {
                    $('form[name="sharetofriend"]').slideUp()
                }
            }
        });
        return false
    });
    $("#pm-vc-report").click(function(){
        $("#securimage-report").prop("src", MELODYURL2 + "/include/securimage_show.php?sid=" + Math.random())
    });
    $('form[name="reportvideo"]').submit(function(){
        $("#report-confirmation").hide();
        $.ajax({
            type: "POST",
            url: MELODYURL2 + "/ajax.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function(a){
                $("#report-confirmation").html(a.msg).show();
                if (a.success == true) {
                    $('form[name="reportvideo"]').slideUp()
                }
            }
        });
        return false
    })
});

// suggest.js
$(document).ready(function(){
    $("#suggest-form").submit(function(){
        return false
    });
    var c = $("#loading-gif-top");
    var d = $("#loading-gif-bottom");
    var b = $("#ajax-error-placeholder");
    var a = $("#ajax-success-placeholder");
    $('#suggest-form input[name="yt_id"]').bind("input propertychange", function(f){
        if ($(this).val() == "") {
            return false
        }
        c.show();
        $("#suggest-video-extra").slideUp();
        b.html("").hide();
        a.html("").hide();
        $.ajax({
            type: "POST",
            url: MELODYURL2 + "/ajax.php",
            data: {
                p: "suggest",
                "do": "getdata",
                url: $(this).val()
            },
            dataType: "json",
            success: function(e){
                c.hide();
                if (e.failed) {
                    b.html(e.message).show()
                }
                else {
                    if (e.success) {
                        b.hide();
                        $("#suggest-video-extra").slideDown();
                        $("#video-thumb-placeholder").html('<div class="suggest-video-placeholder"><img src="' + e.videodata.yt_thumb + '" title="" /></div>').show();
                        $('#suggest-form input[name="video_title"]').val(e.videodata.video_title);
                        $('#suggest-form textarea[name="description"]').val(e.videodata.description);
                        $('#suggest-form input[name="tags"]').val(e.videodata.tags);
			$('#suggest-form input[name="source_id"]').val(e.videodata.source_id);
                    }
                }
            }
        });
        return false
    });
    $('#suggest-form button[type="submit"]').click(function(){
        b.html("").hide();
        a.html("").hide();
        if ($('input[name="video_title"]').val() == "") {
            b.html(pm_lang.validate_video_title).show();
			$('input[name="video_title"]').trigger('focus');
            return false
        }
        if ($('select[name="category"]').val() == "-1") {
            b.html(pm_lang.choose_category).show();
			$('select[name="category"]').trigger('focus');
            return false
        }
        d.show();
        var e = $("#suggest-form").serialize();
        $.ajax({
            type: "POST",
            url: MELODYURL2 + "/ajax.php",
            data: e,
            dataType: "json",
            success: function(f){
                d.hide();
                if (f.failed) {
                    b.html(f.message).show()
                }
                else {
                    if (f.success) {
                        b.hide();
                        a.html(f.message).show();
                        $("#suggest-video-extra").slideUp();
                        $("#suggest-form").each(function(){
                            this.reset()
                        })
                    }
                }
            }
        });
        return false
    })
});

// general.js
function validateSearch(a){
    if (document.forms.search.keywords.value == "" || document.forms.search.keywords.value == "search") {
        alert("You did not enter a search term. Please try again.");
        if (a == "true") {
            return false
        }
    }
    else {
        document.forms.search.submit()
    }
}

function lookup(a){
    if (a.length == 0) {
        $("#suggestions").hide()
    }
    else {
        if (a.length > 2) {
            $.post(MELODYURL2 + "/ajax_search.php", {
                queryString: "" + a + ""
            }, function(b){
                if (b.length > 0) {
                    $("#suggestions").show();
                    $("#autoSuggestionsList").html(b)
                }
            })
        }
    }
}

function fill(a){
    $("#inputString").val(a);
    setTimeout("$('#suggestions').hide();", 200)
}

function ajax_request(f, e, c, d, a){
	var b = false;
    if (d.length == 0) {
        d = "html"
    }
    if (c.length > 0) {
        $(c).html('<img src="' + TemplateP + '/img/ajax-loading.gif" alt="Loading" id="loading" /> Loading...').fadeIn("normal")
    }
    $.ajax({
        type: "GET",
        url: MELODYURL2 + "/ajax.php",
        data: "p=" + f + "&" + e,
        dataType: d,
        success: function(g){
            if (c.length > 0) {
                $(c).html(g);
                if (a == true) {
                    $(c).show();
					if (f == 'comments') {
						bind_comment_user_actions();
					}
                }
            }
            b = true
        }
    });
    return b
}

var arPlayer = null;
var arDone = false;
function reportNotFound(a){
    if (a.length > 0 && !arDone) {
        ajax_request("video", "do=report&vid=" + a, "", "", false);
        arDone = true
    }
}

function onStreamNotFound(a){
    reportNotFound(a.name)
}

function triggerPlayerError(a){
    reportNotFound(arPlayer.id)
}

function playerReady(a){
    arPlayer = document.getElementById(a.id);
    arPlayer.addModelListener("ERROR", "triggerPlayerError")
};

// comment.js
function onpage_delete_comment(e, d, a){
    var b = "You are about to delete this comment. Click 'Cancel' to stop, 'OK' to delete";
    var c = false;
    if (confirm(b)) {
        if (a.length > 0) {
            ajax_request("comments", "do=onpage_delete_comment&cid=" + e + "&vid=" + d, "", "", false);
            $(a).fadeOut("normal")
        }
    }
}

$(document).ready(function(){
    $("#preview_comment").hide();
    var a = false;
    $("#c_comment_txt").click(function(){
        if (!a && $("#c_user_id").val() == 0) {
            $("#captcha-image").prop("src", MELODYURL2 + "/include/securimage_show.php?sid=" + Math.random());
            a = true
        }
    });
    $("#c_submit").click(function(){
        $("#mycommentspan").html('<img src="' + TemplateP + '/img/ajax-loading.gif" alt="Loading" id="loading" />').show();
        var b = $("#c_user_id").val();
        var e = $("#c_vid").val();
        var d = $("#c_comment_txt").val();
        var f = $("#c_username").val();
        var c = $("#captcha").val();
        if (b == 0) {
            $.post(MELODYURL2 + "/comment.php", {
                username: f,
                captcha: c,
                vid: e,
                user_id: b,
                comment_txt: d
            }, function(g){
                if (g.cond == true) {
                    $("#pm-post-form").slideUp("normal", function(){
                        $("#mycommentspan").html(g.msg).show();
                        if (g.preview == true) {
                            $("#be_the_first").hide();
                            $("#preview_comment").html(g.html).fadeIn(700)
                        }
                    })
                }
                else {
                    if (g.cond == false) {
                        $("#c_submit").show();
                        $("#mycommentspan").html(g.msg).show()
                    }
                }
            }, "json")
        }
        else {
            if (b > 0) {
                $.post(MELODYURL2 + "/comment.php", {
                    vid: e,
                    user_id: b,
                    comment_txt: d
                }, function(g){
                    if (g.cond == true) {
                        $("#pm-post-form").slideUp("normal", function(){
                            $("#mycommentspan").html(g.msg).show();
                            if (g.preview == true) {
                                $("#be_the_first").hide();
                                $("#preview_comment").html(g.html).fadeIn(700)
                            }
                        })
                    }
                    else {
                        if (g.cond == false) {
                            $("#c_submit").show();
                            $("#mycommentspan").html(g.msg).show()
                        }
                    }
                }, "json")
            }
        }
        return false
    })
});

function comment_user_action(button, comment_id, user_id, action) {
	
	var page = 'comments';
	if (action == 'ban' || action == 'allow') {
		page = 'users';
	}

	$.ajax({
            type: 'GET',
            url: MELODYURL2 + "/ajax.php",
            data: {
                'comment_id': comment_id,
				'uid' : user_id,
                'p': page,
                'do': action
            },
            dataType: "json",
            success: function(c){
				if (c.success) {
					if(button.hasClass("active")) {
						button.removeClass("active");
					} else {
						button.addClass("active");
						if (action == 'like') {
							$('#comment-dislike-' + comment_id).removeClass('active');
						} else if (action == 'dislike') {
							$('#comment-like-' + comment_id).removeClass('active');
						}
					}
					
					if (action == 'allow' || action == 'ban') {
						if (c.show_label == true) {
							$('.label-banned-'+ user_id).removeClass('hide').show();
							$('.ban-'+ user_id).addClass("active");
							$('.unban-'+ user_id).addClass("active");
						}
						if (c.hide_label == true) {
							$('.label-banned-'+ user_id).hide();
							$('.ban-'+ user_id).removeClass("active");
							$('.unban-'+ user_id).removeClass("active");
						}
					} else {
						if (c.up_vote_count > 0) {
							$('#comment-like-count-'+ comment_id).html(c.up_vote_count);
						} else {						
							$('#comment-like-count-'+ comment_id).html('');
						}
						if (c.down_vote_count > 0) {
							$('#comment-dislike-count-'+ comment_id).html(c.down_vote_count);
						} else {
							$('#comment-dislike-count-'+ comment_id).html('');
						}
					}
				}
				if ( ! c.success && (action == 'allow' || action == 'ban') && c.error != '') {
					alert(c.error);
				}
		}
	});
	return false;
}

function bind_comment_user_actions() {
	// comment rating
	$('button[id^="comment-like-"]').click(function(){
		var comment_id = $(this).attr('id').replace( /^\D+/g, '');
		comment_user_action($(this), comment_id, 0, 'like');
		return false;
	});
	$('button[id^="comment-dislike-"]').click(function(){
		var comment_id = $(this).attr('id').replace( /^\D+/g, '');
		comment_user_action($(this), comment_id, 0, 'dislike');
		return false;
	});
	$('button[id^="comment-flag-"]').click(function(){
		var comment_id = $(this).attr('id').replace( /^\D+/g, '');
		comment_user_action($(this), comment_id, 0, 'flag');
		return false;
	});
	// ban/unban user
	$('button[id^="unban-"]').click(function(){
		var classes = $(this).attr('class').split(' ');
		var user_id = classes[0];
		user_id = user_id.replace( /^\D+/g, '');
		var comment_id = $(this).attr('id').replace( /^\D+/g, '');
		comment_user_action($(this), comment_id, user_id, 'allow');
		return false;
	});
	$('button[id^="ban-"]').click(function(){
		var classes = $(this).attr('class').split(' ');
		var user_id = classes[0];
		user_id = user_id.replace( /^\D+/g, '');
		var comment_id = $(this).attr('id').replace( /^\D+/g, '');
		comment_user_action($(this), comment_id, user_id, 'ban');
		return false;
	});
}

// bin-rating.js
$(document).ready(function(){
	
	bind_comment_user_actions();
	
	// like/dislike video
    $("#bin-rating-like").click(function(){
        var b = $(this);
        var a = $("#bin-rating-dislike");
        $("#bin-rating-response").html("").hide();
        $.ajax({
            type: "GET",
            url: MELODYURL2 + "/ajax.php",
            data: {
                vid: $('input[name="bin-rating-uniq_id"]').val(),
                p: "video",
                "do": "like"
            },
            dataType: "json",
            success: function(c){
                if (c.success) {
                    if (b.hasClass("active")) {
                        b.removeClass("active");
                        $("#rating-bar-up-pct").width(c.up_pct + "%");
                        $("#rating-bar-down-pct").width(c.down_pct + "%")
                    }
                    else {
                        b.addClass("active");
                        a.removeClass("active");
                        $("#rating-bar-up-pct").width(c.up_pct + "%");
                        $("#rating-bar-down-pct").width(c.down_pct + "%");
                        $("#bin-rating-dislike-confirmation").hide();
                        $("#bin-rating-like-confirmation").slideDown("normal")
                    }
                }
                else {
                    if (c.msg != "") {
                        $("#bin-rating-response").html(c.msg).slideToggle("normal")
                    }
                }
            }
        })
    });
    $("#bin-rating-dislike").click(function(){
        var b = $($("#bin-rating-like"));
        var a = $(this);
        $("#bin-rating-response").html("").hide();
        $.ajax({
            type: "GET",
            url: MELODYURL2 + "/ajax.php",
            data: {
                vid: $('input[name="bin-rating-uniq_id"]').val(),
                p: "video",
                "do": "dislike"
            },
            dataType: "json",
            success: function(c){
                if (c.success) {
                    if (a.hasClass("active")) {
                        a.removeClass("active");
                        $("#rating-bar-up-pct").width(c.up_pct + "%");
                        $("#rating-bar-down-pct").width(c.down_pct + "%")
                    }
                    else {
                        a.addClass("active");
                        b.removeClass("active");
                        $("#rating-bar-up-pct").width(c.up_pct + "%");
                        $("#rating-bar-down-pct").width(c.down_pct + "%");
                        $("#bin-rating-like-confirmation").hide();
                        $("#bin-rating-dislike-confirmation").slideDown("normal")
                    }
                }
                else {
                    if (c.msg != "") {
                        $("#bin-rating-response").html(c.msg).slideToggle("normal")
                    }
                }
            }
        })
    })
});

function confirm_action(confirm_message, action_goto_url)
{
	if (confirm(confirm_message)) {
		window.location = action_goto_url;
	}
	return false;
}
