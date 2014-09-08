<?php

class VideoEmbedPageExtesion extends DataExtension {

    /**
     * @example in template: $FlexSlider(2, 960, 450)
     */
    public function VideoEmbed($ID = 1, $width = null, $height = null, $autoplay = null) {
        /* @var $VideoEmbed VideoEmbed */
        $VideoEmbed = is_numeric($ID) ? VideoEmbed::get()->byID($ID) : VideoEmbed::get()->where("Title LIKE '" . $ID . "'")->First();

        if ($width) {
            $VideoEmbed->setWidth($width);
        }
        if ($height) {
            $VideoEmbed->setHeight($height);
        }
        if (!is_null($autoplay)) {
            $VideoEmbed->setAutoPlay($autoplay);
        }
        return $VideoEmbed;
    }

}
