SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS prefix_lsgallery_image_read;
DROP TABLE IF EXISTS prefix_lsgallery_image_tag;
DROP TABLE IF EXISTS prefix_lsgallery_image;
DROP TABLE IF EXISTS prefix_lsgallery_album;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_album` (
    `album_id`             INT(11) UNSIGNED                             NOT NULL AUTO_INCREMENT,
    `album_user_id`        INT(11) UNSIGNED                             NOT NULL,
    `album_title`          VARCHAR(200)                                 NOT NULL,
    `album_description`    TEXT                                         NOT NULL,
    `album_type`           ENUM('personal', 'open', 'friend', 'shared') NOT NULL DEFAULT 'open',
    `album_date_add`       DATETIME                                     NOT NULL,
    `album_date_edit`      DATETIME                                     NOT NULL,
    `album_cover_image_id` INT(11) UNSIGNED                                      DEFAULT NULL,
    `image_count`          INT(10) UNSIGNED                             NOT NULL DEFAULT '0',
    PRIMARY KEY (`album_id`),
    KEY `album_user_id` (`album_user_id`),
    KEY `album_cover_image_id` (`album_cover_image_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_image` (
    `image_id`                 INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`                  INT(11) UNSIGNED NOT NULL,
    `album_id`                 INT(11) UNSIGNED NOT NULL,
    `image_description`        TEXT             NOT NULL,
    `image_tags`               VARCHAR(200)     NOT NULL,
    `image_filename`           VARCHAR(255)     NOT NULL,
    `image_date_add`           DATETIME         NOT NULL,
    `image_date_edit`          DATETIME                  DEFAULT NULL,
    `image_count_comment`      INT(11)          NOT NULL DEFAULT '0',
    `image_rating`             FLOAT(9, 3)      NOT NULL DEFAULT '0.000',
    `image_count_vote`         INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `image_count_vote_up`      INT              NOT NULL DEFAULT '0',
    `image_count_vote_down`    INT              NOT NULL DEFAULT '0',
    `image_count_vote_abstain` INT              NOT NULL DEFAULT '0',
    `image_count_favourite`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`image_id`),
    KEY `album_id` (`album_id`),
    KEY `user_id` (`user_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE utf8_general_ci;

ALTER TABLE `prefix_lsgallery_image`
ADD CONSTRAINT `prefix_lsgallery_image_ibfk_2` FOREIGN KEY (`album_id`) REFERENCES `prefix_lsgallery_album` (`album_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `prefix_lsgallery_album`
ADD CONSTRAINT `prefix_lsgallery_album_ibfk_1` FOREIGN KEY (`album_user_id`) REFERENCES `prefix_user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
ADD CONSTRAINT `prefix_lsgallery_album_ibfk_2` FOREIGN KEY (`album_cover_image_id`) REFERENCES `prefix_lsgallery_image` (`image_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_image_tag` (
    `image_tag_id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `album_id`       INT(11) UNSIGNED NOT NULL,
    `image_id`       INT(11) UNSIGNED NOT NULL,
    `image_tag_text` VARCHAR(50)      NOT NULL,
    PRIMARY KEY (`image_tag_id`),
    KEY `image_id` (`image_id`),
    KEY `image_tag_text` (`image_tag_text`),
    KEY `album_id` (`album_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE utf8_general_ci;

ALTER TABLE `prefix_lsgallery_image_tag`
ADD CONSTRAINT `prefix_lsgallery_image_tag_ibfk_2` FOREIGN KEY (`album_id`) REFERENCES `prefix_lsgallery_album` (`album_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
ADD CONSTRAINT `prefix_lsgallery_image_tag_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `prefix_lsgallery_image` (`image_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `prefix_lsgallery_image_read` (
    `image_id`           INT(11) UNSIGNED NOT NULL,
    `user_id`            INT(11) UNSIGNED NOT NULL,
    `date_read`          DATETIME         NOT NULL,
    `comment_count_last` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `comment_id_last`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
    KEY `image_id` (`image_id`),
    KEY `user_id` (`user_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE utf8_general_ci;


ALTER TABLE `prefix_lsgallery_image_read`
ADD CONSTRAINT `prefix_lsgallery_image_read_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `prefix_lsgallery_image` (`image_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
ADD CONSTRAINT `prefix_lsgallery_image_read_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
