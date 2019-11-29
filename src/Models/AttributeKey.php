<?php

namespace PWRDK\CustomAttributes\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeKey extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function type()
    {
        return $this->belongsTo(AttributeType::class);
    }

    public function customAttributes()
    {
        return $this->hasMany(CustomAttribute::class, 'key_id');
    }
}
