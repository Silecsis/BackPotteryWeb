<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PieceSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        //Pieza 1
        DB::table('pieces')->insert(['name'=>'Yin y yang',
        'user_id'=>'1',
        'description'=>'Una pieza realizada como contenedor de cristales',
        'img'=>'carrusel1.jpg',
        'sold'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Pieza 2
        DB::table('pieces')->insert(['name'=>'Lampara de aladin',
        'user_id'=>'2',
        'description'=>'Es una imitación de las piezas cerámicas Bauhaus. Realizada a torno y montaje.',
        'img'=>'carrusel2.jpg',
        'sold'=>true,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Pieza 3
        DB::table('pieces')->insert(['name'=>'Psique',
        'user_id'=>'2',
        'description'=>'Pieza que simboliza el arquetipo de Carl Young "Persona" y "Sombra". Tamaño de unos 50 cm de alto.',
        'img'=>'carrusel4.jpg',
        'sold'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Pieza 4
        DB::table('pieces')->insert(['name'=>'Tarro de Winnie de Pooh',
        'user_id'=>'6',
        'description'=>'Pieza de cerámica realizada con la tecnica decorativa de carbonización.',
        'img'=>'gal1.jpg',
        'sold'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Pieza 5
        DB::table('pieces')->insert(['name'=>'Jarra imposible',
        'user_id'=>'7',
        'description'=>'Pieza cerámica basada en el concepto de "arquitectura imposible". Simplemente decorativa. Contiene decoración representativa de mi familia. Tamaño de unos 60 cm de alto.',
        'img'=>'gal2.jpg',
        'sold'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Pieza 6
        DB::table('pieces')->insert(['name'=>'Salsera lirio',
        'user_id'=>'8',
        'description'=>'Se trata de una salsera. Realizada a montaje.',
        'img'=>'gal3.jpg',
        'sold'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Pieza 7
        DB::table('pieces')->insert(['name'=>'Pájaro abstracto',
        'user_id'=>'9',
        'description'=>'Pieza contruida a planchas con decoración y figuración contructiva abstracta.',
        'img'=>'gal4.jpg',
        'sold'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Pieza 8
        DB::table('pieces')->insert(['name'=>'Tazas feministas',
        'user_id'=>'10',
        'description'=>'Lote de tazas con mensajes anti-machistas para el día de la mujer. Su realización ha sido a medida de calcas y moldes.',
        'img'=>'gal5.jpg',
        'sold'=>false,
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
