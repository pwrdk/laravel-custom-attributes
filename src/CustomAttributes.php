<?php

namespace PWRDK\CustomAttributes;

use Illuminate\Support\Str;
use PWRDK\CustomAttributes\Models\AttributeKey;
use PWRDK\CustomAttributes\Models\AttributeType;
use Cache;

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
    public function update($handle, $newValue, $index = 0)
    {
        $ak = $this->getAttributeKeyByHandle($handle);
        $type = $ak->type->handle;
        
        $this->model->customAttributes()->where('key_id', $ak->id)->get()->each(function ($attributeSet) use ($type, $index, $newValue) {
            if ($type == 'text') {
                $type = 'default';
            }
            $relationshipName = $this->makeRelationshipName($type);
            $attributeSet->$relationshipName[$index]->update(['value' => $newValue]);
        });

        Cache::forget($this->makeCacheKey());
    }

    /**
     * Unset all of the custom attributes for the current handle from this model
     *
     * @return Illuminate\Support\Collection
     * @author PWR
     */
    public function unset($handle = false)
    {
        if (!$handle) {
            $this->model->customAttributes()->delete();
        } else {
            $ak = $this->getAttributeKeyByHandle($handle);
            $this->model->customAttributes()->where('key_id', $ak->id)->delete();
        }

        Cache::forget($this->makeCacheKey());
    }

    public function set($handle, $newValues)
    {
        if (!is_array($newValues)) {
            $newValues = ['value' => $newValues];
        }

        $ak = $this->getAttributeKeyByHandle($handle);
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
        Cache::forget($this->makeCacheKey());
        return $attr;
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
        $cacheKey = $this->makeCacheKey();
        // if (Cache::has($cacheKey)) {
        //     dump("Getting from cache");
        //     $this->collection = Cache::get($cacheKey);
        // } else {
            $this->model->customAttributes->each(function ($attributeSet) {
                $type = $attributeSet->key->type->handle;

                $relationshipName = $this->makeRelationshipName($type);

                //- More than one attrib might be set for this key
                // $values = $attributeSet->{$relationshipName}->map(function ($attribs) {
                //     return $attribs->getFields();
                // });

                $values = $attributeSet->{$relationshipName};

                if (!isset($this->collection[$attributeSet->key->handle])) {
                    $this->collection[$attributeSet->key->handle] = collect();
                }

                $this->collection[$attributeSet->key->handle]->push($values);
            });
            
        //     Cache::put($cacheKey, $this->collection);
        // }

        $output = [];

        if ($this->handle && isset($this->collection[$this->handle])) {
            if (count($this->collection[$this->handle]) == 1) {
                $output = $this->collection[$this->handle]->first()->value;
            } else {
                $output = $this->collection[$this->handle];
            }
        } else {
            $output = $this->collection;
        }

        return $output;
    }

    /**
     * Create a new attribute key and set it's options
     *
     * @param: array $options properties used for the new attribute key
     * @param: boolean $isUnique determines if the new key should be marked as unique
     * @return CustomAttributes\AttributeKey
     * @author PWR
     */
    public static function createKey($handle, $name, $type, $isUnique = true)
    {
        if ($existing = AttributeKey::where('handle', $handle)->first()) {
            return $existing;
        }

        $type = AttributeType::where('handle', $type)->first();
        $ak = AttributeKey::create([
            'type_id' => $type->id,
            'display_name' => $name,
            'handle' => Str::snake($handle ?? $name),
            'is_unique' => $isUnique
        ]);

        return $ak;
    }

    public static function getKeys()
    {
        $ak = AttributeKey::with('type')->get()->map(function ($key) {
            return [
                'display_name' => $key->display_name,
                'handle' => $key->handle,
                'type' => $key->type->display_name,
            ];
        });
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
        
        return 'attributeType' . Str::studly($type);
    }

    protected function makeCacheKey()
    {
        return str_replace("\\", ":", strtolower(get_class($this->model))) . ':' . $this->model->id;
    }

    public function __get($attr)
    {
        $this->handle = $attr;
        return $this->get();
    }
}
