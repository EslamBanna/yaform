<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFormQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('form_id');
            $table->string('title');
            $table->string('description')->nullable(true);
            $table->integer('type')->default(0)->comment('0=>ShortAnswer,1=>Paragraph,2=>MultipleChoice,3=>Checkboxes,4=>Dropdown,5=>Date,6=>Time,7=>PhoneNumber,8=>video,9=>image,10=>head');
            $table->boolean('required')->default(0)->comment('0 not required, 1 required');
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
        Schema::dropIfExists('form_questions');
    }
}
