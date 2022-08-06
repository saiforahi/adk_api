<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;
class DealerBonusConfigSeeder extends Seeder {
	
	public function run() 
	{
		DB::table('dealer_bonus_configs')->delete();

		$dealer_bonus = array(
			array('dealer_type_id' => 1, 'capital' => 5000000, 'product' => 5000000, 'commission' => 20),
			array('dealer_type_id' => 2, 'capital' => 500000, 'product' => 500000, 'commission' => 30),
			array('dealer_type_id' => 3, 'capital' => 50000, 'product' => 50000, 'commission' => 50)
		);

		DB::table('dealer_bonus_configs')->insert($dealer_bonus);
	}
}