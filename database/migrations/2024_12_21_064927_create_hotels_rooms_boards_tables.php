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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_hotel')->unique();
            $table->string('code');
            $table->string('remark');
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('main_region_id');
            $table->unsignedBigInteger('category_id');
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_room');
            $table->unsignedBigInteger('hotel_id');
            $table->string('code');
            $table->string('remark');
            $table->integer('quota')->default(0);
            $table->boolean('on_request')->default(false);
            $table->integer('min_paid_adult');
            $table->integer('max_adult');
            $table->integer('max_child_age')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('hotel_id')->references('id_hotel')->on('hotels')->onDelete('cascade');
        });

        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_board');
            $table->unsignedBigInteger('room_type_id');
            $table->string('code');
            $table->string('remark');
            $table->timestamps();

            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('hotels');
    }
};
