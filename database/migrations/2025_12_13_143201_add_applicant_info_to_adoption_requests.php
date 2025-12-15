<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('adoption_requests', function (Blueprint $table) {

            $table->string('applicant_name')->nullable();  // ✅ ADD
            $table->string('phone_number')->nullable();    // ✅ ADD
        });
    }

    public function down()
    {
        Schema::table('adoption_requests', function (Blueprint $table) {
            $table->dropColumn(['applicant_name', 'phone_number']);
        });
    }
};