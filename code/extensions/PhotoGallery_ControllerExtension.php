<?php

class PhotoGallery_ControllerExtension extends Extension
{

    public function __construct()
    {
        parent::__construct();
        Requirements::block("photogallery/shadowbox/shadowbox.css");
        Requirements::block("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
        Requirements::block("photogallery/shadowbox/shadowbox.js");
        Requirements::block("photogallery/js/shadowbox_init.js");

        Requirements::CSS("silverstripe-video-embed/assests/javascript/shadowbox/shadowbox.css");
        Requirements::javascript("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
        Requirements::javascript("silverstripe-video-embed/assests/javascript/shadowbox/shadowbox.js");
        Requirements::javascriptTemplate('silverstripe-video-embed/assests/javascript/shadowbox_init.js', array());
    }
}
