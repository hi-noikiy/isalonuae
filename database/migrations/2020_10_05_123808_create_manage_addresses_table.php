<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManageAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('map_title')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('addr_type')->nullable();
            $table->string('addr_title')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('address3')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('manage_addresses');
    }
}
