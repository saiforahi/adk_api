<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;
class TycoonStarMonthlyBonusConfigSeeder extends Seeder {
	
	public function run() 
	{
		DB::table('tycoon_star_monthly_bonus_configs')->delete();

		$tycoon_bonus = array(
			array('star_no' => 1, 'bonus_percentage' => 40),
			array('star_no' => 2, 'bonus_percentage' => 20),
			array('star_no' => 3, 'bonus_percentage' => 15),
			array('star_no' => 4, 'bonus_percentage' => 10),
			array('star_no' => 5, 'bonus_percentage' => 8),
			array('star_no' => 6, 'bonus_percentage' => 5),
			array('star_no' => 7, 'bonus_percentage' => 2)
		);

		DB::table('tycoon_star_monthly_bonus_configs')->insert($tycoon_bonus);
	}
}