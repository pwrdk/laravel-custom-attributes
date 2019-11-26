<?php
namespace PWRDK\CustomAttributes\Models\AttributeTypes;

use Illuminate\Database\Eloquent\Model;

class AttributeTypeDateTime extends Model
{
    protected $fillable = ['custom_attribute_id', 'value'];
    public $timestamps = false;
    protected $primaryKey = 'custom_attribute_id';
    protected $table = 'attribute_values_date';
    public $hidden = ['id', 'custom_attribute_id'];

    protected $casts = [
        'value' => 'datetime'
    ];

    public function getFields()
    {
        return collect($this->getAttributes())->filter(function ($val, $key) {
            return !in_array($key, $this->hidden);
        });
    }
}
