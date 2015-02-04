<?php

/**
 * Description of VideoHtmlEditorField
 *
 * @author corey
 */
class HtmlEditorField_Toolbar_Extension extends HtmlEditorField_Toolbar {

    private static $allowed_actions = array(
        'LinkForm',
        'MediaForm',
        'viewfile',
        'getanchors'
    );

    public function viewfile($request) {
        $result  = false;
        if ($origUrl = $request->getVar('FileURL')) {
            if (Director::is_site_url($origUrl) && VideoEmbed::GetByURL($origUrl)) {
                $video       = VideoEmbed::GetByURL($origUrl);
                $fileWrapper = new HtmlEditorField_Embed($origUrl, $video);
                $refObject   = new ReflectionObject($fileWrapper);
                $refProperty = $refObject->getProperty('oembed');
                $refProperty->setAccessible(true);
                $refProperty->setValue($fileWrapper, $video->GetOembed());
                $fields      = $this->getFieldsForFile($origUrl, $fileWrapper);
                $this->extend('updateFieldsForFile', $fields, $origUrl, $fileWrapper);
                $result      = $fileWrapper->customise(array(
                            'Fields' => $fields,
                        ))->renderWith($this->templateViewFile);
            }
        }
        return $result ? $result : parent::viewfile($request);
    }

}
