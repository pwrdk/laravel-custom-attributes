<?php
namespace PWRDK\CustomAttributes\Models;

use Illuminate\Database\Eloquent\Model;

class CustomAttribute extends Model
{

    protected $fillable = ['key_id','value_id', 'creator_id'];

    public function attributable()
    {
        return $this->morphTo();
    }
    
    public function key()
    {
        return $this->belongsTo(AttributeKey::class);
    }

    public function creator()
    {
        return $this->belongsTo(config('customattributes.usermodel'));
    }

    public function getHandlerAttribute()
    {
        return $this->key->type->handle;
    }

    public function attributeTypeDefault()
    {
        return $this->hasOne(AttributeTypes\AttributeTypeDefault::class);
    }

    public function attributeTypeBoolean()
    {
        return $this->hasOne(AttributeTypes\AttributeTypeBoolean::class);
    }

    public function attributeTypeNumber()
    {
        return $this->hasOne(AttributeTypes\AttributeTypeNumber::class);
    }

    public function attributeTypeDateTime()
    {
        return $this->hasOne(AttributeTypes\AttributeTypeDateTime::class);
    }

    public function attributeTypeContactInformation()
    {
        return $this->hasOne(AttributeTypes\AttributeTypeContactInformation::class);
    }
}
