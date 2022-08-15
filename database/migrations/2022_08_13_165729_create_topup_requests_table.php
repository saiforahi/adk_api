<?php

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
        Schema::create('topup_requests', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('request_from');
            $table->enum("type",["MFS","Bank"]);
            $table->string("mfs_acc_no")->nullable();
            $table->string("mfs_trx_no")->nullable();
            $table->string("bank_acc_no")->nullable();
            $table->string("bank_branch")->nullable();
            $table->string("bank_name")->nullable();
            $table->string("document")->nullable();
            $table->string("document_download_link")->nullable();
            $table->float("amount", 10, 2)->nullable();
            $table->enum("status",["PENDING","APPROVED","PROCESSED"])->default("PENDING");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topup_requests');
    }
};
