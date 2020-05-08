<?php

function attr($object, $attributeKey, $key = null)
{
    $value = $object->attr()->get($attributeKey)->value;
    
    if (!$value) {
        throw new \Exception("No such attribute " . $attributeKey);
    }

    if (!$key) {
        return $value;
    }

    return $value[$key];
}
