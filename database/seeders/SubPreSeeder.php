<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SubPreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\PreNSubDealer::factory()->create([
            'user_id'=>'1',
            'username'=>'predealer',
            // 'type'=>'pre',
            'first_name' => 'Mr pre',
            'last_name'=>'dealer',
            'email' => 'predealer@mail.com',
            'phone'=> '01XXXXXXXX0',
            'password' => Hash::make('12345678'),
            'opening_balance'=>1500
            //'last_login_ip' => 'Male',
        ]);
        $user = \App\Models\PreNSubDealer::factory()->create([
            'user_id'=>'2',
            'username'=>'subdealer',
            // 'type'=>'sub',
            // 'sub_dealer_type_id'=>1,
            'first_name' => 'Mr Sub',
            'last_name'=>'Dealer',
            'email' => 'subdealer@mail.com',
            'phone'=> '01XXXXXXXX1',
            'password' => Hash::make('12345678'),
            'opening_balance'=>2000
        ]);
    }
}
