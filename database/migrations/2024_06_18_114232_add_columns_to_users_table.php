<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->default(null);
            $table->tinyInteger('role')->default(UserRole::USER);
            $table->tinyInteger('status')->default(UserStatus::ACTIVE);
            $table->string('google_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('password')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('role');
            $table->dropColumn('status');
            $table->dropColumn('google_id');
            $table->dropColumn('phone_number');
            $table->string('password')->nullable(false)->change();

        });
    }
};
