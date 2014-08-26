<?php

 // FFmpeg Class By Seyed AmirHossein Tavousi .
 // Duration of Video :: Convert Video :: ScreenShot of Video
 // Tell Me :: Tamirtavoosi@yahoo.com :: 09381585940
 
 class ffmpeg {
	 
	 var $VIDEO_PATCH = '../uploads/videos/';
	 var $IMG_PATCH = '../uploads/thumbs/';
	 
	 public function Duration($FILENAME) {
	   $PATCH = $VIDEO_PATCH.$FILENAME;
	   ob_start();
	   passthru("ffmpeg.exe -i \"{$PATCH}\" 2>&1");
	   $duration = ob_get_contents();
	   ob_end_clean();
	   $search='/Duration: (.*?),/';
	   $duration=preg_match($search, $duration, $matches, PREG_OFFSET_CAPTURE, 3);
	   $dse4ee46er = $matches[1][0];
	   $hours = (int) substr($dse4ee46er, 0, 2);
	   $mins = (int) substr($dse4ee46er, 3, 2) + $hours * 60;
	   $secs = (int) substr($dse4ee46er, 6, 2) + $mins * 60;
	   $secs += ((int) substr($dse4ee46er, 9, 2)) / 100;
	   return $secs;
	 }
	 
	 public function Convert($FILENAME) {
	   $FILENAME_EXE = substr(strrchr($FILENAME, "."), 1);
	   $FILENAME_EXE_STR_REPLACE = str_replace('.'.$FILENAME_EXE,'',$FILENAME);
	   $PATCH = $VIDEO_PATCH;
       exec('ffmpeg.exe -i '.$PATCH.$FILENAME.' -s 1280x720 '.$PATCH.'MP4_'.$FILENAME_EXE_STR_REPLACE.'.mp4');
	 }
	 
	 public function ScreenShot($FILENAME,$NN) {
	   $FILENAME_EXE = substr(strrchr($FILENAME, "."), 1);
	   $FILENAME_STR = 'MP4_'.str_replace('.'.$FILENAME_EXE,'',$FILENAME).'.mp4';
	   $VIDEO_PATCH = $VIDEO_PATCH;
	   $IMG_PATCH = $IMG_PATCH;
	   if($NN == 'false') {
	     $IMG_NAME = base64_encode($FILENAME).'_thumb.jpg';
	   } else {
	     $IMG_NAME = base64_encode($FILENAME_STR).'_thumb.jpg';
	   }
       exec('ffmpeg.exe -i '.$VIDEO_PATCH.$FILENAME.' -deinterlace -an -ss 2 -f mjpeg -t 1 -r 1 -y -s 660x370 '.$IMG_PATCH.$IMG_NAME.' 2>&1');
	 }
 }
 
?>