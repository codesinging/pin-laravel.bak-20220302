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
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->id();

            $table->nestedSet();

            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('path')->nullable();
            $table->string('url')->nullable();
            $table->bigInteger('sort')->default(0);
            $table->boolean('is_home')->default(false);
            $table->boolean('is_opened')->default(false);
            $table->boolean('status')->default(true);

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
        Schema::dropIfExists('admin_menus');
    }
};
