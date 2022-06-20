<?php

use App\Models\SubDealerGroup;
use App\Models\SubDealerTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_dealer_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        SubDealerTypes::create(['name'=>'International']);
        SubDealerTypes::create(['name'=>'National']);
        SubDealerTypes::create(['name'=>'Divisional']);
        SubDealerTypes::create(['name'=>'District']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_dealer_types');
    }
};
