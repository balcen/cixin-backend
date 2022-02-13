<?php

namespace App\Console\Commands\Transfer;

use App\Models\ProductCategory;
use Illuminate\Console\Command;

class AddDefaultIncomingCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product-category:add-default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        ProductCategory::query()
            ->firstOrCreate([
                'tracking_number' => 'IN00',
                'type' => 'incoming',
                'name' => '進貨項目',
            ]);
    }
}
