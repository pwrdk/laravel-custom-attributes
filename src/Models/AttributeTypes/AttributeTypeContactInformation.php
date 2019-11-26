<?php
namespace PWRDK\CustomAttributes\Models\AttributeTypes;

use Illuminate\Database\Eloquent\Model;

class AttributeTypeContactInformation extends Model
{
    protected $fillable = ['custom_attribute_id','first_name', 'last_name', 'email', 'mobile_phone'];
    public $timestamps = false;
    protected $primaryKey = 'custom_attribute_id';
    protected $table = 'attribute_values_contact_information';
    public $hidden = ['id', 'custom_attribute_id'];

    public function getFields()
    {
        return collect($this->getAttributes())->filter(function ($val, $key) {
            return !in_array($key, $this->hidden);
        });
    }
}
