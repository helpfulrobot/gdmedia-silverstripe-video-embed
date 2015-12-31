<?php

/**
 * Description of VideoHtmlEditorField
 *
 * @author corey
 */
class HtmlEditorField_Toolbar_Extension extends HtmlEditorField_Toolbar
{

    private static $allowed_actions = array(
        'LinkForm',
        'MediaForm',
        'viewfile',
        'getanchors'
    );

    public function viewfile($request)
    {
        $result  = false;
        if ($origUrl = $request->getVar('FileURL')) {
            if (Director::is_site_url($origUrl) && VideoEmbed::GetByURL($origUrl)) {
                $video  = VideoEmbed::GetByURL($origUrl);
                $result = $this->GetResultForVideo($video);
            }
        } elseif ($fileId = $request->getVar('ID')) {
            $video  = VideoEmbed::get()->filter(array("HTML5VideoID" => $fileId))->first();
            $result = $this->GetResultForVideo($video);
        }

        return $result ? $result : parent::viewfile($request);
    }

    protected function GetResultForVideo($video)
    {
        $result = false;
        if ($video && $video->exists() && $video->HTML5Video()->exists()) {
            $fileURL     = $video->HTML5Video()->GetURL();
            $fileWrapper = new HtmlEditorField_Embed($fileURL, $video);
            $refObject   = new ReflectionObject($fileWrapper);
            $refProperty = $refObject->getProperty('oembed');
            $refProperty->setAccessible(true);
            $refProperty->setValue($fileWrapper, $video->GetOembed());
            $fields      = $this->getFieldsForFile($fileURL, $fileWrapper);
            $this->extend('updateFieldsForFile', $fields, $fileURL, $fileWrapper);
            $result      = $fileWrapper->customise(array(
                        'Fields' => $fields,
                    ))->renderWith($this->templateViewFile);
        }
        return $result;
    }
}
