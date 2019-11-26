<?php

namespace PWRDK\CustomAttributes;

use Illuminate\Support\Str;
use PWRDK\CustomAttributes\Models\AttributeKey;
use PWRDK\CustomAttributes\Models\AttributeType;

class CustomAttributes
{
    protected $model;
    public $handle;
    protected $classPath;

    public function __construct($model, $handle = false)
    {
        $this->handle = $handle;
        $this->model = $model;
        $this->classPath = 'PWRDK\CustomAttributes\Models\AttributeTypes\\';
    }

    /**
     * Update the attribute with the selected handle for this model
     *
     * @return Illuminate\Support\Collection
     * @author PWR
     */
    public function update($newValue, $index = 0)
    {
        $ak = $this->getAttributeKeyByHandle($this->handle);
        $type = $ak->type->handle;
        
        $this->model->customAttributes()->where('key_id', $ak->id)->get()->each(function ($attributeSet) use ($type, $index, $newValue) {
            if ($type == 'text') {
                $type = 'default';
            }
            $relationshipName = $this->makeRelationshipName($type);
            $attributeSet->$relationshipName[$index]->update(['value' => $newValue]);
        });
    }

    /**
     * Unset all of the custom attributes for the current handle from this model
     *
     * @return Illuminate\Support\Collection
     * @author PWR
     */
    public function unset()
    {
        $ak = $this->getAttributeKeyByHandle($this->handle);
        $this->model->customAttributes()->where('key_id', $ak->id)->delete();
        return $this->get();
    }

    public function set($newValues)
    {
        if (!is_array($newValues)) {
            $newValues = ['value' => $newValues];
        }

        $ak = $this->getAttributeKeyByHandle($this->handle);
        if (!$ak) {
            return false;
        }

        //- Get the type handle
        //- We need it to create the name of the actual attribute class/relationship/table
        $type = $ak->type->handle;
        if ($type == 'text') {
            $type = 'default';
        }
        $relationshipName = $this->makeRelationshipName($type);

        if ($ak->is_unique) {
            //- Get existing
            $this->model->customAttributes()->where('key_id', $ak->id)->delete();
        }

        //- Create a new entry in the CustomAttributes table
        $newCa = $this->model->customAttributes()->create([
            'key_id' => $ak->id,
        ]);

        $className = $this->classPath . $relationshipName;
        //- Create a new entry in the CustomAttributes table

        $attr = $newCa->$relationshipName()->save(
            new $className(
                $newValues +
                ['custom_attribute_id' => $newCa->id],
            )
        );
        return $this->get($this->handle);
    }
    

    /**
     * Get a collection of attributes from the current handl
     *
     * @return Illuminate\Support\Collection
     * @author PWR
     */
    public function get()
    {
        $this->collection = collect();
        $this->model->customAttributes->filter(function ($attributeSet) {
            if (!$this->handle) {
                return true;
            }
            return $attributeSet->key->handle == $this->handle;
        })->each(function ($attributeSet) {
            $type = $attributeSet->key->type->handle;

            $relationshipName = $this->makeRelationshipName($type);

            //- More than one attrib might be set for this key
            $values = $attributeSet->{$relationshipName}->map(function ($attribs) {
                return $attribs->getFields()->flatten();
            })->collapse();

            if (!isset($this->collection[$attributeSet->key->handle])) {
                $this->collection[$attributeSet->key->handle] = collect();
            }

            $this->collection[$attributeSet->key->handle]->push($values);
        });

        return $this->collection->map(function ($attributes, $key) {
            if (count($attributes) == 1) {
                return $attributes->flatten()->first();
            }
            return $attributes->flatten()->toArray();
        })->all();
    }

    /**
     * Create a new attribute key and set it's options
     *
     * @param: array $options properties used for the new attribute key
     * @param: boolean $isUnique determines if the new key should be marked as unique
     * @return CustomAttributes\AttributeKey
     * @author PWR
     */
    public function createKey($options, $isUnique = true)
    {
        if ($existing = AttributeKey::where('handle', $this->handle)->first()) {
            return $existing;
        }

        $type = AttributeType::where('handle', $options['type'])->first();
        $ak = AttributeKey::create([
            'type_id' => $type->id,
            'display_name' => $options['name'],
            'handle' => Str::snake($options['handle'] ?? $options['name']),
            'is_unique' => $isUnique
        ]);

        return $ak;
    }

    /**
     * Delete a key and all its associated attributes
     *
     * @return boolean
     * @author PWR
     */
    public function deleteKey()
    {
        AttributeKey::where('handle', $this->handle)->delete();
        return true;
    }

    /**
     * Lookup an attribute key by it's handle
     *
     * @param string $handle    the handle of the attribute key to look for
     * @return CustomAttributes\AttributeKey
     * @author PWR
     */
    protected function getAttributeKeyByHandle($handle)
    {
        return AttributeKey::where('handle', $handle)->first();
    }

    /**
     * Construct a class name for the relationship based on type of key selected
     *
     * @param string $type the type of attribute we are working with
     * @return string
     * @author PWR
     */

    protected function makeRelationshipName($type)
    {
        //- @todo: Move this to a map of some sort
        if ($type == 'text') {
            $type = 'default';
        }
        
        return 'attributeType' . ucfirst($type);
    }
}
