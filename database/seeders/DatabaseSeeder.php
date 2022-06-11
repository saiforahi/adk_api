<?php

namespace Database\Seeders;


use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleAndPermissionsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Brand::factory(10)->create();
        Category::factory(10)->create();
        SubCategory::factory(10)->create();
        SubSubCategory::factory(10)->create();
        Attribute::factory(10)->create();
        $this->call(RoleAndPermissionsSeeder::class);
    }
}
