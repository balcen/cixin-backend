<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    const CATEGORIES = [
        "A00" => "萬安(全部)",
        "B00" => "素(組合)",
        "C00" => "葷(組合)",
        "D00" => "組合產品",
        "E00" => "單項產品",
        "F00" => "菜盤組合",
        "G00" => "水果組合系列",
        "H00" => "扣除項目"
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductCategory::query()
            ->truncate();

        foreach (self::CATEGORIES as $key => $category) {
            ProductCategory::query()
                ->create([
                    'tracking_number' => $key,
                    'name' => $category,
                ]);
        }
    }
}
