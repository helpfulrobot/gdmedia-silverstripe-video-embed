<?php

/**
 *
 * @property string $Code Code for Vimeo or YouTube Video ( emtpy if HTML5Video is used )
 * @property string $Title Title for this video
 * @property string $Type Type of this video  YouTube, Vimeo, HTML 5/Flash
 * @property string $ThumbnailURL URL to this videos Thumbnail ( empty if ThumbnailFile is used )
 * @property Image $ThumbnailFile Associated Thumbnail image ( empty if ThumbnailURL is used )
 * @property File $HTML5Video Associated video file ( empty if Code is used )
 */
class VideoEmbed extends DataObject {

    static $singular_name             = 'Video';
    static $plural_name               = 'Videos';
    private static $db                = array(
        "Code"         => "Text",
        "Title"        => "Varchar(255)",
        "Type"         => "Enum(array('YouTube', 'Vimeo','Dailymotion', 'HTML 5/Flash'))",
        "ThumbnailURL" => "Text"
    );
    private static $has_one           = array(
        'HTML5Video'    => 'File',
        'ThumbnailFile' => 'Image'
    );
    private static $searchable_fields = array(
        'ID',
        'Type',
        'Code',
        'Title'
    );
    private static $summary_fields    = array(
        'Type',
        'Code',
        'Title',
        'Thumbnail' => 'Thumbnail'
    );
    private $width                    = 480;
    private $height                   = 270;
    private $autoPlay                 = false;

    /**
     *
     * @var VideoEmbedSettings
     */
    private $settings = false;

    function GetThumbURL() {
        $res = "";
        if ($this->ThumbnailURL) {
            $res = $this->ThumbnailURL;
        } else if ($this->ThumbnailFile()) {
            $res = $this->ThumbnailFile()->getURL();
        }
        return $res;
    }

    function Thumbnail() {
        $res = HTMLText::create();
        $res->setValue("<em>No thumbnail found</em>");
        if ($this->GetThumbURL()) {
            $res->setValue("<img src='" . $this->GetThumbURL() . "' style='max-width: 120px; height: auto;' />");
        }
        return $res;
    }

    function CMSThumbnail() {
        return $this->Thumbnail();
    }

    public function getCMSFields() {
        Requirements::css('silverstripe-video-embed/assests/css/VideoEmbedEditor.css');
        Requirements::javascript('silverstripe-video-embed/assests/javascript/urlParser.min.js');
        Requirements::javascript('silverstripe-video-embed/assests/javascript/VideoEmbedEditor.js');
        Requirements::backend()->customScript("var videoEmbedTypes = " . Convert::raw2json($this->GetVideoTypes()) . ";");
        $Fields = parent::getCMSFields();
        $Fields->removeByName('Code');
        $Fields->removeByName('HTML5Video');
        $Fields->removeByName('ThumbnailURL');
        $Fields->removeByName('ThumbnailFile');

        $CodeField  = new TextField("Code", "Code");
        $CodeField->setDescription('
            Copy and paste your Video URL here<br/>
            eg</br>
            &nbsp;&nbsp;https://www.youtube.com/watch?v=cRi5Hh7RU34</br>
            &nbsp;&nbsp;http://vimeo.com/93104916</br>
            &nbsp;&nbsp;http://www.dailymotion.com/video/x1no7nq_jquery-and-ajax-tutorials-45-ajax-login-example_tech</br>
        ');
        $Fields->addFieldToTab('Root.Main', $CodeField);
        $TitleField = new TextField("Title", "Title");
        $Fields->addFieldToTab('Root.Main', $TitleField);

        $HTML5VideoField                                    = new UploadField('HTML5Video', 'Video file'); //::create("HTML5Video")->setTitle("Album Cover Photo");
        $HTML5VideoField->getValidator()->allowedExtensions = array("webm", "ogg", "mp4", "flv");
        $HTML5VideoField->setAllowedMaxFileNumber(1);

        $Fields->addFieldToTab('Root.Main', $HTML5VideoField);

        $ThumbnailUploadField                                    = new UploadField('ThumbnailFile', '');
        $ThumbnailUploadField->getValidator()->allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
        $ThumbnailUploadField->setAllowedMaxFileNumber(1);
        $thumbnailField                                          = new SelectionGroup('ThumbnailGroup', array(
            SelectionGroup_Item::create('URL', array(LiteralField::create('ThumbPreview', '
                <div class="ss-uploadfield ">
                    <div class="ss-uploadfield-item ss-uploadfield-addfile middleColumn">
                        <div class="ss-uploadfield-item-preview ss-uploadfield-dropzone ui-corner-all" style="display: block; margin: 0" id="ThumbnailURLHolder">
                            Enter a URL
        		</div>
                        <img src="" alt="Failed to load preview" style="display:none; width: 60px;" id="ThumbnailURLPreview"/>
                '),
                TextField::create('ThumbnailURL', ''),
                LiteralField::create('ThumbPreview', '
                    </div>
                </div>')
            )),
            SelectionGroup_Item::create('File', $ThumbnailUploadField)
        ));
        $thumbnailField->setValue("URL");
        if ($this->ThumbnailURL) {
            $thumbnailField->setValue("URL");
        } else if ($this->ThumbnailFile()->exists()) {
            $thumbnailField->setValue("File");
        }
        $fields[] = LiteralField::create("guestLabel", '<div id="Thumbnail" class="field text">
            <label class="left" for="Form_ItemEditForm_Value">Thumbnail</label>
            <div class="middleColumn">');
        $fields[] = $thumbnailField;
        $fields[] = LiteralField::create("guestLabel", ' <input type="text" name="Value" class="text" id="Form_ItemEditForm_Value">
            </div>
        </div>');
        $Fields->addFieldsToTab('Root.Main', $fields);

        return $Fields;
    }

    public function GetVideoTypes() {
        return Config::inst()->get('VideoEmbed', 'VideoTypes');
    }

    /**
     *
     * @return VideoEmbedSettings
     */
    public function GetSettings() {
        if (!$this->settings) {
            $this->settings = new VideoEmbedSettings();
            foreach ($this->GetVideoTypes() as $videoType) {
                if (isset($videoType['label']) && $videoType['label'] == $this->Type) {
                    foreach ($videoType as $key => $value) {
                        $this->settings->$key = str_replace("{CODE}", $this->Code, $value);
                    }
                    break;
                }
            }
        }
        return $this->settings;
    }

    public function GetEmbedCode() {
        $res = false;
        if ($this->Type == 'HTML 5/Flash' && $this->HTML5Video()->exists()) {
            $res = $this->renderWith("VideoEmbedHTML5");
        } else {
            $oembed = $this->GetOembed();
            if ($oembed) {
                $res = $oembed->forTemplate();
            } else {
                $res = '<pre class="debug"> "$oembed"' . PHP_EOL . print_r($oembed, true) . PHP_EOL . '</pre>';
            }
        }
        return $res;
    }

    public function GetUrl() {
        $url = false;
        if ($this->Type == 'HTML 5/Flash' && $this->HTML5Video()->exists()) {
            $url = $this->HTML5Video()->GetURL();
        } else {
            $url = $this->GetSettings()->url;
        }
        return $url;
    }

    public function GetEmbedUrl($width = null, $height = null, $autoplay = null) {
        if (!is_null($width)) {
            $this->setWidth($width);
        }
        if (!is_null($height)) {
            $this->setHeight($height);
        }
        if (!is_null($autoplay)) {
            $this->setAutoPlay($autoplay);
        }
        $url = false;
        if ($this->Type == 'HTML 5/Flash' && $this->HTML5Video()->exists()) {
            $url = $this->HTML5Video()->GetURL();
        } else {
            $match = array();
            if (preg_match('/src="([^"]+)"/', $this->GetOembed()->getField("html"), $match)) {
                $url = $match[1];
                if ($this->autoPlay) {
                    $query     = array();
                    $parsedUrl = parse_url($url);
                    if (!isset($parsedUrl["query"])) {
                        $parsedUrl["query"] = "";
                    }
                    if (!isset($parsedUrl["scheme"])) {
                        $parsedUrl["scheme"] = "http" . ( isset($_SERVER['HTTPS']) ? 's' : '');
                    }
                    parse_str($parsedUrl["query"], $query);
                    if (!isset($query["autoplay"])) {
                        $query["autoplay"] = 1;
                    }
                    $query["rel"] = 0;
                    $url          = $parsedUrl['scheme'] . '://'
                            . ((isset($parsedUrl['user'])) ? $parsedUrl['user'] . ((isset($parsedUrl['pass'])) ? ':' . $parsedUrl['pass'] : '') . '@' : '')
                            . ((isset($parsedUrl['host'])) ? $parsedUrl['host'] : '')
                            . ((isset($parsedUrl['port'])) ? ':' . $parsedUrl['port'] : '')
                            . ((isset($parsedUrl['path'])) ? $parsedUrl['path'] : '')
                            . '?' . http_build_query($query)
                            . ((isset($parsedUrl['fragment'])) ? '#' . $parsedUrl['fragment'] : '');
                }
            }
        }
        return $url;
    }

    /**
     *
     * @returns Oembed_Result/bool An Oembed descriptor, or false
     */
    public function GetOembed() {
        $res = false;
        if ($this->Type !== 'HTML 5/Flash' && $this->GetUrl()) {
            $options = array();
            if ($this->getWidth()) {
                $options['width'] = $this->getWidth();
            }
            if ($this->getHeight()) {
                $options['height'] = $this->getHeight();
            }
            $options['autoplay'] = $this->getAutoPlay() ? 1 : 0;
            $res                 = Oembed::get_oembed_from_url($this->GetSettings()->url, false, $options);
        }
        return $res;
    }
    public function GetMimeType() {
        $res = false;
        if ($this->HTML5Video()) {
            $res = HTTP::get_mime_type($this->HTML5Video()->getFilename());

            if ($res == "video/x-flv") {
                $res = "video/flv";
            }
        }
        return $res;
    }

    public function GetSetupData() {
        $settings = array();
        if ($this->GetSettings()->pluginTech) {
            $settings[] = '"techOrder": ["' . $this->GetSettings()->pluginTech . '"]';
        }
        if ($this->GetSettings()->url) {
            $settings[] = '"src": "' . $this->GetSettings()->url . '"';
        }
        return '{' . implode(", ", $settings) . '}';
    }

    public function forTemplate() {
        Requirements::css('silverstripe-video-embed/assests/javascript/video-js/video-js.min.css');
        Requirements::javascript('silverstripe-video-embed/assests/javascript/video-js/video.js');
        Requirements::javascriptTemplate('silverstripe-video-embed/assests/javascript/VideoEmbedSWFTemplate.js', array("videoembed_swf_file" => Director::absoluteBaseURL() . 'silverstripe-video-embed/assests/javascript/video-js/video-js.swf'), 'VideoEmbed');
        if ($this->GetSettings()->pluginFile) {
            Requirements::javascript('silverstripe-video-embed/assests/javascript/video-js/plugins/' . $this->GetSettings()->pluginFile);
        }
        return $this->renderWith('VideoEmbed');
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getAutoPlay() {
        return $this->autoPlay;
    }

    public function setWidth($width) {
        $this->width = $width;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function setAutoPlay($autoPlay) {
        $this->autoPlay = $autoPlay;
    }

}

class VideoEmbedSettings {

    public $label      = "";
    public $hide       = "";
    public $show       = "";
    public $url        = "";
    public $pluginFile = "";
    public $pluginTech = "";

}
