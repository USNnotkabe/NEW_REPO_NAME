<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('adoption_requests', function (Blueprint $table) {
            $table->string('valid_id_1')->nullable();
            $table->string('valid_id_2')->nullable();
        });
    }

    public function down()
    {
        Schema::table('adoption_requests', function (Blueprint $table) {
            $table->dropColumn(['valid_id_1', 'valid_id_2']);
        });
    }
};
