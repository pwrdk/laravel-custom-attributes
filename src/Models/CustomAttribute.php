<?php
namespace PWRDK\CustomAttributes\Models;

use Illuminate\Database\Eloquent\Model;

class CustomAttribute extends Model
{

    protected $fillable = ['key_id','value_id'];
    public $timestamps = false;

    public function attributable()
    {
        return $this->morphTo();
    }
    
    public function key()
    {
        return $this->belongsTo(AttributeKey::class);
    }

    public function getHandlerAttribute()
    {
        return $this->key->type->handle;
    }

    public function attributeTypeDefault()
    {
        return $this->hasMany(AttributeTypes\AttributeTypeDefault::class);
    }

    public function attributeTypeBoolean()
    {
        return $this->hasMany(AttributeTypes\AttributeTypeBoolean::class);
    }

    public function attributeTypeNumber()
    {
        return $this->hasMany(AttributeTypes\AttributeTypeNumber::class);
    }

    public function attributeTypeDateTime()
    {
        return $this->hasMany(AttributeTypes\AttributeTypeDateTime::class);
    }
}
