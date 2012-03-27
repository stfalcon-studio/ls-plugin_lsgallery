ALTER TABLE `prefix_lsgallery_album` ADD `first_image_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL 
ALTER TABLE `prefix_lsgallery_album` ADD `last_image_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL 

ALTER TABLE `prefix_lsgallery_album` ADD INDEX ( `first_image_id` ) 
ALTER TABLE `prefix_lsgallery_album` ADD INDEX ( `last_image_id` ) 

ALTER TABLE `prefix_lsgallery_album` ADD FOREIGN KEY ( `first_image_id` ) REFERENCES `prefix_lsgallery_image` (
`image_id`
) ON DELETE SET NULL ON UPDATE CASCADE ;
ALTER TABLE `prefix_lsgallery_album` ADD FOREIGN KEY ( `last_image_id` ) REFERENCES `prefix_lsgallery_image` (
`image_id`
) ON DELETE SET NULL ON UPDATE CASCADE ;
