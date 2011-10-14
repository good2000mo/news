-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 建立日期: Oct 14, 2011, 01:54 PM
-- 伺服器版本: 5.1.34
-- PHP 版本: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `news`
--
CREATE DATABASE `news` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `news`;

-- --------------------------------------------------------

--
-- 資料表格式： `attach_files`
--

CREATE TABLE IF NOT EXISTS `attach_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_ext` varchar(64) NOT NULL,
  `file_mime_type` varchar(64) NOT NULL,
  `file_path` text NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `download_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaded_at` int(10) unsigned NOT NULL,
  `secure_str` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 列出以下資料庫的數據： `attach_files`
--


-- --------------------------------------------------------

--
-- 資料表格式： `bans`
--

CREATE TABLE IF NOT EXISTS `bans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `expire` int(10) unsigned DEFAULT NULL,
  `ban_creator` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 列出以下資料庫的數據： `bans`
--


-- --------------------------------------------------------

--
-- 資料表格式： `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(80) NOT NULL DEFAULT 'New Category',
  `disp_position` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 列出以下資料庫的數據： `categories`
--

INSERT INTO `categories` (`id`, `cat_name`, `disp_position`) VALUES
(1, '聖若瑟教區中學', 1);

-- --------------------------------------------------------

--
-- 資料表格式： `censoring`
--

CREATE TABLE IF NOT EXISTS `censoring` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_for` varchar(60) NOT NULL DEFAULT '',
  `replace_with` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 列出以下資料庫的數據： `censoring`
--


-- --------------------------------------------------------

--
-- 資料表格式： `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `conf_name` varchar(255) NOT NULL DEFAULT '',
  `conf_value` text,
  PRIMARY KEY (`conf_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `config`
--

INSERT INTO `config` (`conf_name`, `conf_value`) VALUES
('o_cur_version', '1.3.6'),
('o_database_revision', '4'),
('o_board_title', '聖中資訊'),
('o_board_desc', '提供聖若瑟教區中學的最新資訊.'),
('o_default_timezone', '8'),
('o_time_format', 'H:i:s'),
('o_date_format', 'Y-m-d'),
('o_check_for_updates', '0'),
('o_check_for_versions', '0'),
('o_timeout_visit', '5400'),
('o_timeout_online', '300'),
('o_redirect_delay', '1'),
('o_show_version', '0'),
('o_show_user_info', '0'),
('o_show_post_count', '0'),
('o_signatures', '0'),
('o_smilies', '0'),
('o_smilies_sig', '1'),
('o_make_links', '1'),
('o_default_lang', 'Traditional_Chinese'),
('o_default_style', 'Oxygen'),
('o_default_user_group', '3'),
('o_topic_review', '0'),
('o_disp_topics_default', '10'),
('o_disp_posts_default', '25'),
('o_indent_num_spaces', '4'),
('o_quote_depth', '3'),
('o_quickpost', '0'),
('o_users_online', '0'),
('o_censoring', '0'),
('o_ranks', '0'),
('o_show_dot', '0'),
('o_topic_views', '0'),
('o_quickjump', '0'),
('o_gzip', '0'),
('o_additional_navlinks', ''),
('o_report_method', '0'),
('o_regs_report', '0'),
('o_default_email_setting', '1'),
('o_mailing_list', 'comus@cdsj.edu.mo'),
('o_avatars', '0'),
('o_avatars_dir', 'img/avatars'),
('o_avatars_width', '60'),
('o_avatars_height', '60'),
('o_avatars_size', '10240'),
('o_search_all_forums', '1'),
('o_sef', 'Default'),
('o_admin_email', 'comus@cdsj.edu.mo'),
('o_webmaster_email', 'comus@cdsj.edu.mo'),
('o_subscriptions', '0'),
('o_smtp_host', NULL),
('o_smtp_user', NULL),
('o_smtp_pass', NULL),
('o_smtp_ssl', '0'),
('o_regs_allow', '0'),
('o_regs_verify', '0'),
('o_announcement', '0'),
('o_announcement_heading', 'Sample announcement'),
('o_announcement_message', '<p>Enter your announcement here.</p>'),
('o_rules', '0'),
('o_rules_message', 'Enter your rules here.'),
('o_maintenance', '0'),
('o_maintenance_message', 'The forums are temporarily down for maintenance. Please try again in a few minutes.<br /><br />Administrator'),
('o_default_dst', '0'),
('p_message_bbcode', '1'),
('p_message_img_tag', '1'),
('p_message_all_caps', '1'),
('p_subject_all_caps', '1'),
('p_sig_all_caps', '1'),
('p_sig_bbcode', '1'),
('p_sig_img_tag', '0'),
('p_sig_length', '400'),
('p_sig_lines', '4'),
('p_allow_banned_email', '1'),
('p_allow_dupe_email', '1'),
('p_force_guest_email', '0'),
('attach_always_deny', 'html,htm,php,php3,php4,exe,com,bat'),
('attach_basefolder', 'extensions/extensions/attachments/'),
('attach_create_orphans', '1'),
('attach_icon_folder', 'http://www.cdsj.edu.mo/news/extensions/pun_attachment/img/'),
('attach_icon_extension', 'txt,doc,pdf,wav,mp3,ogg,avi,mpg,mpeg,png,jpg,jpeg,gif'),
('attach_icon_name', 'text.png,doc.png,doc.png,audio.png,audio.png,audio.png,video.png,video.png,video.png,image.png,image.png,image.png,image.png'),
('attach_subfolder', 'dbfa31b307488c6098e42277f546232b'),
('attach_use_icon', '1'),
('attach_disp_small', '1'),
('attach_small_height', '60'),
('attach_small_width', '60'),
('attach_disable_attach', '0');

-- --------------------------------------------------------

--
-- 資料表格式： `extension_hooks`
--

CREATE TABLE IF NOT EXISTS `extension_hooks` (
  `id` varchar(150) NOT NULL DEFAULT '',
  `extension_id` varchar(50) NOT NULL DEFAULT '',
  `code` text,
  `installed` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`,`extension_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `extension_hooks`
--


-- --------------------------------------------------------

--
-- 資料表格式： `extensions`
--

CREATE TABLE IF NOT EXISTS `extensions` (
  `id` varchar(150) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(25) NOT NULL DEFAULT '',
  `description` text,
  `author` varchar(50) NOT NULL DEFAULT '',
  `uninstall` text,
  `uninstall_note` text,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `dependencies` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `extensions`
--

INSERT INTO `extensions` (`id`, `title`, `version`, `description`, `author`, `uninstall`, `uninstall_note`, `disabled`, `dependencies`) VALUES
('pun_admin_add_user', 'Admin add user', '1.3.1', 'Admin may add new user using the form in the bottom of User list.', 'PunBB Development Team', NULL, NULL, 0, '||'),
('pun_bbcode', 'BBCode buttons', '1.3.6', 'Pretty buttons for easy BBCode formatting.', 'PunBB Development Team', '$forum_db->drop_field(''users'', ''pun_bbcode_enabled'');\n$forum_db->drop_field(''users'', ''pun_bbcode_use_buttons'');', NULL, 0, '||'),
('pun_attachment', 'Attachment', '1.0.5', 'Allows users to attach files to posts.', 'PunBB Development Team', '$attached_files = scandir(FORUM_ROOT.$forum_config[''attach_basefolder''].$forum_config[''attach_subfolder''].DIRECTORY_SEPARATOR);\n		foreach ($attached_files as $file)\n			if ($file != ''.'' && $file != ''..'')\n				unlink(FORUM_ROOT.$forum_config[''attach_basefolder''].$forum_config[''attach_subfolder''].DIRECTORY_SEPARATOR.$file);\n		rmdir(FORUM_ROOT.$forum_config[''attach_basefolder''].$forum_config[''attach_subfolder'']);\n\n		$forum_db->drop_table(''attach_files'');\n		$forum_db->drop_field(''groups'', ''g_pun_attachment_allow_download'');\n		$forum_db->drop_field(''groups'', ''g_pun_attachment_allow_upload'');\n		$forum_db->drop_field(''groups'', ''g_pun_attachment_allow_delete'');\n		$forum_db->drop_field(''groups'', ''g_pun_attachment_allow_delete_own'');\n		$forum_db->drop_field(''groups'', ''g_pun_attachment_upload_max_size'');\n		$forum_db->drop_field(''groups'', ''g_pun_attachment_files_per_post'');\n		$forum_db->drop_field(''groups'', ''g_pun_attachment_disallowed_extensions'');\n\n		$config_names  =  array(''attach_always_deny'', ''attach_basefolder'', ''attach_create_orphans'', ''attach_cur_version'', ''attach_icon_folder'', ''attach_icon_extension'', ''attach_icon_name'', ''attach_subfolder'', ''attach_disable_attach'', ''attach_disp_small'', ''attach_small_height'', ''attach_small_width'', ''attach_use_icon'');\n		$query_attach = array(\n			''DELETE''	=> ''config'',\n			''WHERE''		=> ''conf_name IN (\\''''.implode(''\\'', \\'''', $config_names).''\\'')''\n		);\n		$forum_db->query_build($query_attach) or error(__FILE__, __LINE__);', 'WARNING: all users'' attachments will be removed during the uninstallation process. It is recommended that you disable the "pun_attachment" extension instead, or upgrade it without uninstalling.', 0, '||');

-- --------------------------------------------------------

--
-- 資料表格式： `forum_perms`
--

CREATE TABLE IF NOT EXISTS `forum_perms` (
  `group_id` int(10) NOT NULL DEFAULT '0',
  `forum_id` int(10) NOT NULL DEFAULT '0',
  `read_forum` tinyint(1) NOT NULL DEFAULT '1',
  `post_replies` tinyint(1) NOT NULL DEFAULT '1',
  `post_topics` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`group_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `forum_perms`
--


-- --------------------------------------------------------

--
-- 資料表格式： `forums`
--

CREATE TABLE IF NOT EXISTS `forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(80) NOT NULL DEFAULT 'New forum',
  `forum_desc` text,
  `redirect_url` varchar(100) DEFAULT NULL,
  `moderators` text,
  `num_topics` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `num_posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_post_id` int(10) unsigned DEFAULT NULL,
  `last_poster` varchar(200) DEFAULT NULL,
  `sort_by` tinyint(1) NOT NULL DEFAULT '0',
  `disp_position` int(10) NOT NULL DEFAULT '0',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 列出以下資料庫的數據： `forums`
--

INSERT INTO `forums` (`id`, `forum_name`, `forum_desc`, `redirect_url`, `moderators`, `num_topics`, `num_posts`, `last_post`, `last_post_id`, `last_poster`, `sort_by`, `disp_position`, `cat_id`) VALUES
(1, '最新消息', '顯示在學校網頁首頁的最新消息.', NULL, NULL, 1, 1, 1318467786, 1, 'admin', 0, 1, 1);

-- --------------------------------------------------------

--
-- 資料表格式： `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `g_title` varchar(50) NOT NULL DEFAULT '',
  `g_user_title` varchar(50) DEFAULT NULL,
  `g_moderator` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_edit_users` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_rename_users` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_change_passwords` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_ban_users` tinyint(1) NOT NULL DEFAULT '0',
  `g_read_board` tinyint(1) NOT NULL DEFAULT '1',
  `g_view_users` tinyint(1) NOT NULL DEFAULT '1',
  `g_post_replies` tinyint(1) NOT NULL DEFAULT '1',
  `g_post_topics` tinyint(1) NOT NULL DEFAULT '1',
  `g_edit_posts` tinyint(1) NOT NULL DEFAULT '1',
  `g_delete_posts` tinyint(1) NOT NULL DEFAULT '1',
  `g_delete_topics` tinyint(1) NOT NULL DEFAULT '1',
  `g_set_title` tinyint(1) NOT NULL DEFAULT '1',
  `g_search` tinyint(1) NOT NULL DEFAULT '1',
  `g_search_users` tinyint(1) NOT NULL DEFAULT '1',
  `g_send_email` tinyint(1) NOT NULL DEFAULT '1',
  `g_post_flood` smallint(6) NOT NULL DEFAULT '30',
  `g_search_flood` smallint(6) NOT NULL DEFAULT '30',
  `g_email_flood` smallint(6) NOT NULL DEFAULT '60',
  `g_pun_attachment_allow_download` tinyint(1) DEFAULT '1',
  `g_pun_attachment_allow_upload` tinyint(1) DEFAULT '1',
  `g_pun_attachment_allow_delete` tinyint(1) DEFAULT '0',
  `g_pun_attachment_allow_delete_own` tinyint(1) DEFAULT '1',
  `g_pun_attachment_upload_max_size` int(10) DEFAULT '2000000',
  `g_pun_attachment_files_per_post` tinyint(3) DEFAULT '1',
  `g_pun_attachment_disallowed_extensions` text,
  PRIMARY KEY (`g_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 列出以下資料庫的數據： `groups`
--

INSERT INTO `groups` (`g_id`, `g_title`, `g_user_title`, `g_moderator`, `g_mod_edit_users`, `g_mod_rename_users`, `g_mod_change_passwords`, `g_mod_ban_users`, `g_read_board`, `g_view_users`, `g_post_replies`, `g_post_topics`, `g_edit_posts`, `g_delete_posts`, `g_delete_topics`, `g_set_title`, `g_search`, `g_search_users`, `g_send_email`, `g_post_flood`, `g_search_flood`, `g_email_flood`, `g_pun_attachment_allow_download`, `g_pun_attachment_allow_upload`, `g_pun_attachment_allow_delete`, `g_pun_attachment_allow_delete_own`, `g_pun_attachment_upload_max_size`, `g_pun_attachment_files_per_post`, `g_pun_attachment_disallowed_extensions`) VALUES
(1, 'Administrators', 'Administrator', 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, -1, ''),
(2, 'Guest', NULL, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 60, 30, 0, 0, 0, 0, 0, 0, 0, ''),
(3, 'Members', NULL, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 60, 30, 60, 1, 1, 0, 1, 2000000, 1, NULL),
(4, 'Moderators', 'Moderator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 0, 1, 2000000, 1, NULL);

-- --------------------------------------------------------

--
-- 資料表格式： `online`
--

CREATE TABLE IF NOT EXISTS `online` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `ident` varchar(200) NOT NULL DEFAULT '',
  `logged` int(10) unsigned NOT NULL DEFAULT '0',
  `idle` tinyint(1) NOT NULL DEFAULT '0',
  `csrf_token` varchar(40) NOT NULL DEFAULT '',
  `prev_url` varchar(255) DEFAULT NULL,
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_search` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `online_user_id_ident_idx` (`user_id`,`ident`(25)),
  KEY `online_ident_idx` (`ident`(25)),
  KEY `online_logged_idx` (`logged`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `online`
--

INSERT INTO `online` (`user_id`, `ident`, `logged`, `idle`, `csrf_token`, `prev_url`, `last_post`, `last_search`) VALUES
(1, '192.168.211.253', 1318571207, 0, 'c720053bef3d5da2b7f784da61346f3288084394', 'http://www.cdsj.edu.mo/news/index.php', NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表格式： `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster` varchar(200) NOT NULL DEFAULT '',
  `poster_id` int(10) unsigned NOT NULL DEFAULT '1',
  `poster_ip` varchar(39) DEFAULT NULL,
  `poster_email` varchar(80) DEFAULT NULL,
  `message` text,
  `hide_smilies` tinyint(1) NOT NULL DEFAULT '0',
  `posted` int(10) unsigned NOT NULL DEFAULT '0',
  `edited` int(10) unsigned DEFAULT NULL,
  `edited_by` varchar(200) DEFAULT NULL,
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `posts_topic_id_idx` (`topic_id`),
  KEY `posts_multi_idx` (`poster_id`,`topic_id`),
  KEY `posts_posted_idx` (`posted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 列出以下資料庫的數據： `posts`
--

INSERT INTO `posts` (`id`, `poster`, `poster_id`, `poster_ip`, `poster_email`, `message`, `hide_smilies`, `posted`, `edited`, `edited_by`, `topic_id`) VALUES
(1, 'admin', 2, '127.0.0.1', NULL, 'If you are looking at this (which I guess you are), the install of PunBB appears to have worked! Now log in and head over to the administration control panel to configure your forum.', 0, 1318467786, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- 資料表格式： `ranks`
--

CREATE TABLE IF NOT EXISTS `ranks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank` varchar(50) NOT NULL DEFAULT '',
  `min_posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 列出以下資料庫的數據： `ranks`
--

INSERT INTO `ranks` (`id`, `rank`, `min_posts`) VALUES
(1, 'New member', 0),
(2, 'Member', 10);

-- --------------------------------------------------------

--
-- 資料表格式： `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reported_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text,
  `zapped` int(10) unsigned DEFAULT NULL,
  `zapped_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_zapped_idx` (`zapped`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 列出以下資料庫的數據： `reports`
--


-- --------------------------------------------------------

--
-- 資料表格式： `search_cache`
--

CREATE TABLE IF NOT EXISTS `search_cache` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `ident` varchar(200) NOT NULL DEFAULT '',
  `search_data` text,
  PRIMARY KEY (`id`),
  KEY `search_cache_ident_idx` (`ident`(8))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `search_cache`
--


-- --------------------------------------------------------

--
-- 資料表格式： `search_matches`
--

CREATE TABLE IF NOT EXISTS `search_matches` (
  `post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `word_id` int(10) unsigned NOT NULL DEFAULT '0',
  `subject_match` tinyint(1) NOT NULL DEFAULT '0',
  KEY `search_matches_word_id_idx` (`word_id`),
  KEY `search_matches_post_id_idx` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `search_matches`
--

INSERT INTO `search_matches` (`post_id`, `word_id`, `subject_match`) VALUES
(1, 1, 0),
(1, 2, 0),
(1, 3, 0),
(1, 4, 0),
(1, 5, 0),
(1, 6, 0),
(1, 7, 0),
(1, 8, 0),
(1, 9, 0),
(1, 10, 0),
(1, 11, 0),
(1, 12, 0),
(1, 13, 0),
(1, 14, 0),
(1, 15, 0),
(1, 16, 0),
(1, 17, 0),
(1, 18, 0),
(1, 19, 0),
(1, 20, 0),
(1, 21, 0),
(1, 22, 0),
(1, 23, 0),
(1, 25, 1),
(1, 24, 1);

-- --------------------------------------------------------

--
-- 資料表格式： `search_words`
--

CREATE TABLE IF NOT EXISTS `search_words` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`word`),
  KEY `search_words_id_idx` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- 列出以下資料庫的數據： `search_words`
--

INSERT INTO `search_words` (`id`, `word`) VALUES
(1, 'you'),
(2, 'are'),
(3, 'looking'),
(4, 'this'),
(5, 'which'),
(6, 'guess'),
(7, 'the'),
(8, 'install'),
(9, 'punbb'),
(10, 'appears'),
(11, 'have'),
(12, 'worked'),
(13, 'now'),
(14, 'log'),
(15, 'and'),
(16, 'head'),
(17, 'over'),
(18, 'administration'),
(19, 'control'),
(20, 'panel'),
(21, 'configure'),
(22, 'your'),
(23, 'forum'),
(24, 'test'),
(25, 'post');

-- --------------------------------------------------------

--
-- 資料表格式： `subscriptions`
--

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 列出以下資料庫的數據： `subscriptions`
--


-- --------------------------------------------------------

--
-- 資料表格式： `topics`
--

CREATE TABLE IF NOT EXISTS `topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster` varchar(200) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `posted` int(10) unsigned NOT NULL DEFAULT '0',
  `first_post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_post` int(10) unsigned NOT NULL DEFAULT '0',
  `last_post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_poster` varchar(200) DEFAULT NULL,
  `num_views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `num_replies` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  `moved_to` int(10) unsigned DEFAULT NULL,
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `topics_forum_id_idx` (`forum_id`),
  KEY `topics_moved_to_idx` (`moved_to`),
  KEY `topics_last_post_idx` (`last_post`),
  KEY `topics_first_post_id_idx` (`first_post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 列出以下資料庫的數據： `topics`
--

INSERT INTO `topics` (`id`, `poster`, `subject`, `posted`, `first_post_id`, `last_post`, `last_post_id`, `last_poster`, `num_views`, `num_replies`, `closed`, `sticky`, `moved_to`, `forum_id`) VALUES
(1, 'admin', 'Test post', 1318467786, 1, 1318467786, 1, 'admin', 0, 0, 0, 0, NULL, 1);

-- --------------------------------------------------------

--
-- 資料表格式： `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT '3',
  `username` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `salt` varchar(12) DEFAULT NULL,
  `email` varchar(80) NOT NULL DEFAULT '',
  `title` varchar(50) DEFAULT NULL,
  `realname` varchar(40) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `jabber` varchar(80) DEFAULT NULL,
  `icq` varchar(12) DEFAULT NULL,
  `msn` varchar(80) DEFAULT NULL,
  `aim` varchar(30) DEFAULT NULL,
  `yahoo` varchar(30) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `signature` text,
  `disp_topics` tinyint(3) unsigned DEFAULT NULL,
  `disp_posts` tinyint(3) unsigned DEFAULT NULL,
  `email_setting` tinyint(1) NOT NULL DEFAULT '1',
  `notify_with_post` tinyint(1) NOT NULL DEFAULT '0',
  `auto_notify` tinyint(1) NOT NULL DEFAULT '0',
  `show_smilies` tinyint(1) NOT NULL DEFAULT '1',
  `show_img` tinyint(1) NOT NULL DEFAULT '1',
  `show_img_sig` tinyint(1) NOT NULL DEFAULT '1',
  `show_avatars` tinyint(1) NOT NULL DEFAULT '1',
  `show_sig` tinyint(1) NOT NULL DEFAULT '1',
  `access_keys` tinyint(1) NOT NULL DEFAULT '0',
  `timezone` float NOT NULL DEFAULT '0',
  `dst` tinyint(1) NOT NULL DEFAULT '0',
  `time_format` int(10) unsigned NOT NULL DEFAULT '0',
  `date_format` int(10) unsigned NOT NULL DEFAULT '0',
  `language` varchar(25) NOT NULL DEFAULT 'English',
  `style` varchar(25) NOT NULL DEFAULT 'Oxygen',
  `num_posts` int(10) unsigned NOT NULL DEFAULT '0',
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_search` int(10) unsigned DEFAULT NULL,
  `last_email_sent` int(10) unsigned DEFAULT NULL,
  `registered` int(10) unsigned NOT NULL DEFAULT '0',
  `registration_ip` varchar(39) NOT NULL DEFAULT '0.0.0.0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_note` varchar(30) DEFAULT NULL,
  `activate_string` varchar(80) DEFAULT NULL,
  `activate_key` varchar(8) DEFAULT NULL,
  `pun_bbcode_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `pun_bbcode_use_buttons` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `users_registered_idx` (`registered`),
  KEY `users_username_idx` (`username`(8))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 列出以下資料庫的數據： `users`
--

INSERT INTO `users` (`id`, `group_id`, `username`, `password`, `salt`, `email`, `title`, `realname`, `url`, `jabber`, `icq`, `msn`, `aim`, `yahoo`, `location`, `signature`, `disp_topics`, `disp_posts`, `email_setting`, `notify_with_post`, `auto_notify`, `show_smilies`, `show_img`, `show_img_sig`, `show_avatars`, `show_sig`, `access_keys`, `timezone`, `dst`, `time_format`, `date_format`, `language`, `style`, `num_posts`, `last_post`, `last_search`, `last_email_sent`, `registered`, `registration_ip`, `last_visit`, `admin_note`, `activate_string`, `activate_key`, `pun_bbcode_enabled`, `pun_bbcode_use_buttons`) VALUES
(1, 2, 'Guest', 'Guest', NULL, 'Guest', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 'English', 'Oxygen', 0, NULL, NULL, NULL, 0, '0.0.0.0', 0, NULL, NULL, NULL, 1, 1),
(2, 1, 'admin', '433a6f8bf4386a23b9c5cdc18b49af993d4e597a', 'Zq|<""ySRi\\2', 'comus@cdsj.edu.mo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 1, 1, 0, 1, 1, 0, 8, 0, 0, 0, 'Traditional_Chinese', 'Oxygen', 1, 1318467786, NULL, NULL, 1318467786, '127.0.0.1', 1318571201, NULL, NULL, NULL, 1, 1);
