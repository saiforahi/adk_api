<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleAndPermissionsSeeder;
use Database\Seeders\TycoonSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Category::factory(10)->create();
        SubCategory::factory(10)->create();
        SubSubCategory::factory(10)->create();
        ProductGroup::factory(10)->create();
        Product::factory(5)->create();
        $this->call(RoleAndPermissionsSeeder::class);
        $this->call(TycoonSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(ThanaSeeder::class);
        $this->call(UnionSeeder::class);
        $this->call(DealerBonusConfigSeeder::class);
        $this->call(TycoonBonusConfigSeeder::class);
        $this->call(TycoonGroupBonusConfigSeeder::class);
        $this->call(TycoonStarMonthlyBonusConfigSeeder::class);
    }
}
