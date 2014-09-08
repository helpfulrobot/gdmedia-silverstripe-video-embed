<?php

class videoEmbedController extends Controller {

    private static $allowed_actions = array('getOembedData');
    private static $url_handlers    = array(
        'getOembedData/!Url' => 'getOembedData'
    );

    public function GetOembedData(SS_HTTPRequest $request) {
        $url    = $request->postVar('url');
        $oembed = Oembed::get_oembed_from_url($url);
        $this->getResponse()->addHeader("Content-Type", "application/json; charset=utf-8");
        echo $oembed->toJson();
    }

}
