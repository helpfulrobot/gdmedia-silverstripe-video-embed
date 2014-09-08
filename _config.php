<?php

Object::add_extension('Page', 'VideoEmbedPageExtesion');
Object::add_extension('Oembed_Result', 'Oembed_ResultExtension');


if (class_exists("PhotoAlbum") && class_exists("PhotoAlbumExtension")) {
    Object::add_extension('PhotoAlbum', 'PhotoAlbumExtension');
}
if (class_exists("PhotoGallery_Controller") && class_exists("PhotoGallery_ControllerExtension")) {
    Object::add_extension('PhotoGallery_Controller', 'PhotoGallery_ControllerExtension');
}

if (class_exists("PhotoItem") && class_exists("PhotoItemExtension")) {
    Object::add_extension('PhotoItem', 'PhotoItemExtension');
    Object::add_extension('VideoEmbed', 'VideoEmbedExtension');
}