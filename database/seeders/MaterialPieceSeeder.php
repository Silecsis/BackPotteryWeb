<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialPieceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('material_piece')->insert([
            'piece_id'=>'1',
            'material_id'=>'2'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'1',
            'material_id'=>'4'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'1',
            'material_id'=>'3'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'2',
            'material_id'=>'2'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'2',
            'material_id'=>'1'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'3',
            'material_id'=>'1'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'3',
            'material_id'=>'2'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'3',
            'material_id'=>'4'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'4',
            'material_id'=>'4'
        ]);

        DB::table('material_piece')->insert([
            'piece_id'=>'5',
            'material_id'=>'4'
        ]);

        for($i=6;$i<=8;$i++){
            DB::table('material_piece')->insert([
                'piece_id'=>$i,
                'material_id'=>$i-1
            ]);
        }
    }
}
