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
class VideoEmbedExtension extends DataExtension
{

    public function GetThumbHeight($y = 120)
    {
        $height = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailHeight;
        if ($height != 0) {
            $y = $height;
        }
        return $y;
    }

    public function GetThumbWidth($x = 120)
    {
        $width = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailWidth;
        if ($width != 0) {
            $x = $width;
        }
        return $x;
    }

    public function GetThumbCropped($x = 120, $y = 120)
    {
        $width  = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailWidth;
        $height = $this->getComponent('PhotoAlbum')->PhotoGallery()->PhotoThumbnailHeight;
        if ($width != 0) {
            $x      = $width;
        }
        if ($height != 0) {
            $y      = $height;
        }
        return $this->Photo()->CroppedImage($x, $y);
    }
}
