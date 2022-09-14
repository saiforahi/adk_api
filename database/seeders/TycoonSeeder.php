<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TycoonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Tycoon::factory()->create([
            'user_id'=>'1',
            'username'=>'mastertycoon',
            'first_name' => 'Master',
            'last_name'=>'Tycoon',
            'email' => 'mastertycoon@mail.com',
            'phone'=> '01XXXXXXXX0',
            'password' => Hash::make('12345678'),
            'opening_balance'=>0
        ]);
    }
}
