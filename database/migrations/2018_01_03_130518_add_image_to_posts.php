<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageToPosts extends Migration
{

    public function up()
    {
        Schema::table('posts', function (Blueprint $table){
            $table->string('image')->nullable();
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table){
            $table->dropColumn('image');
        });
    }
}
