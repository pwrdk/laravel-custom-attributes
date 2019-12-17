<?php
namespace PWRDK\CustomAttributes\Models\AttributeTypes;

use Illuminate\Database\Eloquent\Model;
use PWRDK\CustomAttributes\IsCustomAttribute;

class AttributeTypeContactInformation extends Model
{
    use IsCustomAttribute;

    protected $fillable = ['custom_attribute_id','first_name', 'last_name', 'email', 'mobile_phone'];
    public $timestamps = false;
    protected $primaryKey = 'custom_attribute_id';
    protected $table = 'attribute_values_contact_information';
    public $hidden = ['id', 'custom_attribute_id'];

    public function mappedValue()
    {
        return $this->only('first_name', 'last_name', 'email', 'mobile_number');
    }
}
