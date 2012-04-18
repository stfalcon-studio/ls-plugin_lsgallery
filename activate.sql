CREATE TABLE IF NOT EXISTS `prefix_lsgallery_album` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_user_id` int(11) unsigned NOT NULL,
  `album_title` varchar(200) NOT NULL,
  `album_description` text NOT NULL,
  `album_type` enum('personal','open','friend') NOT NULL DEFAULT 'open',
  `album_date_add` datetime NOT NULL,
  `album_date_edit` datetime NOT NULL,
  `album_cover_image_id` int(11) unsigned DEFAULT NULL,
  `image_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`),
  KEY `album_user_id` (`album_user_id`),
  KEY `album_cover_image_id` (`album_cover_image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_lsgallery_album`
  ADD CONSTRAINT `prefix_lsgallery_album_ibfk_1` FOREIGN KEY (`album_user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_image` (
  `image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `album_id` int(11) unsigned NOT NULL,
  `image_description` text NOT NULL,
  `image_tags` varchar(200) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `image_date_add` datetime NOT NULL,
  `image_date_edit` datetime DEFAULT NULL,
  `image_count_comment` int(11) NOT NULL DEFAULT '0',
  `image_rating` float(9,3) NOT NULL DEFAULT '0.000',
  `image_count_vote` int(11) unsigned NOT NULL DEFAULT '0',
  `image_count_favourite` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`),
  KEY `album_id` (`album_id`),
  KEY `user_id` (`user_id`),
  KEY `next_image_id` (`next_image_id`),
  KEY `prev_image_id` (`prev_image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci ;

ALTER TABLE `prefix_lsgallery_image`
  ADD CONSTRAINT `prefix_lsgallery_image_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_lsgallery_image_ibfk_2` FOREIGN KEY (`album_id`) REFERENCES `prefix_lsgallery_album` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `prefix_lsgallery_album`
  ADD CONSTRAINT `prefix_lsgallery_album_ibfk_1` FOREIGN KEY (`album_user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_lsgallery_album_ibfk_2` FOREIGN KEY (`album_cover_image_id`) REFERENCES `prefix_lsgallery_image` (`image_id`) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_image_tag` (
  `image_tag_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `image_id` int(11) unsigned NOT NULL,
  `image_tag_text` varchar(50) NOT NULL,
  PRIMARY KEY (`image_tag_id`),
  KEY `image_id` (`image_id`),
  KEY `image_tag_text` (`image_tag_text`),
  KEY `album_id` (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci ;

ALTER TABLE `prefix_lsgallery_image_tag`
  ADD CONSTRAINT `prefix_lsgallery_image_tag_ibfk_2` FOREIGN KEY (`album_id`) REFERENCES `prefix_lsgallery_album` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_lsgallery_image_tag_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `prefix_lsgallery_image` (`image_id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_image_read` (
  `image_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `date_read` datetime NOT NULL,
  `comment_count_last` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_id_last` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `image_id` (`image_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


ALTER TABLE `prefix_lsgallery_image_read`
  ADD CONSTRAINT `prefix_lsgallery_image_read_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `prefix_lsgallery_image` (`image_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_lsgallery_image_read_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_image_user` (
  `image_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `target_user_id` int(11) unsigned NOT NULL,
  `lasso_x` int(3) unsigned NOT NULL,
  `lasso_y` int(3) unsigned NOT NULL,
  `lasso_w` int(3) unsigned NOT NULL,
  `lasso_h` int(3) unsigned NOT NULL,
  `status` enum('new','confirmed','declined') NOT NULL DEFAULT 'new',
  UNIQUE KEY `image_id` (`image_id`,`target_user_id`),
  KEY `user_id` (`user_id`),
  KEY `target_user_id` (`target_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_lsgallery_image_user`
  ADD CONSTRAINT `prefix_lsgallery_image_user_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `prefix_lsgallery_image` (`image_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_lsgallery_image_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_lsgallery_image_user_ibfk_3` FOREIGN KEY (`target_user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
