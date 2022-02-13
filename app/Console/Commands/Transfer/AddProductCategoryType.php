<?php

namespace App\Console\Commands\Transfer;

use App\Models\ProductCategory;
use Illuminate\Console\Command;

class AddProductCategoryType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product-category:add-type';

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
            ->each(function (ProductCategory $productCategory) {
                $productCategory->update([
                    'type' => 'outgoing',
                ]);
            });
    }
}
