-- phpMyAdmin SQL Dump
-- version 4.0.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2014 at 02:21 PM
-- Server version: 5.5.31-log
-- PHP Version: 5.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `exe.ir`
--

-- --------------------------------------------------------

--
-- Table structure for table `art_articles`
--

CREATE TABLE IF NOT EXISTS `art_articles` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT '',
  `status` smallint(3) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL DEFAULT '0',
  `author` int(5) NOT NULL DEFAULT '0',
  `allow_comments` enum('0','1') NOT NULL DEFAULT '1',
  `comment_count` int(7) NOT NULL DEFAULT '0',
  `views` int(8) unsigned NOT NULL DEFAULT '0',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `restricted` enum('0','1') NOT NULL DEFAULT '0',
  `article_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `art_articles`
--

INSERT INTO `art_articles` (`id`, `title`, `content`, `category`, `status`, `date`, `author`, `allow_comments`, `comment_count`, `views`, `featured`, `restricted`, `article_slug`) VALUES
(1, 'اسکریپت فارسی رتبه دهی و سایت های برتر', '<p><span>در این مطلب قصد داریم اسکریپت فارسی رتبه دهی به وب سایت ها را به شما معرفی کنیم. با نصب این اسکریپت قادر هستید یک سیستم &ldquo;سایت برتر&rdquo; یا Top site راه اندازی کنید. کاربران در این سیستم قادر هستند اقدام به ثبت وب سایت خود کنند یا به دیگر وب سایت ها امتیاز دهند. از قابلیت های کلیدی این سیستم می توان به فارسی بودن ، قابلیت ثبت امتیاز مثبت یا منفی اشاره کرد.</span></p>\r\n<p><a href="/uploads/articles/a661ab80.jpg" rel="prettyPhoto[phpmelody]"><img src="/uploads/articles/a661ab80.jpg" alt="" width="180" height="180" border="0" hspace="" vspace="" /></a></p>', '1', 1, 1397933422, 1, '1', 0, 41, '0', '0', 'yrtyrty');

-- --------------------------------------------------------

--
-- Table structure for table `art_categories`
--

CREATE TABLE IF NOT EXISTS `art_categories` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `parent_id` int(3) NOT NULL DEFAULT '0',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `published_articles` int(7) unsigned NOT NULL DEFAULT '0',
  `total_articles` int(7) NOT NULL DEFAULT '0',
  `position` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `meta_tags` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `art_categories`
--

INSERT INTO `art_categories` (`id`, `parent_id`, `tag`, `name`, `published_articles`, `total_articles`, `position`, `description`, `meta_tags`) VALUES
(1, 0, 'news', 'اخبار سایت', 1, 1, 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `art_tags`
--

CREATE TABLE IF NOT EXISTS `art_tags` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `article_id` int(6) NOT NULL DEFAULT '0',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `safe_tag` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mm_plugins`
--

CREATE TABLE IF NOT EXISTS `mm_plugins` (
  `plugin` varchar(30) NOT NULL,
  `plugin_name` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `backend_only` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`plugin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mm_settings`
--

CREATE TABLE IF NOT EXISTS `mm_settings` (
  `plugin` varchar(30) NOT NULL,
  `setting` varchar(60) NOT NULL,
  `value` varchar(300) NOT NULL,
  `editable` tinyint(1) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `valid` varchar(40) DEFAULT NULL,
  UNIQUE KEY `plugin` (`plugin`,`setting`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_activity`
--

CREATE TABLE IF NOT EXISTS `pm_activity` (
  `activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `object_type` varchar(50) NOT NULL,
  `target_id` int(10) unsigned NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `hide` enum('0','1') NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `activity_type` (`activity_type`),
  KEY `hide` (`hide`),
  KEY `objects` (`object_id`,`object_type`),
  KEY `targets` (`target_id`,`target_type`),
  KEY `user_id` (`user_id`,`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Dumping data for table `pm_activity`
--

INSERT INTO `pm_activity` (`activity_id`, `user_id`, `activity_type`, `time`, `object_id`, `object_type`, `target_id`, `target_type`, `hide`, `metadata`) VALUES
(2, 1, 'favorite', 1397893359, 17, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"ffc93ef3b";s:11:"video_title";s:21:"پلی استیشن 4";s:10:"video_slug";s:57:"%d9%be%d9%84%db%8c-%d8%a7%d8%b3%d8%aa%db%8c%d8%b4%d9%86-4";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:15:"ffc93ef3b-1.jpg";s:9:"submitted";s:5:"admin";}}'),
(5, 1, 'upload-video', 1398000411, 24, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"674f3bdf9";s:11:"video_title";s:8:"tytrytry";s:10:"video_slug";N;s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:0:"";s:9:"submitted";s:5:"admin";}}'),
(6, 1, 'favorite', 1398002019, 18, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"260efb9c2";s:11:"video_title";s:37:"ترانه زیبا امام زمان";s:10:"video_slug";s:105:"%d8%aa%d8%b1%d8%a7%d9%86%d9%87-%d8%b2%db%8c%d8%a8%d8%a7-%d8%a7%d9%85%d8%a7%d9%85-%d8%b2%d9%85%d8%a7%d9%86";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:15:"260efb9c2-1.jpg";s:9:"submitted";s:5:"admin";}}'),
(9, 1, 'comment', 1398053840, 3, 'comment', 1, 'article', '0', 'a:1:{s:6:"target";a:3:{s:2:"id";i:1;s:5:"title";s:69:"اسکریپت فارسی رتبه دهی و سایت های برتر";s:12:"article_slug";s:7:"yrtyrty";}}'),
(10, 1, 'upload-video', 1398056164, 25, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"17c6ca60d";s:11:"video_title";s:32:"تبلیغ فیلم 2 اسلحه";s:10:"video_slug";N;s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:0:"";s:9:"submitted";s:5:"admin";}}'),
(14, 1, 'upload-video', 1398059000, 29, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"05bafd33e";s:11:"video_title";s:4:"dfdf";s:10:"video_slug";N;s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:0:"";s:9:"submitted";s:5:"admin";}}'),
(16, 1, 'upload-video', 1398060240, 31, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"f1368c180";s:11:"video_title";s:41:"تبلیغ فیلم داستان پلیس";s:10:"video_slug";N;s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0X2YxYzIzNmY2ZGQubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(17, 1, 'upload-video', 1398066538, 32, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"2a0493209";s:11:"video_title";s:31:"مستند دایناسورها";s:10:"video_slug";N;s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0X2I3OWExMjczZmYubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(18, 1, 'upload-video', 1398067613, 33, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"b05dff370";s:11:"video_title";s:3:"n/a";s:10:"video_slug";N;s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0XzE5MjlkN2YzNTkubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(20, 1, 'status', 1398078433, 0, '', 0, '', '0', 'a:1:{s:10:"statustext";s:55:"عجب روز شلوغیه در پارسیان کلیپ";}'),
(21, 1, 'like', 1398090858, 34, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"ec520b6a2";s:11:"video_title";s:30:"تبلیغ فیلم هرکول";s:10:"video_slug";s:86:"%d8%aa%d8%a8%d9%84%db%8c%d8%ba-%d9%81%db%8c%d9%84%d9%85-%d9%87%d8%b1%da%a9%d9%88%d9%84";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0X2JjZDg3ZjliOTIubXA0_thumb.jpg";s:9:"submitted";s:7:"tavousi";}}'),
(22, 1, 'like', 1398099100, 18, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"260efb9c2";s:11:"video_title";s:37:"ترانه زیبا امام زمان";s:10:"video_slug";s:105:"%d8%aa%d8%b1%d8%a7%d9%86%d9%87-%d8%b2%db%8c%d8%a8%d8%a7-%d8%a7%d9%85%d8%a7%d9%85-%d8%b2%d9%85%d8%a7%d9%86";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0Xzk0N2I1ODFiLm1wNA==_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(23, 1, 'read', 1401023884, 1, 'article', 0, '', '0', 'a:1:{s:6:"object";a:3:{s:2:"id";s:1:"1";s:5:"title";s:69:"اسکریپت فارسی رتبه دهی و سایت های برتر";s:12:"article_slug";s:7:"yrtyrty";}}'),
(24, 1, 'watch', 1401261408, 34, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"ec520b6a2";s:11:"video_title";s:30:"تبلیغ فیلم هرکول";s:10:"video_slug";s:86:"%d8%aa%d8%a8%d9%84%db%8c%d8%ba-%d9%81%db%8c%d9%84%d9%85-%d9%87%d8%b1%da%a9%d9%88%d9%84";s:8:"duration";s:5:"03:09";s:8:"yt_thumb";s:34:"TVA0X2JjZDg3ZjliOTIubXA0_thumb.jpg";s:9:"submitted";s:7:"tavousi";}}'),
(25, 1, 'comment', 1401261419, 4, 'comment', 34, 'video', '0', 'a:1:{s:6:"target";a:6:{s:7:"uniq_id";s:9:"ec520b6a2";s:11:"video_title";s:30:"تبلیغ فیلم هرکول";s:10:"video_slug";s:86:"%d8%aa%d8%a8%d9%84%db%8c%d8%ba-%d9%81%db%8c%d9%84%d9%85-%d9%87%d8%b1%da%a9%d9%88%d9%84";s:8:"duration";s:5:"03:09";s:8:"yt_thumb";s:34:"TVA0X2JjZDg3ZjliOTIubXA0_thumb.jpg";s:9:"submitted";s:7:"tavousi";}}'),
(35, 1, 'watch', 1401276349, 31, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"f1368c180";s:11:"video_title";s:41:"تبلیغ فیلم داستان پلیس";s:10:"video_slug";s:117:"%d8%aa%d8%a8%d9%84%db%8c%d8%ba-%d9%81%db%8c%d9%84%d9%85-%d8%af%d8%a7%d8%b3%d8%aa%d8%a7%d9%86-%d9%be%d9%84%db%8c%d8%b3";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0X2YxYzIzNmY2ZGQubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(31, 1, 'comment', 1401276227, 5, 'comment', 18, 'video', '0', 'a:1:{s:6:"target";a:6:{s:7:"uniq_id";s:9:"260efb9c2";s:11:"video_title";s:37:"ترانه زیبا امام زمان";s:10:"video_slug";s:105:"%d8%aa%d8%b1%d8%a7%d9%86%d9%87-%d8%b2%db%8c%d8%a8%d8%a7-%d8%a7%d9%85%d8%a7%d9%85-%d8%b2%d9%85%d8%a7%d9%86";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0Xzk0N2I1ODFiLm1wNA==_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(36, 1, 'comment', 1401276361, 11, 'comment', 31, 'video', '0', 'a:1:{s:6:"target";a:6:{s:7:"uniq_id";s:9:"f1368c180";s:11:"video_title";s:41:"تبلیغ فیلم داستان پلیس";s:10:"video_slug";s:117:"%d8%aa%d8%a8%d9%84%db%8c%d8%ba-%d9%81%db%8c%d9%84%d9%85-%d8%af%d8%a7%d8%b3%d8%aa%d8%a7%d9%86-%d9%be%d9%84%db%8c%d8%b3";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0X2YxYzIzNmY2ZGQubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(37, 1, 'watch', 1403592782, 8, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"afb33f3a9";s:11:"video_title";s:80:"ترانه زیبای رقیب از شهرام شپره و شهرام سولتی";s:10:"video_slug";s:200:"%d8%aa%d8%b1%d8%a7%d9%86%d9%87-%d8%b2%db%8c%d8%a8%d8%a7%db%8c-%d8%b1%d9%82%db%8c%d8%a8-%d8%a7%d8%b2-%d8%b4%d9%87%d8%b1%d8%a7%d9%85-%d8%b4%d9%be%d8%b1%d9%87-%d9%88-%d8%b4%d9%87%d8%b1%d8%a7%d9%85-%d8%b3";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:26:"YmY4MGFlOGUubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(38, 1, 'favorite', 1403592809, 8, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"afb33f3a9";s:11:"video_title";s:80:"ترانه زیبای رقیب از شهرام شپره و شهرام سولتی";s:10:"video_slug";s:200:"%d8%aa%d8%b1%d8%a7%d9%86%d9%87-%d8%b2%db%8c%d8%a8%d8%a7%db%8c-%d8%b1%d9%82%db%8c%d8%a8-%d8%a7%d8%b2-%d8%b4%d9%87%d8%b1%d8%a7%d9%85-%d8%b4%d9%be%d8%b1%d9%87-%d9%88-%d8%b4%d9%87%d8%b1%d8%a7%d9%85-%d8%b3";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:26:"YmY4MGFlOGUubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(39, 1, 'watch', 1403975994, 21, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"92d321340";s:11:"video_title";s:56:"زری زری می بده با ساز و اواز بده";s:10:"video_slug";s:24:"78347497-mp4-h264-aac-hq";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0X2Q3NWQ3MmIxLm1wNA==_thumb.jpg";s:9:"submitted";s:5:"admin";}}'),
(41, 1, 'watch', 1404275025, 31, 'video', 0, '', '0', 'a:1:{s:6:"object";a:6:{s:7:"uniq_id";s:9:"f1368c180";s:11:"video_title";s:41:"تبلیغ فیلم داستان پلیس";s:10:"video_slug";s:117:"%d8%aa%d8%a8%d9%84%db%8c%d8%ba-%d9%81%db%8c%d9%84%d9%85-%d8%af%d8%a7%d8%b3%d8%aa%d8%a7%d9%86-%d9%be%d9%84%db%8c%d8%b3";s:8:"duration";s:5:"00:00";s:8:"yt_thumb";s:34:"TVA0X2YxYzIzNmY2ZGQubXA0_thumb.jpg";s:9:"submitted";s:5:"admin";}}');

-- --------------------------------------------------------

--
-- Table structure for table `pm_ads`
--

CREATE TABLE IF NOT EXISTS `pm_ads` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `position` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `code` text NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `disable_stats` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `pm_ads`
--

INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES
(1, 'Header', 'Appears on all pages right under the horizontal menu', '', '0', '0'),
(2, 'Footer', 'Appears on all pages right before the footer', 'gggggggggggggggggg', '0', '0'),
(3, 'تبلیغ صفحه نمایش ویدیو', 'Appears on video pages under the video player. (Recommended max. width: 540px)', '<div id="pm-featured" class="border-radius3">\r\n<h2>\r\n<div style="\r\n    background-color: #477E84;\r\n    color: #fff;\r\n    margin: 0 auto;\r\n    width: 80px;\r\n    margin-bottom: -5px;\r\n    margin-right: 0px;\r\n"><center>\r\nتبلیغات\r\n</center></div>\r\n</h2>\r\n</div>\r\n<script type="text/javascript">\r\n var anetwork_pram = anetwork_pram || [];\r\n anetwork_pram["aduser"] = "1386603014";\r\n anetwork_pram["adheight"] = "250";\r\n anetwork_pram["adwidth"] = "300";\r\n </script><script src="http://static-cdn.anetwork.ir/showad/pub.js" type="text/javascript"></script>', '1', '0'),
(4, 'تبلیغ وبلاگ سایت', 'Appears at the end of all articles (Recommended max. width: 540px)', '<div id="pm-featured" class="border-radius3">\r\n<h2>\r\n<div style="\r\n    background-color: #477E84;\r\n    color: #fff;\r\n    margin: 0 auto;\r\n    width: 80px;\r\n    margin-bottom: -5px;\r\n    margin-right: 0px;\r\n"><center>\r\nتبلیغات\r\n</center></div>\r\n</h2>\r\n</div>\r\n<script type="text/javascript">\r\n var anetwork_pram = anetwork_pram || [];\r\n anetwork_pram["aduser"] = "1386603014";\r\n anetwork_pram["adheight"] = "250";\r\n anetwork_pram["adwidth"] = "300";\r\n </script><script src="http://static-cdn.anetwork.ir/showad/pub.js" type="text/javascript"></script>', '1', '0'),
(5, 'تبلیغ صفحه اصلی', 'Appears as the first widget block on the right site of your homepage (Recommended max. width: 250px)', '<div id="pm-featured" class="border-radius3">\r\n<h2>\r\n<div style="\r\n    background-color: #477E84;\r\n    color: #fff;\r\n    margin: 0 auto;\r\n    width: 80px;\r\n    margin-bottom: -5px;\r\n    margin-right: 0px;\r\n">\r\nتبلیغات\r\n</div>\r\n</h2>\r\n</div>\r\n<script type="text/javascript">\r\n var anetwork_pram = anetwork_pram || [];\r\n anetwork_pram["aduser"] = "1386603014";\r\n anetwork_pram["adheight"] = "250";\r\n anetwork_pram["adwidth"] = "300";\r\n </script><script src="http://static-cdn.anetwork.ir/showad/pub.js" type="text/javascript"></script>', '1', '0'),
(6, 'Floating Skyscraper (Left)', 'Appears on the left side of the page continer', '<script type="text/javascript">\r\n var anetwork_pram = anetwork_pram || [];\r\n anetwork_pram["aduser"] = "1386603014";\r\n anetwork_pram["adheight"] = "240";\r\n anetwork_pram["adwidth"] = "120";\r\n </script><script src="http://static-cdn.anetwork.ir/showad/pub.js" type="text/javascript"></script>', '0', '0'),
(7, 'Floating Skyscraper (Right)', 'Appears on the right side of the page continer', '<script type="text/javascript">\r\n var anetwork_pram = anetwork_pram || [];\r\n anetwork_pram["aduser"] = "1386603014";\r\n anetwork_pram["adheight"] = "240";\r\n anetwork_pram["adwidth"] = "120";\r\n </script><script src="http://static-cdn.anetwork.ir/showad/pub.js" type="text/javascript"></script>', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `pm_ads_log`
--

CREATE TABLE IF NOT EXISTS `pm_ads_log` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `ad_id` mediumint(6) NOT NULL,
  `ad_type` smallint(2) NOT NULL,
  `impressions` int(11) unsigned NOT NULL DEFAULT '0',
  `clicks` int(11) unsigned NOT NULL DEFAULT '0',
  `skips` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `date` (`date`,`ad_id`,`ad_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

--
-- Dumping data for table `pm_ads_log`
--

INSERT INTO `pm_ads_log` (`log_id`, `date`, `ad_id`, `ad_type`, `impressions`, `clicks`, `skips`) VALUES
(1, '2014-04-18', 5, 1, 86, 0, 0),
(2, '2014-04-19', 5, 1, 137, 0, 0),
(3, '2014-04-19', 7, 1, 8, 0, 0),
(4, '2014-04-19', 6, 1, 7, 0, 0),
(5, '2014-04-20', 5, 1, 160, 0, 0),
(6, '2014-04-20', 3, 1, 44, 0, 0),
(7, '2014-04-20', 1, 3, 29, 0, 8),
(8, '2014-04-20', 4, 1, 7, 0, 0),
(9, '2014-04-20', 2, 1, 1, 0, 0),
(10, '2014-04-21', 1, 3, 31, 0, 13),
(11, '2014-04-21', 5, 1, 83, 0, 0),
(12, '2014-04-21', 3, 1, 23, 0, 0),
(13, '2014-04-22', 5, 1, 33, 0, 0),
(14, '2014-04-22', 3, 1, 31, 0, 0),
(15, '2014-04-23', 3, 1, 6, 0, 0),
(16, '2014-04-23', 5, 1, 101, 0, 0),
(17, '2014-04-23', 4, 1, 1, 0, 0),
(18, '2014-04-24', 5, 1, 2, 0, 0),
(19, '2014-04-25', 5, 1, 9, 0, 0),
(20, '2014-04-26', 5, 1, 6, 0, 0),
(21, '2014-04-26', 3, 1, 3, 0, 0),
(22, '2014-04-27', 5, 1, 2, 0, 0),
(23, '2014-04-27', 3, 1, 4, 0, 0),
(24, '2014-04-28', 3, 1, 10, 0, 0),
(25, '2014-04-28', 5, 1, 17, 0, 0),
(26, '2014-04-29', 5, 1, 8, 0, 0),
(27, '2014-05-02', 5, 1, 3, 0, 0),
(28, '2014-05-04', 5, 1, 1, 0, 0),
(29, '2014-05-24', 5, 1, 34, 0, 0),
(30, '2014-05-24', 3, 1, 24, 0, 0),
(31, '2014-05-24', 4, 1, 10, 0, 0),
(32, '2014-05-25', 5, 1, 22, 0, 0),
(33, '2014-05-25', 4, 1, 3, 0, 0),
(34, '2014-05-25', 3, 1, 25, 0, 0),
(35, '2014-05-26', 5, 1, 3, 0, 0),
(36, '2014-05-26', 3, 1, 1, 0, 0),
(37, '2014-05-08', 5, 1, 1, 0, 0),
(38, '2014-05-28', 5, 1, 10, 0, 0),
(39, '2014-05-28', 3, 1, 23, 0, 0),
(40, '2014-05-28', 4, 1, 3, 0, 0),
(41, '2014-05-29', 5, 1, 7, 0, 0),
(42, '2014-05-29', 3, 1, 12, 0, 0),
(43, '2014-06-06', 5, 1, 12, 0, 0),
(44, '2014-06-06', 3, 1, 1, 0, 0),
(45, '2014-06-13', 5, 1, 1, 0, 0),
(46, '2014-06-23', 5, 1, 7, 0, 0),
(47, '2014-06-23', 3, 1, 4, 0, 0),
(48, '2014-06-24', 5, 1, 14, 0, 0),
(49, '2014-06-24', 3, 1, 5, 0, 0),
(50, '2014-06-28', 5, 1, 20, 0, 0),
(51, '2014-06-28', 3, 1, 10, 0, 0),
(52, '2014-06-28', 4, 1, 13, 0, 0),
(53, '2014-06-29', 5, 1, 5, 0, 0),
(54, '2014-06-29', 3, 1, 1, 0, 0),
(55, '2014-06-30', 5, 1, 2, 0, 0),
(56, '2014-06-30', 3, 1, 3, 0, 0),
(57, '2014-07-01', 5, 1, 6, 0, 0),
(58, '2014-07-01', 3, 1, 4, 0, 0),
(59, '2014-07-01', 4, 1, 1, 0, 0),
(60, '2014-07-02', 5, 1, 24, 0, 0),
(61, '2014-07-02', 3, 1, 1, 0, 0),
(62, '2014-07-02', 4, 1, 1, 0, 0),
(63, '2014-07-03', 5, 1, 5, 0, 0),
(64, '2014-07-03', 3, 1, 1, 0, 0),
(65, '2014-07-04', 5, 1, 1, 0, 0),
(66, '2014-07-07', 5, 1, 6, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pm_banlist`
--

CREATE TABLE IF NOT EXISTS `pm_banlist` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) unsigned NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_bin_rating_meta`
--

CREATE TABLE IF NOT EXISTS `pm_bin_rating_meta` (
  `vote_meta_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `up_vote_count` int(11) NOT NULL DEFAULT '0',
  `down_vote_count` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_meta_id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `score` (`score`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `pm_bin_rating_meta`
--

INSERT INTO `pm_bin_rating_meta` (`vote_meta_id`, `uniq_id`, `up_vote_count`, `down_vote_count`, `score`) VALUES
(10, '260efb9c2', 2, 0, 2),
(9, 'ec520b6a2', 1, 0, 1),
(11, '05bafd33e', 0, 1, -1);

-- --------------------------------------------------------

--
-- Table structure for table `pm_bin_rating_votes`
--

CREATE TABLE IF NOT EXISTS `pm_bin_rating_votes` (
  `vote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `vote_value` tinyint(1) NOT NULL DEFAULT '0',
  `vote_ip` varchar(20) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  KEY `uniq_id` (`uniq_id`,`vote_ip`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `pm_bin_rating_votes`
--

INSERT INTO `pm_bin_rating_votes` (`vote_id`, `uniq_id`, `vote_value`, `vote_ip`, `user_id`, `date`) VALUES
(4, '260efb9c2', 1, '127.0.0.1', 1, 1398099100),
(3, 'ec520b6a2', 1, '127.0.0.1', 1, 1398090858),
(5, '260efb9c2', 1, '127.0.0.1', 0, 1404192529),
(6, '05bafd33e', 0, '127.0.0.1', 0, 1404284630);

-- --------------------------------------------------------

--
-- Table structure for table `pm_categories`
--

CREATE TABLE IF NOT EXISTS `pm_categories` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `published_videos` int(7) unsigned NOT NULL DEFAULT '0',
  `total_videos` int(7) NOT NULL DEFAULT '0',
  `position` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `meta_tags` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `pm_categories`
--

INSERT INTO `pm_categories` (`id`, `parent_id`, `tag`, `name`, `published_videos`, `total_videos`, `position`, `description`, `meta_tags`) VALUES
(33, 31, 'Documentary-tak-part', 'مستندهای یک قسمتی', 0, 0, 2, '', ''),
(35, 33, 'Documentary-en', 'مستندهای خارجی', 0, 0, 2, '', ''),
(34, 33, 'Documentary-irani', 'مستندهای ایرانی', 0, 0, 1, '', ''),
(31, 0, 'Documentary', 'مستند', 0, 0, 1, '', ''),
(36, 32, 'Serials-Documentary-irani', 'مستندهای ایرانی', 0, 0, 1, '', ''),
(37, 32, 'Documentary-en-Serials', 'مستندهای خارجی', 0, 0, 2, '', ''),
(32, 31, 'Documentary-Serials', 'مستندهای سریالی', 0, 0, 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `pm_chart`
--

CREATE TABLE IF NOT EXISTS `pm_chart` (
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `views` int(9) unsigned NOT NULL DEFAULT '0',
  `views_this` int(6) NOT NULL DEFAULT '0',
  `views_last` int(6) NOT NULL DEFAULT '0',
  `views_seclast` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uniq_id`),
  KEY `views` (`views`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_comments`
--

CREATE TABLE IF NOT EXISTS `pm_comments` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(50) DEFAULT NULL,
  `username` varchar(100) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `added` int(10) unsigned NOT NULL DEFAULT '0',
  `user_ip` varchar(20) NOT NULL DEFAULT '',
  `user_id` mediumint(7) NOT NULL DEFAULT '0',
  `approved` enum('0','1') NOT NULL DEFAULT '0',
  `up_vote_count` int(10) unsigned NOT NULL DEFAULT '0',
  `down_vote_count` int(10) unsigned NOT NULL DEFAULT '0',
  `score` int(10) NOT NULL DEFAULT '0',
  `report_count` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `score` (`score`),
  KEY `report_count` (`report_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_comments_reported`
--

CREATE TABLE IF NOT EXISTS `pm_comments_reported` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `comment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`comment_id`),
  KEY `user_id_2` (`user_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_config`
--

CREATE TABLE IF NOT EXISTS `pm_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=114 ;

--
-- Dumping data for table `pm_config`
--

INSERT INTO `pm_config` (`id`, `name`, `value`) VALUES
(1, 'contact_mail', ''),
(2, 'thumb_from', '1'),
(3, 'browse_page', '12'),
(4, 'browse_articles', '5'),
(5, 'player_w', '659'),
(6, 'player_h', '370'),
(7, 'player_w_index', '659'),
(8, 'player_h_index', '370'),
(9, 'player_w_favs', '659'),
(10, 'player_h_favs', '370'),
(11, 'player_w_embed', '659'),
(12, 'player_h_embed', '370'),
(13, 'isnew_days', '7'),
(14, 'ispopular', '100'),
(15, 'stopbadcomments', '1'),
(16, 'comments_page', '10'),
(17, 'template_f', 'default'),
(18, 'firstinstall', 'n'),
(19, 'counterhtml', ''),
(20, 'voth_cat', '0'),
(21, 'views_from', '2'),
(22, 'fav_limit', '20'),
(23, 'version', '2.1'),
(24, 'seomod', '1'),
(25, 'new_videos', '12'),
(26, 'top_videos', '10'),
(27, 'chart_days', '7'),
(28, 'chart_last_reset', '1403975936'),
(29, 'guests_can_comment', '1'),
(30, 'comm_moderation_level', '0'),
(31, 'show_tags', '1'),
(32, 'shuffle_tags', '0'),
(33, 'tag_cloud_limit', '50'),
(34, 'show_stats', '1'),
(35, 'account_activation', '1'),
(36, 'issmtp', '1'),
(37, 'player_timecolor', '545454'),
(38, 'player_bgcolor', '5e5e5e'),
(39, 'player_autoplay', '0'),
(40, 'player_autobuff', '0'),
(41, 'player_watermarkurl', ''),
(42, 'player_watermarklink', ''),
(43, 'player_watermarkshow', 'fullscreen'),
(44, 'search_suggest', '1'),
(45, 'use_hq_vids', '1'),
(46, 'total_videoads', '0'),
(47, 'videoads_delay', '20'),
(48, 'default_lang', '1'),
(49, 'last_video', ''),
(50, 'top_videos_sort', 'views'),
(51, 'video_player', 'main'),
(52, 'gzip', '1'),
(53, 'mod_article', '1'),
(54, 'mail_server', 'mail.yourdomain.com'),
(55, 'mail_port', '25'),
(56, 'mail_user', 'user+yourdomain.com'),
(57, 'mail_pass', ''),
(58, 'show_ads', '0'),
(59, 'total_videos', '0'),
(60, 'total_articles', '1'),
(61, 'total_pages', '2'),
(62, 'homepage_title', 'پارسیان کلیپ'),
(63, 'homepage_description', ''),
(64, 'homepage_keywords', ''),
(65, 'moderator_can', 'manage_users:1;manage_comments:1;manage_videos:1;manage_articles:1;'),
(66, 'last_autosync', '1403537114'),
(67, 'allow_user_uploadvideo', '1'),
(68, 'allow_user_uploadvideo_bytes', '524288000'),
(69, 'jwplayerskin', 'modieus.zip'),
(70, 'video_sitemap_options', 'a:4:{s:14:"media_keywords";b:0;s:14:"media_category";b:0;s:12:"item_pubDate";b:0;s:10:"last_build";i:0;}'),
(71, 'auto_feature', '300'),
(72, 'bin_rating_allow_anon_voting', '1'),
(73, 'published_articles', '1'),
(74, 'published_videos', '0'),
(75, 'comment_default_sort', 'added'),
(76, 'comment_rating_hide_threshold', '3'),
(77, 'user_following_limit', '5000'),
(78, 'mod_social', '1'),
(79, 'activity_options', 'a:14:{s:6:"follow";i:1;s:8:"unfollow";i:1;s:5:"watch";i:1;s:4:"read";i:1;s:7:"comment";i:1;s:4:"like";i:1;s:7:"dislike";i:1;s:8:"favorite";i:1;s:10:"send-video";i:1;s:12:"upload-video";i:1;s:13:"suggest-video";i:1;s:4:"join";i:1;s:13:"update-avatar";i:1;s:6:"status";i:1;}'),
(80, 'pm_notifications_last_prune', '1403976276'),
(81, 'total_preroll_ads', '0'),
(82, 'preroll_ads_delay', '300'),
(83, 'default_tpl_customizations', 'YTowOnt9'),
(84, 'custom_logo_url', 'http://exe.ir/uploads/custom-logo.png'),
(85, 'article_widget_limit', '10'),
(86, 'new_page_limit', '50'),
(87, 'top_page_limit', '50'),
(88, 'allow_registration', '1'),
(89, 'allow_user_suggestvideo', '1'),
(90, 'maintenance_mode', '0'),
(91, 'maintenance_display_message', ''),
(92, 'thumb_video_w', '180'),
(93, 'thumb_video_h', '135'),
(94, 'thumb_article_w', '180'),
(95, 'thumb_article_h', '180'),
(96, 'thumb_avatar_w', '180'),
(97, 'thumb_avatar_h', '180'),
(98, 'allow_nonlatin_usernames', '1'),
(99, 'featured_autoplay', '0'),
(100, 'jwplayerkey', ''),
(101, 'auto-approve_suggested_videos', '0'),
(102, 'keyboard_shortcuts', '1'),
(103, 'show_addthis_widget', '0'),
(104, 'playingnow_limit', '9'),
(105, 'watch_related_limit', '10'),
(106, 'watch_toprated_limit', '10'),
(107, 'user_upload_daily_limit', '20'),
(108, 'spambot_prevention', 'securimage'),
(109, 'recaptcha_public_key', ''),
(110, 'recaptcha_private_key', ''),
(111, 'comment_system', 'on'),
(112, 'unread_system_messages', '0'),
(113, 'mm_framework', '1');

-- --------------------------------------------------------

--
-- Table structure for table `pm_countries`
--

CREATE TABLE IF NOT EXISTS `pm_countries` (
  `countryid` smallint(3) NOT NULL AUTO_INCREMENT,
  `country` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`countryid`),
  KEY `location` (`country`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=505 ;

--
-- Dumping data for table `pm_countries`
--

INSERT INTO `pm_countries` (`countryid`, `country`) VALUES
(500, 'USA'),
(184, 'Albania'),
(301, 'Algeria'),
(240, 'American Samoa'),
(241, 'Andorra'),
(302, 'Angola'),
(303, 'Anguilla'),
(304, 'Antigua'),
(115, 'Antilles'),
(305, 'Argentina'),
(185, 'Armenia'),
(306, 'Aruba'),
(307, 'Australia'),
(308, 'Austria'),
(186, 'Azerbaijan'),
(187, 'Azores'),
(309, 'Bahamas'),
(310, 'Bahrain'),
(311, 'Bangladesh'),
(312, 'Barbados'),
(313, 'Barbuda'),
(315, 'Belgium'),
(316, 'Belize'),
(314, 'Belorus'),
(317, 'Benin'),
(318, 'Bermuda'),
(319, 'Bhutan'),
(320, 'Bolivia'),
(321, 'Bonaire'),
(188, 'Bosnia-Hercegovina'),
(322, 'Botswana'),
(324, 'Br. Virgin Islands'),
(323, 'Brazil'),
(325, 'Brunei'),
(326, 'Bulgaria'),
(327, 'Burkina Faso'),
(328, 'Burundi'),
(189, 'Caicos Island'),
(329, 'Cameroon'),
(330, 'Canada'),
(190, 'Canary Islands'),
(331, 'Cape Verde'),
(332, 'Cayman Islands'),
(333, 'Central African Republic'),
(334, 'Chad'),
(335, 'Channel Islands'),
(336, 'Chile'),
(337, 'China'),
(338, 'Colombia'),
(191, 'Commonwealth of Ind'),
(339, 'Congo'),
(242, 'Cook Islands'),
(192, 'Cooper Island'),
(340, 'Costa Rica'),
(193, 'Cote D''Ivoire'),
(194, 'Croatia'),
(341, 'Curacao'),
(342, 'Cyprus'),
(343, 'Czech Republic'),
(344, 'Denmark'),
(345, 'Djibouti'),
(346, 'Dominica'),
(347, 'Dominican Republic'),
(348, 'Ecuador'),
(349, 'Egypt'),
(350, 'El Salvador'),
(351, 'England'),
(352, 'Equatorial Guinea'),
(353, 'Estonia'),
(354, 'Ethiopia'),
(355, 'Fiji'),
(356, 'Finland'),
(357, 'France'),
(358, 'French Guiana'),
(243, 'French Polynesia'),
(254, 'Futuna Island'),
(359, 'Gabon'),
(360, 'Gambia'),
(215, 'Georgia'),
(361, 'Germany'),
(362, 'Ghana'),
(216, 'Gibraltar'),
(363, 'Greece'),
(364, 'Grenada'),
(217, 'Grenland'),
(365, 'Guadeloupe'),
(366, 'Guam'),
(367, 'Guatemala'),
(368, 'Guinea'),
(369, 'Guinea-Bissau'),
(370, 'Guyana'),
(195, 'Haiti'),
(244, 'Holland'),
(371, 'Honduras'),
(372, 'Hong Kong'),
(373, 'Hungary'),
(374, 'Iceland'),
(375, 'India'),
(376, 'Indonesia'),
(377, 'Iran'),
(196, 'Iraq'),
(378, 'Ireland, Northern'),
(379, 'Ireland, Republic of'),
(197, 'Isle of Man'),
(380, 'Israel'),
(381, 'Italy'),
(382, 'Ivory Coast'),
(383, 'Jamaica'),
(384, 'Japan'),
(385, 'Jordan'),
(198, 'Jost Van Dyke Island'),
(218, 'Kampuchea'),
(199, 'Kazakhstan'),
(386, 'Kenya'),
(219, 'Kiribati'),
(239, 'Korea'),
(387, 'Korea, South'),
(256, 'Kosrae'),
(388, 'Kuwait'),
(200, 'Kyrgyzstan'),
(220, 'Laos'),
(389, 'Latvia'),
(390, 'Lebanon'),
(391, 'Lesotho'),
(221, 'Liberia'),
(392, 'Liechtenstein'),
(393, 'Lithuania'),
(394, 'Luxembourg'),
(395, 'Macau'),
(222, 'Macedonia'),
(396, 'Madagascar'),
(201, 'Madeira Islands'),
(202, 'Malagasy'),
(397, 'Malawi'),
(398, 'Malaysia'),
(399, 'Maldives'),
(100, 'Mali'),
(101, 'Malta'),
(102, 'Marshall Islands'),
(103, 'Martinique'),
(104, 'Mauritania'),
(105, 'Mauritius'),
(106, 'Mexico'),
(107, 'Micronesia'),
(203, 'Moldova'),
(108, 'Monaco'),
(223, 'Mongolia'),
(109, 'Montserrat'),
(110, 'Morocco'),
(111, 'Mozambique'),
(224, 'Myanmar'),
(112, 'Namibia'),
(225, 'Nauru'),
(113, 'Nepal'),
(114, 'Netherlands'),
(204, 'Nevis'),
(246, 'Nevis (St. Kitts)'),
(116, 'New Caledonia'),
(117, 'New Zealand'),
(118, 'Nicaragua'),
(119, 'Niger'),
(120, 'Nigeria'),
(226, 'Niue'),
(258, 'Norfolk Island'),
(205, 'Norman Island'),
(257, 'Northern Mariana Island'),
(121, 'Norway'),
(122, 'Oman'),
(123, 'Pakistan'),
(124, 'Palau'),
(125, 'Panama'),
(126, 'Papua New Guinea'),
(127, 'Paraguay'),
(128, 'Peru'),
(129, 'Philippines'),
(130, 'Poland'),
(260, 'Ponape'),
(131, 'Portugal'),
(132, 'Qatar'),
(133, 'Reunion'),
(134, 'Romania'),
(261, 'Rota'),
(135, 'Russia'),
(136, 'Rwanda'),
(137, 'Saba'),
(147, 'Saipan'),
(228, 'San Marino'),
(229, 'Sao Tome'),
(148, 'Saudi Arabia'),
(149, 'Scotland'),
(150, 'Senegal'),
(207, 'Serbia'),
(151, 'Seychelles'),
(152, 'Sierra Leone'),
(153, 'Singapore'),
(208, 'Slovakia'),
(209, 'Slovenia'),
(210, 'Solomon Islands'),
(154, 'Somalia'),
(155, 'South Africa'),
(156, 'Spain'),
(157, 'Sri Lanka'),
(138, 'St. Barthelemy'),
(206, 'St. Christopher'),
(139, 'St. Croix'),
(140, 'St. Eustatius'),
(141, 'St. John'),
(142, 'St. Kitts'),
(143, 'St. Lucia'),
(144, 'St. Maarten'),
(245, 'St. Martin'),
(145, 'St. Thomas'),
(146, 'St. Vincent'),
(158, 'Sudan'),
(159, 'Suriname'),
(160, 'Swaziland'),
(161, 'Sweden'),
(162, 'Switzerland'),
(163, 'Syria'),
(247, 'Tahiti'),
(164, 'Taiwan'),
(211, 'Tajikistan'),
(165, 'Tanzania'),
(166, 'Thailand'),
(248, 'Tinian'),
(167, 'Togo'),
(230, 'Tonaga'),
(249, 'Tonga'),
(250, 'Tortola'),
(168, 'Trinidad and Tobago'),
(251, 'Truk'),
(169, 'Tunisia'),
(170, 'Turkey'),
(212, 'Turkmenistan'),
(171, 'Turks and Caicos Island'),
(231, 'Tuvalu'),
(175, 'U.S. Virgin Islands'),
(172, 'Uganda'),
(173, 'Ukraine'),
(252, 'Union Island'),
(174, 'United Arab Emirates'),
(176, 'Uruguay'),
(262, 'United Kingdom'),
(232, 'Uzbekistan'),
(233, 'Vanuatu'),
(177, 'Vatican City'),
(178, 'Venezuela'),
(234, 'Vietnam'),
(235, 'Virgin Islands (Brit'),
(236, 'Virgin Islands (U.S.'),
(237, 'Wake Island'),
(179, 'Wales'),
(253, 'Wallis Island'),
(238, 'Western Samoa'),
(255, 'Yap'),
(180, 'Yemen, Republic of'),
(213, 'Yugoslavia'),
(181, 'Zaire'),
(182, 'Zambia'),
(183, 'Zimbabwe'),
(501, 'Kosova'),
(502, 'Afghanistan'),
(503, 'Libya'),
(504, 'Eritrea');

-- --------------------------------------------------------

--
-- Table structure for table `pm_embed_code`
--

CREATE TABLE IF NOT EXISTS `pm_embed_code` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `embed_code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pm_embed_code`
--

INSERT INTO `pm_embed_code` (`id`, `uniq_id`, `embed_code`) VALUES
(1, '8ec72618b', 'a:5:{s:8:"provider";s:0:"";s:10:"startparam";s:0:"";s:11:"loadbalance";s:0:"";s:9:"subscribe";s:0:"";s:11:"securetoken";s:0:"";}');

-- --------------------------------------------------------

--
-- Table structure for table `pm_favorites`
--

CREATE TABLE IF NOT EXISTS `pm_favorites` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL DEFAULT '0',
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`uniq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_log`
--

CREATE TABLE IF NOT EXISTS `pm_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `log_msg` text NOT NULL,
  `area` varchar(50) NOT NULL DEFAULT '',
  `added` int(11) NOT NULL DEFAULT '0',
  `msg_type` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `area` (`area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pm_log`
--

INSERT INTO `pm_log` (`id`, `log_msg`, `area`, `added`, `msg_type`) VALUES
(1, 'SMTP Error: Could not connect to SMTP host. Check your details again and contact your hosting provider if in doubt.', 'Register Page', 1398002755, '0');

-- --------------------------------------------------------

--
-- Table structure for table `pm_meta`
--

CREATE TABLE IF NOT EXISTS `pm_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `item_type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`item_type`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `pm_meta`
--

INSERT INTO `pm_meta` (`id`, `item_id`, `item_type`, `meta_key`, `meta_value`) VALUES
(4, 1, 3, '_meta_keywords', ''),
(5, 1, 3, '_meta_description', ''),
(6, 2, 3, '_meta_keywords', ''),
(7, 2, 3, '_meta_description', ''),
(8, 1, 2, '_post_thumb', 'a661ab80_th.jpg'),
(9, 1, 2, '_post_image', 'a661ab80.jpg'),
(10, 1, 2, '_post_thumb_show', 'a661ab80_th.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pm_notifications`
--

CREATE TABLE IF NOT EXISTS `pm_notifications` (
  `notification_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `to_user_id` int(10) unsigned NOT NULL,
  `from_user_id` int(10) unsigned NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `seen` enum('0','1') NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `to_user_id` (`to_user_id`,`seen`),
  KEY `activity_type` (`activity_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_pages`
--

CREATE TABLE IF NOT EXISTS `pm_pages` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `author` int(5) NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `status` smallint(3) NOT NULL DEFAULT '0',
  `page_name` varchar(255) NOT NULL DEFAULT '',
  `views` int(8) unsigned NOT NULL DEFAULT '0',
  `showinmenu` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pm_pages`
--

INSERT INTO `pm_pages` (`id`, `title`, `content`, `author`, `date`, `status`, `page_name`, `views`, `showinmenu`) VALUES
(1, 'Terms of Agreement', '<h2>Code of Conduct</h2>\r\n<p>In using this Service, you must behave in a civil and respectful manner at all times. Further, you will not:</p>\r\n<ul>\r\n<li>Act in a deceptive manner by, among other things, impersonating any person;</li>\r\n<li>Harass or stalk any other person;</li>\r\n<li>Harm or exploit minors;</li>\r\n<li>Distribute "spam";</li>\r\n<li>Collect information about others; or</li>\r\n<li>Advertise or solicit others to purchase any product or service within the Site (unless you are an official partner or advertiser and have a written agreement with us).</li>\r\n</ul>\r\n<p>The Site owner has the right, but not the obligation, to monitor all conduct on and content submitted to the Service.</p>\r\n<hr />\r\n<h2>Membership</h2>\r\n<p>REGISTRATION: To fully use the the Service, you must register as a member by providing a user name, password, and valid email address. You must provide complete and accurate registration information and notify us if your information changes. If you are a business, government, or non-profit entity, the person whose email address is associated with the account must have the authority to bind the entity to this Agreement.</p>\r\n<p>USER NAME: We encourage you to use your real name. If you are a business, government, or non-profit entity, you must use the actual name of your organization. You may not use someone else''s name, a name that violates any third party right, or a name that is obscene or otherwise objectionable.</p>\r\n<p>ACCOUNT SECURITY: You are responsible for all activity that occurs under your account, including any activity by unauthorized users. You must not allow others to use your account. You must safeguard the confidentiality of your password. If you are using a computer that others have access to, you must log out of your account after using the Service.</p>\r\n<hr />\r\n<h2>Content Restrictions</h2>\r\n<p>You may not upload, post, or transmit (collectively, "submit") any video, image, text, audio recording, or other work (collectively, "content") that:</p>\r\n<ul>\r\n<li>Infringes any third party''s copyrights or other rights (e.g., trademark, privacy rights, etc.);</li>\r\n<li>Contains sexually explicit content or pornography (provided, however, that non-sexual nudity is permitted);</li>\r\n<li>Contains hateful, defamatory, or discriminatory content or incites hatred against any individual or group;</li>\r\n<li>Exploits minors;</li>\r\n<li>Depicts unlawful acts or extreme violence;</li>\r\n<li>Depicts animal cruelty or extreme violence towards animals;</li>\r\n<li>Promotes fraudulent schemes, multi level marketing (MLM) schemes, get rich quick schemes, online gaming and gambling, cash gifting, work from home businesses, or any other dubious money-making ventures; or Violates any law.</li>\r\n</ul>', 1, 1366891687, 1, 'terms-toa', 1, '0'),
(2, '404 Error', '<h3>Sorry, page not found!</h3>\r\n<p>The page you are looking for could not be found. Please check the link you followed to get here and try again.</p>', 1, 1366891687, 1, '404', 3, '0');

-- --------------------------------------------------------

--
-- Table structure for table `pm_preroll_ads`
--

CREATE TABLE IF NOT EXISTS `pm_preroll_ads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `duration` mediumint(5) unsigned NOT NULL,
  `user_group` tinyint(2) unsigned NOT NULL,
  `impressions` int(10) unsigned NOT NULL,
  `status` enum('0','1') NOT NULL,
  `code` text NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pm_preroll_ads`
--

INSERT INTO `pm_preroll_ads` (`id`, `name`, `duration`, `user_group`, `impressions`, `status`, `code`, `options`) VALUES
(1, 'uuuuuuuuuu', 30, 0, 0, '0', 'yuiuiuyi', 'a:5:{s:4:"skip";i:1;s:18:"skip_delay_seconds";i:5;s:15:"ignore_category";a:0:{}s:13:"ignore_source";a:0:{}s:13:"disable_stats";i:0;}');

-- --------------------------------------------------------

--
-- Table structure for table `pm_ratings`
--

CREATE TABLE IF NOT EXISTS `pm_ratings` (
  `id` varchar(10) NOT NULL DEFAULT '',
  `total_votes` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `total_value` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `used_ips` longtext,
  `which_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_reports`
--

CREATE TABLE IF NOT EXISTS `pm_reports` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `r_type` enum('1','2') NOT NULL DEFAULT '1',
  `entry_id` varchar(20) NOT NULL DEFAULT '',
  `added` varchar(11) NOT NULL DEFAULT '',
  `reason` varchar(100) NOT NULL DEFAULT '',
  `submitted` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_searches`
--

CREATE TABLE IF NOT EXISTS `pm_searches` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `string` varchar(100) NOT NULL DEFAULT '',
  `hits` mediumint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_sources`
--

CREATE TABLE IF NOT EXISTS `pm_sources` (
  `source_id` smallint(2) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(20) NOT NULL DEFAULT '',
  `source_rule` varchar(40) NOT NULL DEFAULT '',
  `url_example` varchar(100) NOT NULL DEFAULT '',
  `last_check` int(10) unsigned NOT NULL DEFAULT '0',
  `flv_player_support` enum('0','1') NOT NULL DEFAULT '0',
  `embed_player_support` enum('0','1') NOT NULL DEFAULT '0',
  `embed_code` text NOT NULL,
  `user_choice` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`source_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;

--
-- Dumping data for table `pm_sources`
--

INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`) VALUES
(1, 'localhost', '/(.*?)\\.flv/i', '', 0, '1', '0', '', 'flvplayer'),
(2, 'other', '/(.*?)\\.flv/i', 'http://www.example.com/uploads/video.flv', 0, '1', '0', '', 'flvplayer'),
(3, 'youtube', '/youtube\\./i', 'http://www.youtube.com/watch?v=[VIDEO ID]', 0, '1', '1', '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="%%player_w%%" height="%%player_h%%">\r\n<param name="movie" value="http://www.youtube.com/v/%%yt_id%%?hl=en_US&fs=1&hd=%%use_hq_vids%%&rel=0&autoplay=%%player_autoplay%%&color2=0x%%player_bgcolor%%&showsearch=0&showinfo=0&iv_load_policy=3">\r\n</param><param name="allowFullScreen" value="true">\r\n</param><param name="allowscriptaccess" value="never">\r\n</param><param name="allowNetworking" value="internal">\r\n</param><param name="wmode" value="%%player_wmode%%">\r\n</param>\r\n<embed src="http://www.youtube.com/v/%%yt_id%%?hl=en_US&fs=1&hd=%%use_hq_vids%%&rel=0&autoplay=%%player_autoplay%%&color2=0x%%player_bgcolor%%&showsearch=0&showinfo=0&iv_load_policy=3" type="application/x-shockwave-flash" allowscriptaccess="never" allowNetworking="internal" allowfullscreen="true" wmode="%%player_wmode%%" width="%%player_w%%" height="%%player_h%%">\r\n</embed>\r\n</object>', 'embed'),
(5, 'dailymotion', '/dailymotion\\./i', 'http://www.dailymotion.com/en/category/[VIDEO ID]_video-title-here', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://www.dailymotion.com/swf/%%yt_id%%&related=0"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param>\r\n <embed src="http://www.dailymotion.com/swf/%%yt_id%%&related=0" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" allowFullScreen="true" wmode="%%player_wmode%%" allowScriptAccess="always"></embed>\r\n</object>', 'embed'),
(6, 'metacafe', '/metacafe\\.com/i', 'http://www.metacafe.com/watch/[VIDEO ID]/video_title_here/', 0, '1', '1', '<embed src="http://www.metacafe.com/fplayer/%%yt_id%%/video.swf" width="%%player_w%%" height="%%player_h%%" wmode="%%player_wmode%%" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_%%yt_id%%"> \r\n</embed>', 'embed'),
(7, 'myspace', '/myspace\\.com/i', 'http://www.myspace.com/video/channel/video-title/123456781', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="allowFullScreen" value="true"/>\r\n <param name="wmode" value="%%player_wmode%%"/>\r\n <param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m=%%yt_id%%,t=1,mt=video"/>\r\n <embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m=%%yt_id%%,t=1,mt=video" width="%%player_w%%" height="%%player_h%%" allowFullScreen="true" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(9, 'veoh', '/veoh\\.com/i', 'http://www.veoh.com/collection/Artist-or-Group-Name/watch/[VIDEO ID]', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" id="veohFlashPlayer" name="veohFlashPlayer">\r\n <param name="movie" value="http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?permalinkId=%%yt_id%%&player=videodetailsembedded&videoAutoPlay=%%player_autoplay%%&id=anonymous"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowscriptaccess" value="always"></param>\r\n <embed src="http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?permalinkId=%%yt_id%%&player=videodetailsembedded&videoAutoPlay=%%player_autoplay%%&id=anonymous" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" id="veohFlashPlayerEmbed" name="veohFlashPlayerEmbed" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(10, 'break', '/break\\.com/i', 'http://www.break.com/index/video-title-here.html', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://embed.break.com/%%yt_id%%"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowScriptAccess" value="always"></param>\r\n <embed src="http://embed.break.com/%%yt_id%%" type="application/x-shockwave-flash" allowScriptAccess="always" wmode="%%player_wmode%%" width="%%player_w%%" height="%%player_h%%"></embed>\r\n</object>', 'embed'),
(11, 'myvideo', '/myvideo\\.de/i', 'http://www.myvideo.de/watch/[VIDEO ID]/Video_title_here/', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://www.myvideo.de/movie/%%yt_id%%"></param>\r\n <param name="AllowFullscreen" value="true"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="AllowScriptAccess" value="always"></param>\r\n <embed src="http://www.myvideo.de/movie/%%yt_id%%" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(71, 'nhaccuatui', '/nhaccuatui\\.com/i', 'http://www.nhaccuatui.com/mv4u/xem-clip/cjidlr07OG3N/phai-lam-the-nao-wanbi-tuan-anh.html', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="%%url_flv%%" />\r\n <param name="quality" value="high" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="allowscriptaccess" value="always" />\r\n <embed src="%%url_flv%%" allowscriptaccess="always" quality="high" wmode="%%player_wmode%%" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%">\r\n </embed>\r\n</object>', 'embed'),
(72, 'kure', '/kure\\.tv/i', 'http://www.kure.tv/otomobil/494-surucu/bmw-z4-test-surusu/151-Bolum/87652/', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="http://www.kure.tv/VideoEmbed?ID=%%yt_id%%" hspace="0" vspace="0" scrolling="no" frameborder="0" allowfullscreen="true"></iframe>', 'embed'),
(43, 'windows media player', '/-(.*?)\\.(wmv|asf|wma)/i', 'http://www.example.com/video.wmv', 0, '0', '1', '<object id="wmv" width="%%player_w%%" height="%%player_h%%" classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" type="application/x-oleobject">\r\n<param name="URL" value="%%url_flv%%">\r\n<param name="AutoStart" value="true">\r\n<param name="ShowControls" value="true">\r\n<param name="ShowStatusBar" value="false">\r\n<param name="ShowDisplay" value="false">\r\n<param name="EnableFullScreenControls" value="true">\r\n<param name="FullScreenMode" value="true">\r\n<param name="wmode" value="%%player_wmode%%"></param> \r\n<embed type="application/x-mplayer2" src="%%url_flv%%" name="MediaPlayer"\r\nwidth="%%player_w%%" height="%%player_h%%" ShowControls="1" ShowStatusBar="0" ShowDisplay="0" AutoStart="%%player_autoplay%%" EnableFullScreenControls="1" FullScreenMode="1" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(69, 'veevr', '/veevr\\.com/i', 'http://veevr.com/videos/videoID', 0, '0', '1', '<iframe src="http://veevr.com/embed/%%yt_id%%?w=%%player_w%%&h=%%player_h%%" width="%%player_w%%" height="%%player_h%%" scrolling="no" frameborder="0"></iframe>', 'embed'),
(70, '123video.nl', '/123video\\.nl/i', 'http://www.123video.nl/playvideos.asp?MovieID=1234567', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://www.123video.nl/123video_emb.swf?mediaSrc=%%yt_id%%"></param>\r\n <param name="quality" value="high"></param>\r\n <param name="allowScriptAccess" value="always"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <embed src="http://www.123video.nl/123video_emb.swf?mediaSrc=%%yt_id%%" quality="high" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" type="application/x-shockwave-flash" allowscriptaccess="always" pluginspage="http://www.macromedia.com/go/getflashplayer" />\r\n</object>', 'embed'),
(16, 'vimeo', '/vimeo\\.com/i', 'http://vimeo.com/[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=%%yt_id%%&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=%%player_bgcolor%%&amp;fullscreen=1" />\r\n <embed src="http://vimeo.com/moogaloop.swf?clip_id=%%yt_id%%&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=%%player_bgcolor%%&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="%%player_w%%" height="%%player_h%%" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(17, 'trilulilu', '/trilulilu\\.ro/i', 'http://www.trilulilu.ro/user/[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%"> <param name="wmode" value="%%player_wmode%%"></param> <param name="movie" value="http://embed.trilulilu.ro/video/%%username%%/%%yt_id%%.swf"></param> <param name="allowFullScreen" value="true"></param> <param name="allowscriptaccess" value="always"></param> <param name="flashvars" value="username=%%username%%&hash=%%yt_id%%&color=0x%%player_bgcolor%%"></param> <embed src="http://embed.trilulilu.ro/video/%%username%%/%%yt_id%%.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" wmode="%%player_wmode%%" flashvars="username=%%username%%&hash=%%yt_id%%&color=0x%%player_bgcolor%%"></embed> </object>', 'embed'),
(18, 'bliptv', '/blip\\.tv/i', 'http://blip.tv/user/video-title-[VIDEO ID]', 0, '1', '1', '<embed src="http://blip.tv/play/%%yt_id%%" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" allowscriptaccess="always" allowfullscreen="true" wmode="%%player_wmode%%"></embed> ', 'embed'),
(19, 'sevenload', '/sevenload\\.com/i', 'http://en.sevenload.com/videos/[VIDEO ID]-Video-title-here', 0, '1', '1', '<object type="application/x-shockwave-flash" data="http://static.sevenload.com/swf/player/player.swf?configPath=http%3A%2F%2Fflash.sevenload.com%2Fplayer%3FportalId%3Den%26autoplay%3D%%player_autoplay%%%26mute%3D0%26itemId%3D%%yt_id%%&locale=en_US&autoplay=%%player_autoplay%%" width="%%player_w%%" height="%%player_h%%"> <param name="allowFullscreen" value="true" /> <param name="allowScriptAccess" value="always" /> <param name="movie" value="http://static.sevenload.com/swf/player/player.swf?configPath=http%3A%2F%2Fflash.sevenload.com%2Fplayer%3FportalId%3Den%26autoplay%3D%%player_autoplay%%%26mute%3D0%26itemId%3D%%yt_id%%&locale=en_US&autoplay=%%player_autoplay%%" />', 'embed'),
(20, 'funnyordie', '/funnyordie\\.com/i', 'http://www.funnyordie.com/videos/[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="ordie_player_%%yt_id%%">\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="movie" value="http://player.ordienetworks.com/flash/fodplayer.swf" />\r\n <param name="flashvars" value="key=%%yt_id%%" />\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always"></param>\r\n <embed width="%%player_w%%" height="%%player_h%%" flashvars="key=%%yt_id%%" allowfullscreen="true" allowscriptaccess="always" quality="high" src="http://player.ordienetworks.com/flash/fodplayer.swf" name="ordie_player_%%yt_id%%" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(68, 'clip.vn', '/clip\\.vn//i', 'http://clip.vn/watch/Video-title,videoID', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://clip.vn/w/%%yt_id%%"/>\r\n <param name="allowFullScreen" value="true"/>\r\n <param name="allowScriptAccess" value="always"/>\r\n <embed type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" width="%%player_w%%" height="%%player_h%%" src="http://clip.vn/w/%%yt_id%%"></embed>\r\n</object>', 'embed'),
(23, 'filebox', '/filebox\\.ro/i', 'http://www.filebox.ro/video/play_video.php?key=[VIDEO ID]', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <embed type="application/x-shockwave-flash" src="http://www.filebox.ro/video/FileboxPlayer_provider.php" style="" id="mediaplayer" name="mediaplayer" quality="high" allowfullscreen="true" wmode="%%player_wmode%%" flashvars="source_script=http://videoserver325.filebox.ro/get_video.php&key=%%yt_id%%&autostart=%%player_autoplay%%&getLink=http://fbx.ro/v/%%yt_id%%&splash=http://imageserver.filebox.ro/get_splash.php?key=%%yt_id%%&link=" height="%%player_h%%" width="%%player_w%%">\r\n</object>', 'embed'),
(24, 'youku', '/youku\\.com/i', 'http://v.youku.com/v_show/id_[VIDEO ID].html', 0, '0', '1', '<embed src="http://player.youku.com/player.php/sid/%%yt_id%%=/v.swf" quality="high" width="%%player_w%%" height="%%player_h%%" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>', 'embed'),
(67, 'tudou', '/tudou\\.com/i', 'http://www.tudou.com/programs/view/video-id/', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://www.tudou.com/v/%%yt_id%%/v.swf"></param>\r\n <param value="true" name="allowfullscreen"></param>\r\n <param value="always" name="allowscriptaccess"></param>\r\n <param value="opaque" name="%%player_wmode%%"></param>\r\n <embed src="http://www.tudou.com/v/%%yt_id%%/v.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="%%player_wmode%%" width="%%player_w%%" height="%%player_h%%"></embed>\r\n</object>', 'embed'),
(66, 'publicdomainflicks', '/publicdomainflicks\\.com/i', 'http://www.publicdomainflicks.com/0123-video-title/', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://www.publicdomainflicks.com/flvplayer.swf"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <param name="allowScriptAccess" value="always"></param>\r\n <param name="flashvars" value="file=%%url_flv%%&autostart=%%player_autoplay%%&volume=80"></param>\r\n <embed src="http://www.publicdomainflicks.com/flvplayer.swf" width="%%player_w%%" height="%%player_h%%" allowscriptaccess="always" allowfullscreen="true" flashvars="file=%%url_flv%%&autostart=%%player_autoplay%%&volume=80"/>\r\n</object>', 'embed'),
(30, 'liveleak', '/liveleak\\.com/i', 'http://www.liveleak.com/view?i=[VIDEO ID]', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n<param name="movie" value="http://www.liveleak.com/e/%%yt_id%%"></param>\r\n<param name="wmode" value="%%player_wmode%%"></param>\r\n<embed src="http://www.liveleak.com/e/%%yt_id%%" type="application/x-shockwave-flash" wmode="%%player_wmode%%" width="%%player_w%%" height="%%player_h%%"></embed></object>', 'embed'),
(32, 'supervideo', '/balsas\\.lt/i', 'http://video.balsas.lt/video/[VIDEO ID]', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="%%player_w%%" height="%%player_h%%">\r\n<param name="allowScriptAccess" value="always" />\r\n<param name="allowFullScreen" value="true" />\r\n<param name="movie" value="http://video.balsas.lt/pimg/Site/Flash/player.swf?configFile=http://video.balsas.lt/Videos/getConfig/%%yt_id%%" />\r\n<param name="quality" value="high" />\r\n<param name="bgcolor" value="#%%player_bgcolor%%" />\r\n<param name="flashvars" value="configFile=http://video.balsas.lt/Videos/getConfig/%%yt_id%%"/>\r\n<embed src="http://video.balsas.lt/pimg/Site/Flash/player.swf?configFile=http://video.balsas.lt/Videos/getConfig/%%yt_id%%" quality="high" bgcolor="#%%player_bgcolor%%" width="%%player_w%%" height="%%player_h%%" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>\r\n', 'embed'),
(65, 'peteava', '/peteava\\.ro/i', 'http://www.peteava.ro/id-123456-video-title', 0, '0', '1', '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="%%player_w%%" height="%%player_h%%" id="swf_player_id_for_ie_who_sucks">\r\n <param name="movie" value="http://www.peteava.ro/static/swf/player.swf">\r\n <param name="allowfullscreen" value="true">\r\n <param name="allowscriptaccess" value="always">\r\n <param name="menu" value="false">\r\n <param name="flashvars" value="streamer=http://content.peteava.ro/stream.php&file=%%yt_id%%_standard.mp4&image=http://storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerstandard&hd_file=&hd_image=http://storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerhigh&autostart=%%player_autoplay%%">\r\n <embed src="http://www.peteava.ro/static/swf/player.swf" id="__ptv_pl_%%yt_id%%_%%player_w%%_%%player_h%%__" name="__ptv_pl_%%yt_id%%_%%player_w%%_%%player_h%%__" width="%%player_w%%" height="%%player_h%%" allowscriptaccess="always" menu="false" allowfullscreen="true" \r\n flashvars="streamer=http://content.peteava.ro/stream.php&file=%%yt_id%%_standard.mp4&image=http://storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerstandard&hd_file=&hd_image=http://storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerhigh&autostart=%%player_autoplay%%"/>\r\n</object>', 'embed'),
(35, 'musicme', '/musicme\\.com/i', 'http://www.musicme.com/#/Patrick-Bruel/videos/Epk-Patrick-Bruel-[VIDEO ID].html', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="%%player_w%%" height="%%player_h%%" id="musicmevideo%%yt_id%%">\r\n <param name="movie" value="http://www.musicme.com/_share/vplayer.swf?cb=%%yt_id%%"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowScriptAccess" value="always">\r\n <param name="bgcolor" value="#000000" />\r\n <embed src="http://www.musicme.com/_share/vplayer.swf?cb=%%yt_id%%" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" bgcolor="#000000" allowScriptAccess="always" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(39, 'spike', '/spike\\.com/i', 'http://www.spike.com/video/cinemassacre-top-10/[VIDEO ID]', 0, '0', '1', '<embed width="%%player_w%%" height="%%player_h%%" src="http://www.spike.com/efp" quality="high" bgcolor="000000" name="efp" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="flvbaseclip=%%yt_id%%" allowfullscreen="true" wmode="%%player_wmode%%">\r\n</embed> ', 'embed'),
(64, 'videozer', '/videozer\\.com/i', 'http://www.videozer.com/video/abcde', 0, '0', '1', '<object id="player" width="%%player_w%%" height="%%player_h%%" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">\r\n <param name="movie" value="http://www.videozer.com/embed/%%yt_id%%"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <param name="allowscriptaccess" value="always"></param>\r\n <embed src="http://www.videozer.com/embed/%%yt_id%%" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"></embed>\r\n</object>', 'embed'),
(42, 'musicplayon', '/musicplayon\\.com/i', 'http://en.musicplayon.com/play?v=[VIDEO ID]Video_Title', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,15,0">\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="movie" value="http://en.musicplayon.com/embed?VID=%%yt_id%%&autoPlay=N&hideLeftPanel=Y&bgColor=0x232323&activeColor=0x%%player_bgcolor%%&inactiveColor=0x3C3C3C&titleColor=0x584596&textsColor=0x999999&selectedColor=0x0F0F0F&btnColor=0x000000&rnd=288950" />\r\n <param name="quality" value="high" />\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <embed width="%%player_w%%" height="%%player_h%%" src="http://en.musicplayon.com/embed?VID=%%yt_id%%&autoPlay=N&hideLeftPanel=Y&bgColor=0x232323&activeColor=0x%%player_bgcolor%%&inactiveColor=0x3C3C3C&titleColor=0x584596&textsColor=0x999999&selectedColor=0x0F0F0F&btnColor=0x000000&rnd=288950" quality="high" allowfullscreen="true" allowscriptaccess="always" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed'),
(44, 'quicktime', '/-(.*?)\\.(mov|m2a|m2v|3gp|3g2|m4a|m4v)/i', 'http://www.example.com/video.mov', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase= "http://www.apple.com/qtactivex/qtplugin.cab">\r\n <param name="src" value="%%url_flv%%" />\r\n <param name="autoplay" value="false" />\r\n <param name="controller" value="true" />\r\n <param name="scale" value="tofit" />\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <embed src="%%url_flv%%" width="%%player_w%%" height="%%player_h%%" scale="tofit" wmode="%%player_wmode%%" autoplay="false" controller="true" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed>\r\n</object>', 'embed'),
(45, 'yahoomusic', '/music\\.yahoo\\.com/i', 'http://new.music.yahoo.com/videos/LadyGaGa/Bad-Romance--218606963', 0, '0', '1', '<object width="%%player_w%%" id="uvp_fop" height="%%player_h%%" allowFullScreen="true">\r\n <param name="movie" value="http://d.yimg.com/m/up/fop/embedflv/swf/fop.swf"/>\r\n <param name="flashVars" value="%%url_flv%%"/>\r\n <param name="wmode" value="%%player_wmode%%"/>\r\n <embed width="%%player_w%%" id="uvp_fop" height="%%player_h%%" allowFullScreen="true" src="http://d.yimg.com/m/up/fop/embedflv/swf/fop.swf" type="application/x-shockwave-flash" flashvars="%%url_flv%%" />\r\n</object>', 'embed'),
(47, '5min', '/5min\\.com\\/video/i', 'http://www.5min.com/Video/Video-Title-[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%" id="FiveminPlayer" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">\r\n <param name="allowfullscreen" value="true"/>\r\n <param name="allowScriptAccess" value="always"/>\r\n <param name="movie" value="http://www.5min.com/Embeded/%%yt_id%%/"/>\r\n <embed name="FiveminPlayer" src="http://www.5min.com/Embeded/%%yt_id%%/" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" allowScriptAccess="always"></embed>\r\n</object>', 'embed'),
(63, 'vplay', '/vplay\\.ro/i', 'http://vplay.ro/watch/abcdef/', 0, '0', '1', '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://i.vplay.ro/f/embed.swf?key=%%yt_id%%">\r\n <param name="allowfullscreen" value="true">\r\n <param name="quality" value="high">\r\n <embed src="http://i.vplay.ro/f/embed.swf?key=%%yt_id%%" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" ></embed>\r\n</object>', 'embed'),
(51, 'smotri', '/smotri\\.com\\/video/i', 'http://smotri.com/video/view/?id=[VIDEO ID]', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://pics.smotri.com/scrubber_custom8.swf?file=%%yt_id%%&bufferTime=3&autoStart=false&str_lang=eng&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_black.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" />\r\n <param name="allowScriptAccess" value="always" />\r\n <param name="allowFullScreen" value="true" />\r\n <embed src="http://pics.smotri.com/scrubber_custom8.swf?file=%%yt_id%%&bufferTime=3&autoStart=false&str_lang=eng&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_black.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="window" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash"></embed>\r\n </object>', 'embed'),
(52, 'divx', '/(.*?)\\.(avi|divx|mkv)/i', 'http://www.example.com/video.avi', 0, '0', '1', '<object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="%%player_w%%" height="%%player_h%%" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab"> \r\n<param name="src" value="%%url_flv%%" />\r\n<param name="autoPlay" value="%%player_autoplay%%" />\r\n<param name="bannerEnabled" value="false" />\r\n<param name="previewImage" value="%%yt_thumb%%" />\r\n<embed type="video/divx" src="%%url_flv%%" autoPlay="%%player_autoplay%%" previewImage="%%yt_thumb%%" bannerEnabled="false" width="%%player_w%%" height="%%player_h%%" pluginspage="http://go.divx.com/plugin/download/"></embed> \r\n</object>', 'embed'),
(53, 'vbox7', '/vbox7\\.com\\/play/i', 'http://vbox7.com/play:[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="http://i48.vbox7.com/player/ext.swf?vid=%%yt_id%%"></param>\r\n <param name="quality" value="high"></param>\r\n <embed src="http://i48.vbox7.com/player/ext.swf?vid=%%yt_id%%" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%"></embed>\r\n </object>', 'embed'),
(54, 'livestream', '/livestream\\.com/i', 'http://www.livestream.com/channel_name', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" id="lsplayer" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"><param name="movie" value="%%url_flv%%&amp;autoPlay=false"></param><param name="allowScriptAccess" value="always"></param><param name="allowFullScreen" value="true"></param><embed name="lsplayer" src="%%url_flv%%&amp;autoPlay=false" width="%%player_w%%" height="%%player_h%%" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash"></embed></object>', 'embed'),
(55, 'justin', '/justin\\.tv/i', 'http://www.justin.tv/channel_name', 0, '0', '1', '<object type="application/x-shockwave-flash" height="%%player_h%%" width="%%player_w%%" id="live_embed_player_flash" \r\ndata="%%url_flv%%" bgcolor="#%%player_bgcolor%%">\r\n <param name="allowFullScreen" value="true" />\r\n <param name="allowScriptAccess" value="always" />\r\n <param name="allowNetworking" value="all" />\r\n <param name="movie" value="%%url_flv%%" />\r\n <param name="flashvars" value="channel=%%yt_id%%&auto_play=false&start_volume=25" />\r\n</object>', 'embed'),
(56, 'ustream', '/ustream\\.tv/i', 'http://www.ustream.tv/channel/user', 0, '0', '1', '<object type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" data="http://www.ustream.tv/flash/viewer.swf">\r\n <param name="flashvars" value="autoplay=true&amp;%%yt_id%%&amp;v3=true&amp;locale=en_US&amp;referrer=unknown&amp;enablejsapi=true"/>\r\n <param name="allowfullscreen" value="true"/>\r\n <param name="allowscriptaccess" value="always"/>\r\n <param name="movie" value="%%url_flv%%"/>\r\n <embed flashvars="autoplay=true&amp;%%yt_id%%&amp;v3=true&amp;locale=en_US&amp;referrer=unknown&amp;enablejsapi=true" src="http://www.ustream.tv/flash/viewer.swf" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" />\r\n </object>', 'embed'),
(57, 'mp3', '/(.*?)\\.mp3/i', 'http://www.example.com/file.mp3', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n <param name="scale" value="noscale" />\r\n <param name="allowFullScreen" value="true" />\r\n <param name="allowScriptAccess" value="always" />\r\n <param name="allowNetworking" value="all" />\r\n <param name="bgcolor" value="#%%player_bgcolor%%" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="movie" value="%%site_url%%/jwplayer.swf" />\r\n <param name="flashVars" value="&plugins=revolt-1&file=%%url_flv%%&type=sound&image=%%yt_thumb%%&backcolor=%%player_bgcolor%%&frontcolor=FFFFFF&autostart=%%player_autoplay%%&screencolor=000000" />\r\n <embed src="%%site_url%%/jwplayer.swf" width="%%player_w%%" height="%%player_h%%" scale="noscale" bgcolor="#%%player_bgcolor%%" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" wmode="%%player_wmode%%" flashvars="&plugins=revolt-1&file=%%url_flv%%&type=sound&image=%%yt_thumb%%&backcolor=%%player_bgcolor%%&frontcolor=FFFFFF&autostart=%%player_autoplay%%&screencolor=000000"></embed>\r\n</object>', 'embed'),
(58, 'mynet', '/video\\.mynet\\.com/i', 'http://video.mynet.com/username/video-title/video-id/', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <param name="autoplay" value="%%player_autoplay%%" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="movie" value="http://video.mynet.com/username/video-title/%%yt_id%%.swf" />\r\n <embed src="http://video.mynet.com/username/video-title/%%yt_id%%.swf" type="application/x-shockwave-flash" wmode="%%player_wmode%%" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" autoplay="%%player_autoplay%%"></embed>\r\n</object>', 'embed'),
(59, 'vidivodo', '/vidivodo\\.com/i', 'http://www.vidivodo.com/video-id/video-title', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="%%url_flv%%" />\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <param name="autoplay" value="%%player_autoplay%%" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="bgcolor" value="#%%player_bgcolor%%" />\r\n <embed src="%%url_flv%%" type="application/x-shockwave-flash" wmode="%%player_wmode%%" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" autoplay="%%player_autoplay%%" bgcolor="#%%player_bgcolor%%"></embed>\r\n</object>', 'embed'),
(61, 'izlesene', '/izlesene\\.com/i', 'http://www.izlesene.com/video/video-title/video-id', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="bgcolor" value="#%%player_bgcolor%%" />\r\n <param name="movie" value="http://www.izlesene.com/embedplayer.swf?video=%%yt_id%%" />\r\n <embed src="http://www.izlesene.com/embedplayer.swf?video=%%yt_id%%" wmode="%%player_wmode%%" bgcolor="#%%player_bgcolor%%" allowfullscreen="true" allowscriptaccess="always" menu="false" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash"></embed>\r\n</object>', 'embed'),
(62, 'videobb', '/videobb\\./i', 'http://www.videobb.com/video/video-id', 0, '0', '1', '<object id="player" width="%%player_w%%" height="%%player_h%%" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">\r\n <param name="movie" value="%%url_flv%%"></param>\r\n <param name="allowFullScreen" value="true" ></param>\r\n <param name="allowscriptaccess" value="always"></param>\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <embed src="%%url_flv%%" wmode="%%player_wmode%%" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%"></embed>\r\n</object>', 'embed'),
(73, 'mail.ru', '/mail\\.ru\\/video/i', 'http://my.mail.ru/video/mail/radnovomyznakomstvy/176/254.html', 0, '0', '1', '<iframe src="http://api.video.mail.ru/videos/embed/%%yt_id%%" width="%%player_w%%" height="%%player_h%%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', 'embed'),
(74, 'vk', '/vk\\.(com|ru|me)\\/video/i', 'http://vk.com/video28908630_165233143', 0, '0', '1', '<iframe src="http://vk.com/video_ext.php?%%yt_id%%" width="%%player_w%%" height="%%player_h%%" frameborder="0"></iframe>', 'embed'),
(75, 'rutube', '/rutube\\.ru\\/video/i', 'http://rutube.ru/video/852e974534e3527f16810a7a19c418b0/', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//rutube.ru/video/embed/%%yt_id%%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', 'embed'),
(76, 'novamov', '/novamov\\.com/i', 'http://www.novamov.com/video/video-id', 0, '0', '1', '<iframe style="overflow: hidden; border: 0; width: %%player_w%%px; height: %%player_h%%px;" src="http://embed.novamov.com/embed.php?v=%%yt_id%%" scrolling="no"></iframe>', 'embed');

-- --------------------------------------------------------

--
-- Table structure for table `pm_tags`
--

CREATE TABLE IF NOT EXISTS `pm_tags` (
  `tag_id` int(7) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `safe_tag` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `safe_tag` (`safe_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_temp`
--

CREATE TABLE IF NOT EXISTS `pm_temp` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `video_title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `yt_length` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `category` smallint(3) NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL DEFAULT '',
  `user_id` int(5) NOT NULL DEFAULT '0',
  `added` int(10) NOT NULL DEFAULT '0',
  `source_id` mediumint(3) NOT NULL DEFAULT '0',
  `language` mediumint(3) NOT NULL DEFAULT '0',
  `thumbnail` varchar(255) NOT NULL DEFAULT '',
  `yt_id` varchar(50) NOT NULL DEFAULT '',
  `url_flv` varchar(255) NOT NULL DEFAULT '',
  `mp4` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_users`
--

CREATE TABLE IF NOT EXISTS `pm_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(150) NOT NULL DEFAULT '',
  `gender` varchar(10) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `reg_ip` varchar(20) NOT NULL DEFAULT '',
  `reg_date` int(10) unsigned NOT NULL,
  `last_signin` int(10) unsigned NOT NULL,
  `last_signin_ip` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL DEFAULT '',
  `favorite` enum('0','1') NOT NULL DEFAULT '1',
  `power` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `about` text NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'default.gif',
  `activation_key` varchar(20) NOT NULL DEFAULT '',
  `new_password` varchar(32) NOT NULL DEFAULT '',
  `website` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `lastfm` varchar(255) NOT NULL,
  `followers_count` int(10) unsigned NOT NULL DEFAULT '0',
  `following_count` int(10) unsigned NOT NULL DEFAULT '0',
  `unread_notifications_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pm_users`
--

INSERT INTO `pm_users` (`id`, `username`, `password`, `name`, `gender`, `country`, `reg_ip`, `reg_date`, `last_signin`, `last_signin_ip`, `email`, `favorite`, `power`, `about`, `avatar`, `activation_key`, `new_password`, `website`, `facebook`, `twitter`, `lastfm`, `followers_count`, `following_count`, `unread_notifications_count`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'سید امیرحسین', 'male', '377', '127.0.0.1', 1397643677, 1404742811, '127.0.0.1', 'p30search@gmail.com', '1', '1', 'مدیر سایت', 'avatar890-1.jpg', '', '', '', '', '', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pm_users_follow`
--

CREATE TABLE IF NOT EXISTS `pm_users_follow` (
  `user_id` int(10) unsigned NOT NULL,
  `follower_id` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`follower_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_videoads`
--

CREATE TABLE IF NOT EXISTS `pm_videoads` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `hash` varchar(12) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `flv_url` varchar(255) NOT NULL DEFAULT '',
  `redirect_url` text NOT NULL,
  `redirect_type` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `disable_stats` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_videos`
--

CREATE TABLE IF NOT EXISTS `pm_videos` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `video_title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `yt_id` varchar(50) NOT NULL DEFAULT '',
  `yt_length` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `yt_thumb` varchar(255) NOT NULL DEFAULT '',
  `yt_views` int(10) NOT NULL DEFAULT '0',
  `category` varchar(30) NOT NULL DEFAULT 'none',
  `submitted` varchar(100) NOT NULL DEFAULT 'admin',
  `lastwatched` int(10) unsigned NOT NULL DEFAULT '0',
  `added` int(10) unsigned NOT NULL DEFAULT '0',
  `site_views` int(9) NOT NULL DEFAULT '0',
  `url_flv` varchar(255) NOT NULL DEFAULT '',
  `source_id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `language` smallint(2) unsigned NOT NULL DEFAULT '0',
  `age_verification` enum('0','1') NOT NULL DEFAULT '0',
  `last_check` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `restricted` enum('0','1') NOT NULL DEFAULT '0',
  `allow_comments` enum('0','1') NOT NULL DEFAULT '1',
  `video_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `added` (`added`),
  KEY `yt_id` (`yt_id`),
  KEY `featured` (`featured`),
  FULLTEXT KEY `fulltext_index` (`video_title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm_videos_urls`
--

CREATE TABLE IF NOT EXISTS `pm_videos_urls` (
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `mp4` varchar(200) NOT NULL DEFAULT '',
  `direct` varchar(200) NOT NULL DEFAULT '',
  UNIQUE KEY `uniq_id` (`uniq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
