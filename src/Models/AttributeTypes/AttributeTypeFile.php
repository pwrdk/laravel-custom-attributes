<?php
namespace PWRDK\CustomAttributes\Models\AttributeTypes;

use Illuminate\Database\Eloquent\Model;
use PWRDK\CustomAttributes\IsCustomAttribute;

class AttributeTypeFile extends Model
{
    use IsCustomAttribute;

    protected $guarded = [];
    public $timestamps = true;
    protected $primaryKey = 'custom_attribute_id';
    protected $table = 'attribute_values_file';
    public $hidden = ['id', 'custom_attribute_id'];

    public function mappedValue()
    {
        return $this->only('file_name', 'file_type');
    }
}
