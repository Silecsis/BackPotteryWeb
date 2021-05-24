<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Mensaje 1
        DB::table('messages')->insert([
            'title'=>'Prueb',
            'msg'=>'Hola',
            'read'=>true,
            'user_id_sender'=>'1',
            'user_id_receiver'=>'2',
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Mensaje 2
        DB::table('messages')->insert([
            'title'=>'Prueb2',
            'msg'=>'Hola soy MJ',
            'read'=>false,
            'user_id_sender'=>'3',
            'user_id_receiver'=>'2',
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Mensaje 3
        DB::table('messages')->insert([
            'title'=>'Prob3',
            'msg'=>'Que pasa cara pasa',
            'read'=>false,
            'user_id_sender'=>'4',
            'user_id_receiver'=>'2',
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //Mensaje 4
        DB::table('messages')->insert([
            'title'=>'Prob4',
            'msg'=>'Hola caracola',
            'read'=>false,
            'user_id_sender'=>'5',
            'user_id_receiver'=>'2',
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
