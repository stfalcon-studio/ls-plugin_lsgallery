<?php

if (!class_exists('Config'))
    die('Hacking attempt!');

Config::Set('router.page.gallery', 'PluginLsgallery_ActionGallery');
Config::Set('router.page.galleryajax', 'PluginLsgallery_ActionAjax');

Config::Set('path.uploads.lsgallery_images', Config::Get('path.uploads.images') . '/lsgallery');

Config::Set('db.table.lsgallery.album', '___db.table.prefix___lsgallery_album');
Config::Set('db.table.lsgallery.image', '___db.table.prefix___lsgallery_image');
Config::Set('db.table.lsgallery.image_tag', '___db.table.prefix___lsgallery_image_tag');
Config::Set('db.table.lsgallery.image_read', '___db.table.prefix___lsgallery_image_read');
Config::Set('db.table.lsgallery.image_user', '___db.table.prefix___lsgallery_image_user');

Config::Set('acl.vote.image.limit_time', 60*60*24*20);
Config::Set('acl.vote.image.rating', -1);

Config::Set('module.image.lsgallery.jpg_quality', 95);

return array(
    'images_new_time' => 60*60*24*1,
    'images_best' => 1,
    'image_row' => 6,
    'images_random' => 4,
    'album_block' => 4,
    'image_per_page' => 25,
    'album_per_page' => 5,
    'image_max_size' => 6 * 1024, //Kb  // максимально допустимый размер фото
    'count_image_max' => 100,
    'size' => array(array(
            'w' => 600,
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