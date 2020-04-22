<?php

namespace PWRDK\CustomAttributes;

use Cache;
use Illuminate\Support\Str;
use PWRDK\CustomAttributes\CustomAttributeOutput;
use PWRDK\CustomAttributes\Interfaces\UsesCustomAttributesCaching;
use PWRDK\CustomAttributes\Models\AttributeKey;
use PWRDK\CustomAttributes\Models\AttributeType;
use PWRDK\CustomAttributes\Models\CustomAttribute;

class CustomAttributes
{
    protected $model;
    public $handle;
    protected $classPath;
    protected $creatorId;
    protected $useCaching = false;

    public function __construct($model, $creatorId = null)
    {
        $this->creatorId = $creatorId;
        $this->model = $model;
        if ($model instanceof UsesCustomAttributesCaching) {
            $this->useCaching = true;
        }
    }

    public function setClassPath($path)
    {
        $this->classPath = $path;
    }

    /**
     * Update the attribute with the selected handle for this model
     *
     * @return Illuminate\Support\Collection
     * @author PWR
     */
    public function update($attributeId, $newValues)
    {
        $attribute = CustomAttribute::find($attributeId);
        //- This gives us $attribute->attributeTypeDefault
        $relationshipName = $this->makeRelationshipName($attribute->key->type->handle);
        
        //- There will always be only one entry per custom attribute row
        $targetAttribute = $attribute->{$relationshipName}()->first();
        
        //- Perform the update
        $targetAttribute->update($newValues);
        
        //- The handle is needed for the cache
        $this->handle = $attribute->key->handle;

        Cache::forget($this->makeCacheKey($this->handle));

        return $targetAttribute;
    }

    /**
     * Unset all of the custom attributes for the current handle from this model
     *
     * @return Illuminate\Support\Collection
     * @author PWR
     */
    public function unset($handle = false)
    {
        self::shouldPurgeCache(true);
        
        if (!$handle) {
            $this->model->customAttributes()->delete();
        } else {
            $ak = $this->getAttributeKeyByHandle($handle);
            $this->model->customAttributes()->where('key_id', $ak->id)->delete();
        }
        $this->handle = $handle;

        Cache::forget($this->makeCacheKey($this->handle));
        Cache::forget($this->makeCacheKey());
    }

    public function set($handle, $newValues)
    {
        self::shouldPurgeCache(true);
        
        $this->handle = $handle;
        //- Clear the cache values first
        $cacheKey = $this->makeCacheKey($this->handle);
        $cacheKeyCollection = Cache::get($cacheKey);
        Cache::forget($cacheKey . ':' . md5($cacheKeyCollection));
        Cache::forget($cacheKey);

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

        //- Create a new entry in the CustomAttributes table
        $data = ['key_id' => $ak->id];
        if ($this->creatorId > 0) {
            $data['creator_id'] = $this->creatorId;
        }

        if ($this->model instanceof HasLocalCustomAttributeType && class_exists('App\Models\AttributeTypes\\' . ucFirst($relationshipName))) {
            $this->setClassPath('App\Models\AttributeTypes\\');
        } else {
            $this->setClassPath('PWRDK\CustomAttributes\Models\AttributeTypes\\');
        }

        $className = $this->classPath . ucfirst($relationshipName);

        //- Create a new entry in the CustomAttributes table
        if ($ak->is_unique) {
            $newCa = $this->model->customAttributes()->firstOrCreate(['key_id' => $ak->id]);

            $attr = $className::updateOrCreate(
                ['custom_attribute_id' => $newCa->id],
                $newValues
            );
        } else {
            $newCa = $this->model->customAttributes()->create($data);
            $newValues += ['custom_attribute_id' => $newCa->id];
            $attr = $className::create($newValues);
        }
        
        return $attr;
    }
    

    /**
     * Get a collection of attributes from the current handl
     *
     * @return Illuminate\Support\Collection
     * @author PWR
     */
    public function get($attributeHandle = null, $fresh = true, \Closure $callback = null)
    {

        $this->handle = $attributeHandle;

        $this->collection = collect();
        $cacheKey = $this->makeCacheKey($this->handle);

        if ($this->useCaching && !$fresh && Cache::has($cacheKey) && !self::shouldPurgeCache()) {
            $modelCustomAttributes = Cache::get($cacheKey);
        } else {
            $modelCustomAttributes = $this->getModelCustomAttributes();
            if (!count($modelCustomAttributes)) {
                return false;
            }
            Cache::put($cacheKey, $modelCustomAttributes);
        }

        $cacheKeyCollection = $cacheKey . ':' . md5($modelCustomAttributes);

        if ($this->useCaching && !$fresh && Cache::has($cacheKeyCollection) && !self::shouldPurgeCache()) {
            $values = Cache::get($cacheKeyCollection);
            // \Log::debug("Getting from cache " . $cacheKeyCollection);
        } else {
            $collection = $this->buildRelationships($modelCustomAttributes)->filter();

            $values = $collection->values();

            if (count($values) == 0) {
                return false;
            }

            //- If we only have a single value, we can just return that one.
            //- If the key is marked as unique however, we must return a collection
            if (count($values) == 1 && $values->first()->unique) {
                return $values->first();
            }

            //- If we have requested every attribute, we'll group them by their key
            if (!$this->handle) {
                $values = $collection->groupBy('key');
            }

            // \Log::debug("Adding to cache " . $cacheKeyCollection);
            Cache::put($cacheKeyCollection, $values);
        }
        
        if (!is_null($callback)) {
            return $callback($values);
        }

        return $values;
    }

    public function buildRelationships($modelCustomAttributes)
    {
        return $modelCustomAttributes->map(function ($customAttributeModel) use (&$types) {
            $type = $customAttributeModel->key->type->handle;

            $relationshipName = $this->makeRelationshipName($type);
            $types[] = $type;

            if ($mappedValue = $customAttributeModel->{$relationshipName}()->first()) {
                $mappedValue = $mappedValue->mappedValue();
            } else {
                return false;
            }
            $data = [
                'key' => $customAttributeModel->key->handle,
                'value' => $mappedValue,
                'unique' => $customAttributeModel->key->is_unique,
                'created_at' => $customAttributeModel->created_at,
                'id' => $customAttributeModel->id,
            ];

            if ($this->creatorId) {
                $data += ['creator' => $customAttributeModel->creator];
            }
            
            $obj = new CustomAttributeOutput($data);
            
            return $obj;
        });
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

    protected function makeCacheKey($handle = false)
    {
        $handle = !empty($handle) ? $handle : 'all';

        $cacheKey = 'custom-attributes:';
        $cacheKey .= str_replace("\\", ":", strtolower(get_class($this->model))) . ':' . $this->model->id . ':' . ($handle);
        
        return $cacheKey;
    }

    public static function shouldPurgeCache($put = false)
    {
        if (!$put) {
            if (Cache::has('custom-attributes:should-purge')) {
                Cache::forget('custom-attributes:should-purge');
                return true;
            } else {
                return false;
            }
        } else {
            Cache::put('custom-attributes:should-purge', 1);
        }
    }

    public function __get($attr)
    {
        return $this->get($attr);
    }

    /**
     * Get custom attributes for a model by the attribute key type
     *
     * @return array
     * @author PWR
     */
    public static function getByType($handle)
    {
        $type = AttributeType::with('keys')->where('handle', $handle)->first();
        $output = collect();
        foreach ($type->keys as $key) {
            $modelCustomAttributes = (new static($key))->buildRelationships($key->customAttributes);

            foreach ($modelCustomAttributes as $attributeCollection) {
                $output[$key->handle] = $attributeCollection->pluck('value');
            }
        }

        return $output;
    }

    /**
     * Get the customattributes for a model
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @author PWR
     */
    public function getModelCustomAttributes()
    {
        return $this->model->customAttributes()->when($this->handle, function ($query) {
            $query->whereHas('key', function ($query) {
                return $query->where('handle', $this->handle);
            });
        })->when($this->creatorId, function ($query) {
            $query->with('creator');
        })->get();
    }
}
