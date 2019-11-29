<?php
namespace PWRDK\CustomAttributes\Models\AttributeTypes;

use Illuminate\Database\Eloquent\Model;
use PWRDK\CustomAttributes\IsCustomAttribute;

class AttributeTypeBoolean extends Model
{
    use IsCustomAttribute;

    protected $fillable = ['custom_attribute_id', 'value'];
    public $timestamps = false;
    protected $primaryKey = 'custom_attribute_id';
    protected $table = 'attribute_values_boolean';
    public $hidden = ['id', 'custom_attribute_id'];
}
