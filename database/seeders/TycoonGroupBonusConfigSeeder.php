<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use DB;
class TycoonGroupBonusConfigSeeder extends Seeder {
	
	public function run() 
	{
		DB::table('tycoon_group_bonus_configs')->delete();

		$tycoon_group_bonus = array(
			array('group_no' => 1, 'bonus_percentage' => 1.82),
			array('group_no' => 2, 'bonus_percentage' => 1.68),
			array('group_no' => 3, 'bonus_percentage' => 1.46),
			array('group_no' => 4, 'bonus_percentage' => 0.91),
			array('group_no' => 5, 'bonus_percentage' => 0.55),
			array('group_no' => 6, 'bonus_percentage' => 0.7),
			array('group_no' => 7, 'bonus_percentage' => 0.37),
			array('group_no' => 8, 'bonus_percentage' => 0.37),
			array('group_no' => 9, 'bonus_percentage' => 0.19),
			array('group_no' => 10, 'bonus_percentage' => 0.19),
			array('group_no' => 11, 'bonus_percentage' => 0.19),
			array('group_no' => 12, 'bonus_percentage' => 0.19),
			array('group_no' => 13, 'bonus_percentage' => 0.19),
			array('group_no' => 14, 'bonus_percentage' => 0.10),
			array('group_no' => 15, 'bonus_percentage' => 0.10),
			array('group_no' => 16, 'bonus_percentage' => 0.10),
			array('group_no' => 17, 'bonus_percentage' => 0.10),
			array('group_no' => 18, 'bonus_percentage' => 0.10),
			array('group_no' => 19, 'bonus_percentage' => 0.10),
			array('group_no' => 20, 'bonus_percentage' => 0.10)
		);

		DB::table('tycoon_group_bonus_configs')->insert($tycoon_group_bonus);
	}
}