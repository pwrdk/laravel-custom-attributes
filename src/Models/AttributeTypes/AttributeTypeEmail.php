<?php
namespace App\Models\AttributeTypes;

use Illuminate\Database\Eloquent\Model;
use PWRDK\CustomAttributes\IsCustomAttribute;

class AttributeTypeEmail extends Model
{
    use IsCustomAttribute;

    protected $guarded = [];
    public $timestamps = true;
    protected $primaryKey = 'custom_attribute_id';
    protected $table = 'attribute_values_email';
    public $hidden = ['id', 'custom_attribute_id'];

    public function mappedValue()
    {
        return [
            'recipient' => $this->recipient,
            'cc' => $this->bcc,
            'bcc' => $this->bcc,
            'body' => $this->body
        ];
    }
}
