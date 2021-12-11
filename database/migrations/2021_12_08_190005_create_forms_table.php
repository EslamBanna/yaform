<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title')->nullable(true);
            $table->string('description')->nullable(true);
            $table->string('logo')->nullable(true);
            $table->integer('type')->default(0)->comment('0 => classic, 1 => card, 2 => quiz, 3 => contact form');
            $table->string('facebook_link')->nullable(true);
            $table->string('twitter_link')->nullable(true);
            $table->string('instgram_link')->nullable(true);
            $table->string('response_msg')->default('Your answer has been submitted')->nullable(true);
            // $table->timestamps();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
