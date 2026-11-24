<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GeoLocationSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = $this->readData('divisions.json');
        $districts = $this->readData('districts.json');
        $upazilas = $this->readData('upazilas.json');
        $unions = $this->readData('unions.json');

        $now = now();

        DB::table('divisions')->insertOrIgnore(array_map(fn (array $row) => [
            'id' => (int) $row['id'],
            'country_code' => 'BD',
            'name' => $row['name'],
            'name_bn' => $row['bn_name'],
            'slug' => Str::slug($row['name']) ?: 'division-'.$row['id'],
            'bbs_code' => $row['bbs_code'] ?? null,
            'latitude' => $row['lat'] ?? null,
            'longitude' => $row['lon'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $divisions));

        DB::table('districts')->insertOrIgnore(array_map(fn (array $row) => [
            'id' => (int) $row['id'],
            'country_code' => 'BD',
            'division_id' => (int) $row['division_id'],
            'name' => $row['name'],
            'name_bn' => $row['bn_name'],
            'slug' => Str::slug($row['name']) ?: 'district-'.$row['id'],
            'bbs_code' => $row['bbs_code'] ?? null,
            'latitude' => $row['lat'] ?? null,
            'longitude' => $row['lon'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $districts));

        foreach (array_chunk($upazilas, 200) as $chunk) {
            DB::table('upazilas')->insertOrIgnore(array_map(fn (array $row) => [
                'id' => (int) $row['id'],
                'country_code' => 'BD',
                'district_id' => (int) $row['district_id'],
                'name' => $row['name'],
                'name_bn' => $row['bn_name'],
                'slug' => Str::slug($row['name']) ?: 'upazila-'.$row['id'],
                'bbs_code' => $row['bbs_code'] ?? null,
                'latitude' => $row['lat'] ?? null,
                'longitude' => $row['lon'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }

        foreach (array_chunk($unions, 500) as $chunk) {
            DB::table('unions')->insertOrIgnore(array_map(fn (array $row) => [
                'id' => (int) $row['id'],
                'country_code' => 'BD',
                'upazila_id' => (int) $row['upazilla_id'],
                'name' => $row['name'],
                'name_bn' => $row['bn_name'],
                'slug' => Str::slug($row['name']) ?: 'union-'.$row['id'],
                'bbs_code' => $row['bbs_code'] ?? null,
                'latitude' => $row['lat'] ?? null,
                'longitude' => $row['lon'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }

    /**
     * Extract the data rows from a bd-geocode JSON dump.
     *
     * @return array<int, array<string, mixed>>
     */
    private function readData(string $file): array
    {
        $json = json_decode(File::get(database_path('data/geo/'.$file)), true);

        foreach ($json as $block) {
            if (($block['type'] ?? null) === 'table' && isset($block['data'])) {
                return $block['data'];
            }
        }

        return [];
    }
}
