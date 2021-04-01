<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //User 1
        DB::table('users')->insert(['name'=>'MJ',
        'email'=>'mj@gmail.com',
        'email_verified_at'=>'1991/02/02',
        'password'=>Hash::make("MJ"),
        'type'=>'user',
        'nick'=>'mjnick',
        'img'=>'1614332898final.png',
        'remember_token'=>'rememberMJ',
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //User 2
        DB::table('users')->insert(['name'=>'Campon',
        'email'=>'campon@gmail.com',
        'email_verified_at'=>'1991/02/02',
        'password'=>Hash::make("MJ"),
        'type'=>'admin',
        'nick'=>'camponnick',
        'img'=>'1614335943final.png',
        'remember_token'=>'rememberCampon',
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //User 3
        DB::table('users')->insert(['name'=>'garcia',
        'email'=>'garcia@gmail.com',
        'email_verified_at'=>'1991/02/02',
        'password'=>Hash::make("MJ"),
        'type'=>'user',
        'nick'=>'garcianick',
        'img'=>'1614336156IMG_20140122_183237.jpg',
        'remember_token'=>'rememberGarcia',
        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);

        //5 Usuarios más
        for($i=1;$i<=5;$i++){
            DB::table('users')->insert(['name'=>'name'.$i,
            'email'=>'gmail@gmail.com'.$i,
            'email_verified_at'=>'1991/02/02',
            'password'=>Hash::make("MJ"),
            'type'=>'user',
            'nick'=>'nick'.$i,
            'remember_token'=>'remember'.$i,
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);
        }

        //5 admins más
        for($i=6;$i<=10;$i++){
            DB::table('users')->insert(['name'=>'name'.$i,
            'email'=>'gmail@gmail.com'.$i,
            'email_verified_at'=>'1991/02/02',
            'password'=>Hash::make("MJ"),
            'type'=>'admin',
            'nick'=>'nick'.$i,
            'remember_token'=>'remember'.$i,
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s')]);
        }
        
    }
}
