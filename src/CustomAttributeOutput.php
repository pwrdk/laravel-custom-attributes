<?php

namespace PWRDK\CustomAttributes;

class CustomAttributeOutput
{
    public $id;
    public $key;
    public $output;
    public $created_at;
    public $creator;
    public $unique;
    
    public function __construct($input)
    {
        $this->id = $input['id'];
        $this->key = $input['key'];
        $this->unique = $input['unique'];
        $this->output = collect();
        
        if (is_array($input['value'])) {
            foreach($input['value'] as $key => $val) {
                $this->output[$key] = $val;
            }
        } else {
            $this->output = $input['value'];
        }
    }
}
