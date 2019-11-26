<?php

namespace PWRDK\CustomAttributes;

use PWRDK\CustomAttributes\CustomAttributes as CustomAttributesManager;
use PWRDK\CustomAttributes\Models\CustomAttribute;

trait HasCustomAttributes
{
    public function customAttributes()
    {
        return $this->morphMany(CustomAttribute::class, 'attributable');
    }

    public function attr($handle = false)
    {
        return new CustomAttributesManager($this, $handle);
    }
}
