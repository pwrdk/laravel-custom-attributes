<?php

namespace PWRDK\CustomAttributes;

class CustomAttributeOutput
{
    public $id;
    public $key;
    public $value;
    public $created_at;
    public $creator;
    
    public function __construct($input)
    {
        foreach ($input as $key => $val) {
            $this->$key = $val;
        }
    }
}
