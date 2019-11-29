<?php

namespace PWRDK\CustomAttributes;

trait IsCustomAttribute
{
    public function ownerAttribute()
    {
        return $this->belongsTo(CustomAttribute::class);
    }

    public function getFields()
    {
        return collect($this->getAttributes())->filter(function ($val, $key) {
            return !in_array($key, $this->hidden);
        });
    }

    public function getAttributeValuesAttribute()
    {
        return $this->getFields()->values();
    }
}
