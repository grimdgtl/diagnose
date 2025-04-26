<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    // Komanda će se zvati: php artisan sitemap:generate
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate public/sitemap.xml';

    public function handle()
    {
        // Generiše sitemap na osnovu APP_URL iz .env
        SitemapGenerator::create(config('app.url'))
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('sitemap.xml generated at ' . public_path('sitemap.xml'));
    }
}
