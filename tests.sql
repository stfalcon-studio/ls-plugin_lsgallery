ALTER TABLE `prefix_comment` MODIFY target_type ENUM('image', 'topic', 'talk') DEFAULT 'topic';

ALTER TABLE `prefix_favourite` MODIFY target_type ENUM('image', 'topic', 'comment', 'talk') DEFAULT 'topic';

ALTER TABLE `prefix_vote` MODIFY target_type ENUM('image', 'topic', 'blog', 'user', 'comment') DEFAULT 'topic';
