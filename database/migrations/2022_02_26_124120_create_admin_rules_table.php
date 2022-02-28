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
        Schema::create('admin_rules', function (Blueprint $table) {
            $table->id();

            $table->string("name")->unique();
            $table->string("module");
            $table->string("controller");
            $table->string("action");
            $table->string("controller_title");
            $table->string("action_title");

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
        Schema::dropIfExists('admin_rules');
    }
};
