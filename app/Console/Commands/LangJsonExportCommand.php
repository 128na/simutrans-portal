<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

final class LangJsonExportCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'lang:export-json {--locales=ja}';

    /**
     * コマンドの説明
     */
    protected $description = 'Export Laravel lang PHP files into JSON for frontend (Vite/React)';

    public function handle(): int
    {
        $locales = explode(',', (string) $this->option('locales'));

        foreach ($locales as $locale) {
            App::setLocale($locale);
            $translations = [];

            $langPath = base_path('lang/'.$locale);
            if (! is_dir($langPath)) {
                $this->warn(sprintf("⚠️  Locale '%s' not found in resources/lang/", $locale));

                continue;
            }

            foreach (glob($langPath.'/*.php') ?: [] as $file) {
                $filename = basename($file, '.php');
                $translations[$filename] = require $file;
            }

            $jsonPath = resource_path(sprintf('js/utils/%s.json', $locale));
            if (! is_dir(dirname($jsonPath))) {
                mkdir(dirname($jsonPath), 0755, true);
            }

            file_put_contents(
                $jsonPath,
                json_encode(Arr::dot($translations), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );

            $this->info('✅ Generated: '.$jsonPath);
        }

        $this->info('✨ Language export completed.');

        return Command::SUCCESS;
    }
}
