# UPDATE to v0.3.0

Для апдейта необходимо полностью удалить текущую версию галереи на сайте и залить новую.
Исполнить запрос к БД:
```  ALTER TABLE `prefix_lsgallery_image`
	ADD `image_count_vote_up` INT NOT NULL DEFAULT '0' AFTER `image_count_vote` ,
	ADD `image_count_vote_down` INT NOT NULL DEFAULT '0' AFTER `image_count_vote_up` ,
	ADD `image_count_vote_abstain` INT NOT NULL DEFAULT '0' AFTER `image_count_vote_down`;```

Перейти в админку и сделать перерасчет избранного и голосов:
  - Пересчитать данные для фото (избраннои и голоса)

# Update to v0.1.1

Изменился процес загрузки файлов, просчет next|prev id для фото.
Выполнить запросы:
  ALTER TABLE `prefix_lsgallery_image` DROP FOREIGN KEY `prefix_lsgallery_image_ibfk_3` ;

  ALTER TABLE `prefix_lsgallery_image` DROP FOREIGN KEY `prefix_lsgallery_image_ibfk_4` ;

	ALTER TABLE `prefix_lsgallery_image`
	  DROP `next_image_id`,
	  DROP `prev_image_id`;

