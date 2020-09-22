<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VueI18nGenerator;

/**
 * @see https://github.com/martinlindhe/laravel-vue-i18n-generator/blob/master/src/Commands/GenerateInclude.php
 */
class VueI18n extends Command
{
    protected $signature = 'vue-i18n:generate';
    protected $description = "Generates a vue-i18n compatible js array out of project translations";
    private VueI18nGenerator $generator;

    public function __construct(VueI18nGenerator $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    public function handle()
    {
        $root = base_path() . '/resources/lang';

        $data = $this->generator->generateFromPath($root);

        $jsFile = base_path() . '/resources/js/vue-i18n-locales.generated.js';
        file_put_contents($jsFile, $data);

        $this->info("Written to : " . $jsFile);
        return 0;
    }
}
