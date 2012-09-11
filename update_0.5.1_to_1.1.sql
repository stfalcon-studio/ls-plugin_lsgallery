ALTER TABLE `prefix_lsgallery_image`
ADD `image_count_vote_up` INT NOT NULL DEFAULT '0' AFTER `image_count_vote` ,
ADD `image_count_vote_down` INT NOT NULL DEFAULT '0' AFTER `image_count_vote_up` ,
ADD `image_count_vote_abstain` INT NOT NULL DEFAULT '0' AFTER `image_count_vote_down`;