<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    const UNITS = [
        "A"=>"套",
        "B"=>"個",
        "C"=>"組",
        "D"=>"對",
        "E"=>"式",
        "F"=>"碗",
        "G"=>"包",
        "H"=>"盤",
        "I"=>"支",
        "J"=>"瓶",
        "K"=>"粒",
        "L"=>"捆",
        "M"=>"鍋",
        "N"=>"盒",
        "O"=>"付",
        "P"=>"張",
        "Q"=>"雙",
        "R"=>"只",
        "S"=>"條",
        "T"=>"打",
        "U"=>"束",
        "V"=>"斤",
        "W"=>"份",
        "X"=>"桶",
        "Z"=>"罐",
        "A1"=>"隻",
        "B1"=>"箱",
        "A2"=>"樣",
        "Y"=>"桌",
        "A3"=>"塊",
        "A4"=>"天",
        "A5"=>"尺",
        "A6"=>"杯",
        "A7"=>"袋",
        "A8"=>"面",
        "A9"=>"把",
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::query()
            ->truncate();

        foreach (self::UNITS as $key => $unit) {
            Unit::query()
                ->create([
                    'tracking_number' => $key,
                    'name' => $unit
                ]);
        }
    }
}
