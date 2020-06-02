<?php
namespace PWRDK\CustomAttributes\Models\AttributeTypes;

use Illuminate\Database\Eloquent\Model;
use PWRDK\CustomAttributes\IsCustomAttribute;

class AttributeTypeJson extends Model
{
    use IsCustomAttribute;

    protected $fillable = ['custom_attribute_id','value'];
    public $timestamps = true;
    protected $primaryKey = 'custom_attribute_id';
    protected $table = 'attribute_values_json';
    public $hidden = ['id', 'custom_attribute_id'];

    public function mappedValue()
    {
        return [
            'values' => json_decode($this->value)
        ];
    }
}
