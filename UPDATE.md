Update to v0.1.1

Изменился процес загрузки файлов, просчет next|prev id для фото.
Исполнить запрос:

ALTER TABLE `prefix_lsgallery_image` DROP FOREIGN KEY `prefix_lsgallery_image_ibfk_3` ;

ALTER TABLE `prefix_lsgallery_image` DROP FOREIGN KEY `prefix_lsgallery_image_ibfk_4` ;

ALTER TABLE `prefix_lsgallery_image`
  DROP `next_image_id`,
  DROP `prev_image_id`;

Update to v0.1.2
Возможность запрета комментирвоать изображение
ALTER TABLE `prefix_lsgallery_image` ADD `image_forbid_comment` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';


