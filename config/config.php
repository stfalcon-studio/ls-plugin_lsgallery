<?php

if (!class_exists('Config'))
    die('Hacking attempt!');

Config::Set('router.page.gallery', 'PluginLsgallery_ActionGallery');
Config::Set('router.page.galleryajax', 'PluginLsgallery_ActionAjax');

Config::Set('path.uploads.lsgallery_images', Config::Get('path.uploads.images') . '/lsgallery'); // путь для загрузки картинок

Config::Set('db.table.lsgallery.album', '___db.table.prefix___lsgallery_album');
Config::Set('db.table.lsgallery.image', '___db.table.prefix___lsgallery_image');
Config::Set('db.table.lsgallery.image_tag', '___db.table.prefix___lsgallery_image_tag');
Config::Set('db.table.lsgallery.image_read', '___db.table.prefix___lsgallery_image_read');
Config::Set('db.table.lsgallery.image_user', '___db.table.prefix___lsgallery_image_user');

Config::Set('acl.vote.image.limit_time', 60*60*24*20); // время голосования за картинку
Config::Set('acl.vote.image.rating', -1); // мин карма для голосования за картинку

Config::Set('module.image.lsgallery.jpg_quality', 95);

Config::Set('block.rule_stream_gallery', array(
    'action'  => array(
        'gallery' => array(
            'photo', 'image', 'albums'
            )
    ),
    'blocks'  => array(
        'right' => array(
            'StreamGallery'=>array(
                'params' => array('plugin' => 'lsgallery'),
                'priority'=>100)
            )
    ),
));

// Settings for plugin Sitemap

Config::Set('sitemap', array(
    'cache_lifetime' => 60 * 60 * 24, // 24 hours
    'sitemap_priority' => '0.8',
    'sitemap_changefreq' => 'monthly'
));

Config::Set('block.rule_tags_gallery', array(
    'action'  => array(
        'gallery' => array(
            'photo', 'album', 'albums'
            )
    ),
    'blocks'  => array(
        'right' => array(
            'GalleryTags'=>array(
                'params' => array('plugin' => 'lsgallery'),
                'priority'=>10)
            )
    ),
));

return array(
    'aldbum_create_rating' => false, // минимальный рейтинг для создания альбома, если false - не проверяется
    'images_new_time' => 60*60*24*1, // сколько времени картинка считается лучшей
    'images_best' => 1, // мин рейтинг для попадания картинки в топ
    'image_row' => 6, // кол-во изображений в блоке садйбара
    'images_random' => 4, // кол-во изображений в блоке случайные картинки
    'album_block' => 4, // кол-во изображений в блоке альбомов
    'image_per_page' => 25, // изображений на страницу
    'album_per_page' => 12, // альбомов на страницу
    'image_max_size' => 6 * 1024, //Kb  // максимально допустимый размер фото
    'count_image_max' => 100, // макс колво картинок пользователя
    'priority_album_block' => 110,
    'size' => array(array(  // параметры ресайза изображения
            'w' => 638,
            'h' => null,
            'crop' => false,
        ),
        array(
            'w' => 100,
            'h' => 100,
            'crop' => true,
        ),
        array(
            'w' => 40,
            'h' => 40,
            'crop' => true,
        )
    ),
);
