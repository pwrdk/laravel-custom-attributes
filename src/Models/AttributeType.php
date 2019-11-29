<?php

namespace PWRDK\CustomAttributes\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeType extends Model
{
    protected $guarded = [];


    public function keys()
    {
        return $this->hasMany(AttributeKey::class, 'type_id');
    }
}
