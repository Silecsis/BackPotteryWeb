<?php

namespace Database\Seeders;


use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Material 1
        DB::table('materials')->insert(['name'=>'Arcilla Bonita',
        'type_material'=>'arcilla',
        'temperature'=>'900',
        'toxic'=>true,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Material 2
        DB::table('materials')->insert(['name'=>'Color azul',
        'type_material'=>'óxido PE',
        'temperature'=>'1200',
        'toxic'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Material 3
        DB::table('materials')->insert(['name'=>'Color verde',
        'type_material'=>'óxido de cobre',
        'temperature'=>'900',
        'toxic'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Material 4
        DB::table('materials')->insert(['name'=>'Arcilla blanca',
        'type_material'=>'arcilla',
        'temperature'=>'1200',
        'toxic'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        for($i=5;$i<=10;$i++){
            DB::table('materials')->insert(['name'=>'name'.$i,
            'type_material'=>'type_material'.$i,
            'temperature'=>'1200',
            'toxic'=>false,
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);
        }
    }
}
