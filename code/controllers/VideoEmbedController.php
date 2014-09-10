<?php

class videoEmbedController extends Controller {

    private static $allowed_actions = array('getOembedData');
    private static $url_handlers    = array(
        'getOembedData/!Url' => 'getOembedData'
    );

    public function GetOembedData(SS_HTTPRequest $request) {
        $response = "{}";
        $url      = $request->postVar('url');
        $oembed   = Oembed::get_oembed_from_url($url);
        if ($oembed->exists()) {
            $this->getResponse()->addHeader("Content-Type", "application/json; charset=utf-8");
            $response = $oembed->toJson();
        }
        echo $response;
    }

}
