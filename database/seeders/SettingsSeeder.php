<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * @return array<int, array{key: string, value: mixed, group: string, type: string}>
     */
    public function defaults(): array
    {
        $defaults = [];

        foreach (Setting::defaults() as $key => [$value, $group, $type]) {
            $defaults[] = compact('key', 'value', 'group', 'type');
        }

        return $defaults;
    }

    public function run(): void
    {
        foreach (Setting::defaults() as $key => [$value, $group, $type]) {
            // Never overwrite an administrator's saved value during upgrades.
            if (! Setting::query()->where('key', $key)->exists()) {
                Setting::put($key, $value, $group, $type);
            }
        }
    }
}
