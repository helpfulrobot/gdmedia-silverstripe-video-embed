<?php

/**
 * @property Oembed_Result $owner
 */
class Oembed_ResultExtension extends DataExtension {

    public function toJson() {
        $res = false;
        if ($this->owner->exists()) {
            // I could not seem to find a way to access the protected data propery of Oembed_Result
            // This works.... seems a little hacky
            $reflection = new ReflectionClass($this->owner);
            $property   = $reflection->getProperty("data");
            $property->setAccessible(true);
            $res        = Convert::raw2json($property->getValue($this->owner));
        }
        return $res;
    }

}
