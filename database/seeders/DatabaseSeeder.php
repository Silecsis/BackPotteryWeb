<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(UserSeeder::class);
        $this->call(MaterialSeeder::class);
        $this->call(PieceSeeder::class);
        $this->call(SaleSeeder::class);
        $this->call(MaterialPieceSeeder::class);

        
    }
}
