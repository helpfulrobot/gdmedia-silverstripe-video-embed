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

Object::useCustomClass('HtmlEditorField_Toolbar', 'HtmlEditorField_Toolbar_Extension', true);
$oembedProviders = Config::inst()->get('Oembed', 'providers');
$categories      = Config::inst()->get('File', 'app_categories');
$localProvider   = array("http" => Director::absoluteURL('/oembed.json'), "https" => Director::absoluteURL('/oembed.json'));
if (isset($categories['audio'])) {
    foreach ($categories['audio'] as $ext) {
        $oembedProviders['*' . ASSETS_DIR . '/*.' . $ext] = $localProvider;
    }
}
if (isset($categories['mov'])) {
    foreach ($categories['mov'] as $ext) {
        $oembedProviders['*' . ASSETS_DIR . '/*.' . $ext] = $localProvider;
    }
}
Config::inst()->update('Oembed', 'providers', $oembedProviders);


ShortcodeParser::get('default')->register('embed', array('Oembed_Custom', 'handle_shortcode'));
