<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class SystemSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Whitelist of writable settings and their validation rules.
     */
    public const ALLOWED_KEYS = [
        'enrollment_fee_amount' => ['required', 'numeric', 'min:0', 'max:1000000'],
        'exam_fee_amount' => ['required', 'numeric', 'min:0', 'max:1000000'],
        'late_fee_amount' => ['required', 'numeric', 'min:0', 'max:1000000'],
        'challan_validity_days' => ['required', 'integer', 'min:1', 'max:365'],
        'site_name' => ['required', 'string', 'max:255'],
        'site_email' => ['required', 'email', 'max:255'],
        'site_phone' => ['required', 'string', 'max:50'],
        'site_address' => ['required', 'string', 'max:500'],
        'allow_enrollment' => ['required', 'in:true,false,1,0'],
        'maintenance_mode' => ['required', 'in:true,false,1,0'],
    ];

    /**
     * Validate a batch of setting updates against the whitelist.
     *
     * Rejects unknown keys and values that fail the per-key rules.
     *
     * @throws ValidationException
     */
    public static function validateUpdates(array $updates): array
    {
        $unknown = array_diff(array_keys($updates), array_keys(self::ALLOWED_KEYS));

        if ($unknown !== []) {
            throw ValidationException::withMessages([
                'settings' => ['Unknown setting key(s): '.implode(', ', $unknown)],
            ]);
        }

        $rules = array_intersect_key(self::ALLOWED_KEYS, $updates);

        return validator($updates, $rules)->validate();
    }

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::find($key);

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get multiple settings
     */
    public static function getMultiple(array $keys): array
    {
        $settings = static::whereIn('key', $keys)->get();
        $result = [];

        foreach ($keys as $key) {
            $setting = $settings->firstWhere('key', $key);
            $result[$key] = $setting ? $setting->value : null;
        }

        return $result;
    }
}
