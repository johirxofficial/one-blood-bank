<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Division;
use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ImportLocations extends Command
{
    protected $signature = 'locations:import
        {file : Path to a CSV file}
        {--country=BD : ISO-3166 alpha-2 country code}
        {--delimiter=, : CSV delimiter}';

    protected $description = 'Import a country location hierarchy (division, district, upazila, union) from CSV';

    public function handle(): int
    {
        $path = (string) $this->argument('file');
        $country = strtoupper((string) $this->option('country'));
        $delimiter = (string) $this->option('delimiter') ?: ',';

        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        if (preg_match('/^[A-Z]{2}$/', $country) !== 1) {
            $this->error('Country must be a two-letter ISO code such as BD, IN, US or GB.');

            return self::FAILURE;
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            $this->error("Unable to open {$path}");

            return self::FAILURE;
        }

        $header = fgetcsv($handle, 0, $delimiter);

        if ($header === false) {
            fclose($handle);
            $this->error('The CSV file is empty.');

            return self::FAILURE;
        }

        $header = array_map(
            fn ($value) => strtolower(trim((string) preg_replace('/^\xEF\xBB\xBF/', '', (string) $value))),
            $header,
        );

        foreach (['level', 'name'] as $required) {
            if (! in_array($required, $header, true)) {
                fclose($handle);
                $this->error("Missing required CSV column: {$required}");

                return self::FAILURE;
            }
        }

        $counts = ['division' => 0, 'district' => 0, 'upazila' => 0, 'union' => 0];
        $line = 1;

        try {
            DB::transaction(function () use ($handle, $header, $delimiter, $country, &$counts, &$line): void {
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $line++;

                    if ($row === [null] || $row === []) {
                        continue;
                    }

                    $row = array_pad($row, count($header), null);
                    $data = array_combine($header, $row);

                    if ($data === false) {
                        continue;
                    }

                    $level = strtolower(trim((string) ($data['level'] ?? '')));
                    $name = trim((string) ($data['name'] ?? ''));

                    if ($name === '') {
                        continue;
                    }

                    if (! array_key_exists($level, $counts)) {
                        throw new RuntimeException("Line {$line}: unknown level '{$level}'.");
                    }

                    $nameBn = trim((string) ($data['name_bn'] ?? '')) ?: $name;
                    $parent = trim((string) ($data['parent'] ?? ''));
                    $code = trim((string) ($data['code'] ?? '')) ?: null;
                    $latitude = $this->nullableNumber($data['latitude'] ?? null);
                    $longitude = $this->nullableNumber($data['longitude'] ?? null);

                    match ($level) {
                        'division' => $this->importDivision($country, $name, $nameBn, $code, $latitude, $longitude),
                        'district' => $this->importDistrict($country, $name, $nameBn, $parent, $code, $latitude, $longitude, $line),
                        'upazila' => $this->importUpazila($country, $name, $nameBn, $parent, $code, $latitude, $longitude, $line),
                        'union' => $this->importUnion($country, $name, $nameBn, $parent, $code, $latitude, $longitude, $line),
                    };

                    $counts[$level]++;
                }
            });
        } catch (\Throwable $exception) {
            fclose($handle);
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        fclose($handle);

        $this->info("Imported locations for {$country}:");
        $this->table(['Level', 'Rows'], [
            ['Divisions', $counts['division']],
            ['Districts', $counts['district']],
            ['Upazilas', $counts['upazila']],
            ['Unions', $counts['union']],
        ]);
        $this->line('Set Settings → Others → Location country code to '.$country.' to show this hierarchy on the site.');

        return self::SUCCESS;
    }

    private function importDivision(
        string $country,
        string $name,
        string $nameBn,
        ?string $code,
        ?float $latitude,
        ?float $longitude,
    ): void {
        $division = Division::query()
            ->forCountry($country)
            ->where('name', $name)
            ->first() ?? new Division;

        $division->fill([
            'country_code' => $country,
            'name' => $name,
            'name_bn' => $nameBn,
            'bbs_code' => $code,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        if (! $division->exists || blank($division->slug)) {
            $division->slug = $this->uniqueSlug('divisions', $country.' '.$name, $division->id);
        }

        $division->save();
    }

    private function importDistrict(
        string $country,
        string $name,
        string $nameBn,
        string $parent,
        ?string $code,
        ?float $latitude,
        ?float $longitude,
        int $line,
    ): void {
        $division = $this->findParent(Division::query()->forCountry($country), $parent);

        if ($division === null) {
            throw new RuntimeException("Line {$line}: parent division '{$parent}' was not found for district '{$name}'.");
        }

        $district = District::query()
            ->forCountry($country)
            ->where('division_id', $division->id)
            ->where('name', $name)
            ->first() ?? new District;

        $district->fill([
            'country_code' => $country,
            'division_id' => $division->id,
            'name' => $name,
            'name_bn' => $nameBn,
            'bbs_code' => $code,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        if (! $district->exists || blank($district->slug)) {
            $district->slug = $this->uniqueSlug('districts', $country.' '.$parent.' '.$name, $district->id);
        }

        $district->save();
    }

    private function importUpazila(
        string $country,
        string $name,
        string $nameBn,
        string $parent,
        ?string $code,
        ?float $latitude,
        ?float $longitude,
        int $line,
    ): void {
        $district = $this->findParent(District::query()->forCountry($country), $parent);

        if ($district === null) {
            throw new RuntimeException("Line {$line}: parent district '{$parent}' was not found for upazila '{$name}'.");
        }

        $upazila = Upazila::query()
            ->forCountry($country)
            ->where('district_id', $district->id)
            ->where('name', $name)
            ->first() ?? new Upazila;

        $upazila->fill([
            'country_code' => $country,
            'district_id' => $district->id,
            'name' => $name,
            'name_bn' => $nameBn,
            'bbs_code' => $code,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        if (! $upazila->exists || blank($upazila->slug)) {
            $upazila->slug = $this->uniqueSlug('upazilas', $country.' '.$parent.' '.$name, $upazila->id);
        }

        $upazila->save();
    }

    private function importUnion(
        string $country,
        string $name,
        string $nameBn,
        string $parent,
        ?string $code,
        ?float $latitude,
        ?float $longitude,
        int $line,
    ): void {
        $upazila = $this->findParent(Upazila::query()->forCountry($country), $parent);

        if ($upazila === null) {
            throw new RuntimeException("Line {$line}: parent upazila '{$parent}' was not found for union '{$name}'.");
        }

        $union = Union::query()
            ->forCountry($country)
            ->where('upazila_id', $upazila->id)
            ->where('name', $name)
            ->first() ?? new Union;

        $union->fill([
            'country_code' => $country,
            'upazila_id' => $upazila->id,
            'name' => $name,
            'name_bn' => $nameBn,
            'bbs_code' => $code,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        if (! $union->exists || blank($union->slug)) {
            $union->slug = $this->uniqueSlug('unions', $country.' '.$parent.' '.$name, $union->id);
        }

        $union->save();
    }

    private function findParent($query, string $parent)
    {
        if ($parent === '') {
            return null;
        }

        return (clone $query)
            ->where(fn ($builder) => $builder->where('name', $parent)->orWhere('bbs_code', $parent))
            ->first();
    }

    private function uniqueSlug(string $table, string $base, ?int $ignoreId = null): string
    {
        $base = substr(Str::slug($base) ?: 'location', 0, 70);
        $slug = $base;
        $counter = 2;

        while (DB::table($table)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    private function nullableNumber(mixed $value): ?float
    {
        $value = trim((string) $value);

        return is_numeric($value) ? (float) $value : null;
    }
}
