<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.96down.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.96down.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@96down.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.96down.com) / support@96down.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '4';
$_page_title = 'نظردادن درباره ممانعت';
include('header.php');

$list = ''; 
$content = '';
$words = '';
$words_list = array();

$list = $_GET['list'];

if($list == ''){
	$list = 'censor_words';
}

switch($list){ 
	default:
	case 'censored':
		$file = '../censor_words.txt';
		$title = 'Censored words';
	break;
	
	case 'blacklist':		
		$file = '../blacklist.txt';
		$title = 'Blacklist';
	break;
}//	end switch

if($_POST['Submit'] == "Save"){
	$words = $_POST['words'];
	
	$temp_arr = explode("\n", $words);
	
	for($i = 0; $i < count($temp_arr); $i++){
		if(trim($temp_arr[$i]) != '' && strlen($temp_arr[$i]) > 1) 
			$words_list[] = $temp_arr[$i];
	}	
	$fp = fopen($file, "w");
	if(!$fp) { 
		echo '<div class="alert">متاسفانه فایل <strong>'.$file.'</strong> نمی تواند باز شود. بررسی کن اگر<strong>'.$file.'</strong> آپلود شد و اگر قابل نوشتن بود یعنی مجوز  آن 0777 بود.</div>';
		include('footer.php');
		exit();
	}
	$line = '';
	for($i = 0; $i < count($words_list); $i++){
		if($i != count($words_list)-1)
			$line = $words_list[$i]."\n";
		else
			$line = $words_list[$i];
		fwrite($fp, $line, strlen($line));
	}
	fclose($fp);
	$info_msg = '<div class="alert alert-success">لیست با موفقیت آپلود شد.</div>';
}
else{
	$fp = @fopen($file, "r");
	if(!$fp) { 
		echo '<div class="alert">متاسفانه فایل <strong>'.$file.'</strong> نمی تواند باز شود. بررسی کن اگر'.$file.'</strong> آپلود شد و اگر قابل نوشتن بود یعنی مجوز  آن 0777 بود.</div>';
		include('footer.php');
		exit;
	}
	while( !feof($fp)){
		$content .= fread($fp, 4096);
	}
	fclose($fp);
if(empty($content)) $info_msg = '<div class="alert alert-info">لیست در حال حاضر خالی است. برای هر خط یک کلمه وارد کن. (بدون هیچ نقطه گذاری).</div>';
}

function read_censored_words($filename) {
		$fp = @fopen($filename, "r");
		$content = '';
		if(!$fp) { 
			return 'فایل باز نشد.'.$file.'مطمئن شو که فایل آپلود شده و قابل نوشتن است یعنی مجوز آن 0777 باشد.';
			//include('footer.php');
			//exit;
		}
		while( !feof($fp)){
			$content .= fread($fp, 4096);
		}
		fclose($fp);
		return $content;
}

?>
<div id="adminPrimary">
    <div class="content">
	<h2>Abuse Prevention</h2>
<?php
if($info_msg) {
echo $info_msg; 
}
?>
	<div class="alert alert-info">سایت تان را بهینه سازی بکنید و رتبه سئویتان را به واسطه فیلتر کردن در هر لغات ناخواسته و زشت از نظرات مقالات و ویدئو ها به جایگاه خوبی برسانید.</div>
    <h2 class="sub-heading">لغات لیست یاه</h2>
    <div class="help-block">نظر دادن شامل هر کلمه موجود در لیست یاه بصورت اتوماتیک حذف خواهد شد.</div>
    <form name="form" method="post" action="blacklist.php?list=blacklist" class="form">
        <textarea name="words" class="span4" rows="5"><?php echo read_censored_words("../blacklist.txt"); ?></textarea>
        <input type="submit" name="Submit" value="Save" class="btn btn-success" />
    </form>
	<hr />
    <h2 class="sub-heading">لغات سانسور شده</h2>
    <div class="help-block">لغات سانسور شده جاگزین می شود با '***' اما بقیه نظر هنوز نشان داده می شود.</div>
    <form name="form" method="post" action="blacklist.php?list=censored" class="form">
        <textarea name="words" class="span4" rows="5"><?php echo read_censored_words("../censor_words.txt"); ?></textarea>
        <input type="submit" name="Submit" value="Save" class="btn btn-success" />
    </form>

    



    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>