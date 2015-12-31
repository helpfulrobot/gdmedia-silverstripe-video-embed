<?php

class videoEmbedController extends Controller
{

    private static $allowed_actions = array('getOembedData');

    public function GetOembedData(SS_HTTPRequest $request)
    {
        $response = "{}";
        $this->getResponse()->addHeader("Content-Type", "application/json; charset=utf-8");
        $url      = $request->postVar('url') ? $request->postVar('url') : $request->getVar("mediaurl");
        if (Director::is_site_url($url) && VideoEmbed::GetByURL($url)) {
            $video    = VideoEmbed::GetByURL($url);
            $response = $video->GetOembedJson();
        } else {
            $oembed = Oembed::get_oembed_from_url($url);
            if ($oembed && $oembed->exists()) {
                $response = $oembed->toJson();
            }
        }
        echo $response;
    }

    public function index(SS_HTTPRequest $request)
    {
        return $this->GetOembedData($request);
    }
}
