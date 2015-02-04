<?php

class Oembed_Custom extends Oembed {

    public static function handle_shortcode($arguments, $url, $parser, $shortcode) {
        $result = false;
        if (Director::is_site_url($url) && VideoEmbed::GetByURL($url)) {
            $result = VideoEmbed::GetByURL($url)->forTemplate();
        } else {
            $result = parent::handle_shortcode($arguments, $url, $parser, $shortcode);
        }
        return $result;
    }

}
