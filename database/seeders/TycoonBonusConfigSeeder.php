<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;
class TycoonBonusConfigSeeder extends Seeder {
	
	public function run() 
	{
		DB::table('tycoon_bonus_configs')->delete();

		$tycoon_bonus = array(
			array('bonus_type' => 'instant_sale', 'bonus_percentage' => 20),
			array('bonus_type' => 'group_bonus', 'bonus_percentage' => 9),
			array('bonus_type' => 'adk_provident_fund', 'bonus_percentage' => 2),
			array('bonus_type' => 'tycoon_provident_fund', 'bonus_percentage' => 3),
			array('bonus_type' => 'dealer_ref_comm', 'bonus_percentage' => 5),
			array('bonus_type' => 'monthly_star_bonus', 'bonus_percentage' => 2),
			array('bonus_type' => 'monthly_sallary', 'bonus_percentage' => 3.65)
		);

		DB::table('tycoon_bonus_configs')->insert($tycoon_bonus);
	}
}