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
        DB::table('units')->insert([
            'id_owner' => '1',
            'name' => 'APT 100'
        ]);
        DB::table('units')->insert([
            'id_owner' => '1',
            'name' => 'APT 101'
        ]);
        DB::table('units')->insert([
            'id_owner' => '0',
            'name' => 'APT 200'
        ]);
        DB::table('units')->insert([
            'id_owner' => '0',
            'name' => 'APT 201'
        ]);

        
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Academia',
            'cover' => 'gym.jpg',
            'days' => '1,2,4,5',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Piscina',
            'cover' => 'pool.jpg',
            'days' => '1,2,3,4,5',
            'start_time' => '07:00:00',
            'end_time' => '23:00:00'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Churrasqueira',
            'cover' => 'barbecue.jpg',
            'days' => '4,5,6',
            'start_time' => '13:00:00',
            'end_time' => '23:00:00'
        ]);

        
        DB::table('walls')->insert([
            'title' => 'Aviso de Teste',
            'body' => 'Um aviso inicial de teste...',
            'datecreated' => '2021-06-06 13:00:00'
        ]);
        DB::table('walls')->insert([
            'title' => 'Meu alerta para moradores',
            'body' => 'Um segundo aviso de teste...',
            'datecreated' => '2021-06-06 08:00:00'
        ]);
    }
}
