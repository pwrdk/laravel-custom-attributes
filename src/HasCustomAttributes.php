<?php

namespace PWRDK\CustomAttributes;

use PWRDK\CustomAttributes\CustomAttributes as CustomAttributesManager;
use PWRDK\CustomAttributes\Models\CustomAttribute;

trait HasCustomAttributes
{
    public function customAttributes()
    {
        if ($this instanceof HasLocalCustomAttributeType) {
            return $this->morphMany(\App\Models\CustomAttribute::class, 'attributable');
        } else {
            return $this->morphMany(CustomAttribute::class, 'attributable');
        }
    }

    public function attr($handle = null)
    {
        return (new CustomAttributesManager($this, $handle, \Auth::user()->id ?? null));
    }
}
