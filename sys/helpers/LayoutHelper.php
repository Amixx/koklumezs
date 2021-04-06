<?php

namespace app\helpers;

class LayoutHelper
{
    protected $school = null;

    public function getWrapperBackground()
    {
        return $this->school && $this->school->background_image
            ? "url(" . $this->school->background_image . ")"
            : "white";
    }

    public function getLogo()
    {
        return $this->school && $this->school->logo ? "url(" . $this->school->logo . ")" : "white";
    }
}
