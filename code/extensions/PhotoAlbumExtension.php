<?php

class PhotoAlbumExtension extends DataExtension {

    public function GetItems(ArrayList $photoset) {
        $photos = PhotoItem::get()->filter("PhotoAlbumID", $this->owner->ID);
        if ($photos) {
            foreach ($photos AS $photo) {
                if ($photo->getComponent("Photo")->exists()) {
                    $photoset->push($photo);
                } elseif ($photo->getComponent("VideoItem")->exists()) {
                    $photoset->push($photo);
                }
            }
        }
        return $photoset;
    }

}
