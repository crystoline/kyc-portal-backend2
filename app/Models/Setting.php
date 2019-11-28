<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;


class Setting extends BaseModel
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    protected $hidden = ['path'];
    protected $fillable = [ 'name', 'val', 'type', 'path'];

    /**
     * Add a settings value
     *
     * @param $key
     * @param $val
     * @param string $type
     * @param string $path
     * @return bool
     */
    public static function add($key, $val, $type = 'string', $path=''): bool
    {
        if ( self::has($key) ) {
            return self::set($key, $val, $type);
        }
        return self::query()->create(['name' => $key, 'val' => $val, 'type' => $type, 'path' => $path]) ? $val : false;
    }

    /**
     * Get a settings value
     *
     * @param $key
     * @param null $default
     * @return bool|int|mixed
     */
    public static function get($key, $default = null)
    {
        if ( self::has($key) ) {
            $setting = self::getAllSettings()->where('name', $key)->first();
            return self::castValue($setting->val, $setting->type);
        }

        return self::getDefaultValue($key, $default);
    }

    /**
     * Set a value for setting
     *
     * @param $key
     * @param $val
     * @param string $type
     * @return bool
     */
    public static function set($key, $val, $type = 'string',$path=''): bool
    {
        $val = ($type === 'array')? json_encode($val) :$val;
        //dump($type);

        /** @var Setting $setting */
        $setting = self::getAllSettings()->where('name', $key)->first();
        if ($setting !== null ) {
            return $setting->update([
                'name' => $key,
                'val' => $val,
                'type' => $type,
                'path' => $path
            ]) ? $val : false;
        }

        return self::add($key, $val, $type);
    }

    /**
     * Remove a setting
     *
     * @param $key
     * @return bool
     */
    public static function remove($key): bool
    {
        if( self::has($key) ) {
            return self::query()->where('name', $key)->delete();
        }

        return false;
    }

    /**
     * Check if setting exists
     *
     * @param $key
     * @return bool
     */
    public static function has($key): bool
    {
        return (boolean) self::getAllSettings()->whereStrict('name', $key)->count();
    }

    /**
     * Get the validation rules for setting fields
     *
     * @return array
     */
    public static function getValidationRules(): array
    {
        return self::getDefinedSettingFields()->pluck('rules', 'name')
            ->reject(static function ($val) {
                return $val === null;
            })->toArray();
    }

    /**
     * Get the data type of a setting
     *
     * @param $field
     * @return mixed
     */
    public static function getDataType($field)
    {
        $type  = self::getDefinedSettingFields()
            ->pluck('data', 'name')
            ->get($field);

        return $type ?? 'string';
    }

    /**
     * Get default value for a setting
     *
     * @param $field
     * @return mixed
     */
    public static function getDefaultValueForField($field)
    {
        return self::getDefinedSettingFields()
            ->pluck('value', 'name')
            ->get($field);
    }

    /**
     * Get default value from config if no value passed
     *
     * @param $key
     * @param $default
     * @return mixed
     */
    private static function getDefaultValue($key, $default)
    {
        return $default ?? self::getDefaultValueForField($key);
    }

    /**
     * Get all the settings fields from config
     *
     * @return Collection
     */
    private static function getDefinedSettingFields(): Collection
    {
        return collect(config('settings'))->pluck('elements')->flatten(1);
    }

    /**
     * caste value into respective type
     *
     * @param $val
     * @param $castTo
     * @return bool|int
     */
    private static function castValue($val, $castTo)
    {
        switch ($castTo) {
            case 'int':
            case 'integer':
                return (int)$val;
                break;
            case 'array':
                return json_decode($val, true);
                break;

            case 'bool':
            case 'boolean':
                return (bool)$val;
                break;

            default:
                return $val;
        }
    }



    /**
     * Get all setting
     * @return array
     */
    public static function settings(): array
    {
        $collection =  new Collection();
        $settings = config('settings');
        foreach ($settings as $section => $fields){
            if(is_array($fields['elements'])) {

                foreach ($fields['elements'] as $index => $element) {
                    $fields['elements'][$index]['value'] = setting($element['name']);
                    $name = $element['name'];
                    $fields['elements'][$index] = self::afterFetchSetting($fields['elements'][$index]);
                }
            }

            $collection->put($section, $fields);
        }

        return $collection->toArray();
    }


    /**
     * Get all the settings
     *
     * @return mixed
     */
    public static function getAllSettings()
    {
        //return self::all();
        return Cache::rememberForever('settings.all', static function() {
            return self::all();
        });
    }

    /**
     * @param array $item
     * @return array
     */
    public static function afterFetchSetting(array $item): array
    {
        $name = $item['name']?? null;
        if ($name === 'field_officer_role') {

            $item['options'] = Group::query()->get()->map(static function (Group $group) {
                return [
                    'value' => $group->role,
                    'name' => $group->name
                ];
            })->values();
        }
        return $item;
    }

    /**
     * Flush the cache
     */
    public static function flushCache(): void
    {
        Cache::forget('settings.all');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::updated(static function () {
            self::flushCache();
        });

        static::created(static function() {
            self::flushCache();
        });
    }
}
