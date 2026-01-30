<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('startup', function (Blueprint $table) {
            $table->string('website_link')->nullable()->after('phone_number');
            $table->text('description')->nullable()->after('website_link');
        });
    }

    public function down()
    {
        Schema::table('startup', function (Blueprint $table) {
            $table->dropColumn(['website_link', 'description']);
        });
    }
};
