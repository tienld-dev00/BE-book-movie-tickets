<?php

use App\Enums\MovieStatus;
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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('release_date');
            $table->unsignedSmallInteger('category_id');
            $table->unsignedTinyInteger('age_limit');
            $table->unsignedTinyInteger('duration');
            $table->text('description');
            $table->unsignedTinyInteger('status')->default(MovieStatus::SHOW);
            $table->text('image');
            $table->text('trailer');
            $table->text('slug');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
};
