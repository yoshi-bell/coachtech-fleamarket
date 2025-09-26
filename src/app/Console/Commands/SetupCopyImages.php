<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupCopyImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:copy-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy dummy images from resources to storage for seeding.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sourcePath = resource_path('images/seed_images');
        $destinationPath = storage_path('app/public');

        if (!File::exists($sourcePath)) {
            $this->error('Source directory not found: ' . $sourcePath);
            return 1;
        }

        // Clean up destination directories if they exist
        File::deleteDirectory($destinationPath . '/item_images');
        File::deleteDirectory($destinationPath . '/profile_images');

        // Copy the directories
        File::copyDirectory($sourcePath, $destinationPath);

        $this->info('Successfully copied dummy images to storage.');
        return 0;
    }
}
