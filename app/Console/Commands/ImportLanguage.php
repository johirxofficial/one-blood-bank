<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportLanguage extends Command
{
    protected $signature = 'language:import
        {locale : Locale code such as en, hi, ar, es or pt_BR}
        {file : Path to a JSON dictionary (Bengali source => translated text)}
        {--activate : Make this the active site language}';

    protected $description = 'Import or update a site language dictionary stored in storage/app/languages';

    public function handle(): int
    {
        $locale = strtolower(str_replace('-', '_', (string) $this->argument('locale')));
        $path = (string) $this->argument('file');

        if (preg_match('/^[a-z]{2,8}(_[a-z]{2,8})?$/', $locale) !== 1) {
            $this->error('Invalid locale. Use a code such as en, hi, ar, es or pt_BR.');

            return self::FAILURE;
        }

        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded) || array_is_list($decoded)) {
            $this->error('The dictionary must be a JSON object: {"Bengali source": "translated text"}.');

            return self::FAILURE;
        }

        $dictionary = [];

        foreach ($decoded as $source => $translated) {
            if (is_string($source) && is_string($translated) && trim($source) !== '' && trim($translated) !== '') {
                $dictionary[$source] = $translated;
            }
        }

        if ($dictionary === []) {
            $this->error('No usable translations were found in the file.');

            return self::FAILURE;
        }

        $target = storage_path('app/languages/'.$locale.'.json');
        File::ensureDirectoryExists(dirname($target));

        if (is_file($target)) {
            $existing = json_decode((string) file_get_contents($target), true);
            if (is_array($existing) && ! array_is_list($existing)) {
                $dictionary = array_merge($existing, $dictionary);
            }
        }

        File::put($target, json_encode($dictionary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL);

        if ($this->option('activate')) {
            Setting::put('others.locale', $locale, 'others');
        }

        $this->info('Imported '.count($dictionary)." translations for {$locale}.");
        $this->line('Dictionary: '.$target);

        if ($this->option('activate')) {
            $this->line('Active site language set to '.$locale.'.');
        } else {
            $this->line('To activate it, set Settings → Others → Language to '.$locale.' or rerun with --activate.');
        }

        return self::SUCCESS;
    }
}
