<?php

class PhotoItemExtension extends DataExtension {

    static $db                        = array(
        "Type" => "Enum(array('Photo', 'Video'))",
    );
    static $has_one                   = array(
        "VideoItem" => "VideoEmbed"
    );
    private static $searchable_fields = array(
        'VideoItem.Title',
        'Photo.Caption'
    );
    private static $singular_name     = "Item";
    private static $plural_name       = "Items";

    public function updateCMSFields(FieldList $fields) {
        Requirements::javascript('silverstripe-video-embed/assests/javascript/PhotoItemExtension.js');
        $typeField  = new DropdownField(
                'Type', 'Type', singleton('PhotoItem')->dbObject('Type')->enumValues());
        $fields->insertBefore($typeField, 'Photo');
        $videoField = new DropdownField("VideoItemID", "Video", VideoEmbed::get()->map("ID", "Title"));
        $fields->insertBefore($videoField, 'Caption');
    }

    public function Thumbnail() {
        $res       = null;
        /* @var $videoItem VideoEmbed */
        $videoItem = $this->owner->VideoItem();
        if ($videoItem && $videoItem->exists()) {
            $res = $videoItem->CMSThumbnail();
        }
        return $res;
    }

    public function ThumbStyle($x = 120, $y = 120) {
        $width  = $this->owner->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailWidth;
        $height = $this->owner->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailHeight;
        if ($width != 0){
            $x      = $width;
        }
        if ($height != 0){
            $y      = $height;
        }
        return "display: inline-block; width: ${x}px; height: ${y}px; overflow: hidden;";
    }

    public function VideoRel($x = 700, $y = 700) {
        $width  = $this->owner->getComponent('PhotoAlbum')->PhotoGallery()->PhotoFullWidth;
        $height = $this->owner->getComponent('PhotoAlbum')->PhotoGallery()->PhotoFullHeight;
         if ($width != 0){
            $x      = $width;
        }
        if ($height != 0){
            $y      = $height;
        }
        return "height=${x};width=${y}";
    }

}
