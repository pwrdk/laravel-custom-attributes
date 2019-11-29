<?php

namespace PWRDK\CustomAttributes;

use PWRDK\CustomAttributes\CustomAttributes as CustomAttributesManager;
use PWRDK\CustomAttributes\Models\CustomAttribute;

trait IsCustomAttribute
{
    public function getFields()
    {
        return collect($this->getAttributes())->filter(function ($val, $key) {
            return !in_array($key, $this->hidden);
        })->toArray();
    }
}
