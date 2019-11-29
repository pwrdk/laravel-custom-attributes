<?php

namespace PWRDK\CustomAttributes;

trait IsCustomAttribute
{
    public function getFields()
    {
        return collect($this->getAttributes())->filter(function ($val, $key) {
            return !in_array($key, $this->hidden);
        })->toArray();
    }
}
