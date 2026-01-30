<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('chatbot_trainings', function (Blueprint $table) {
    $table->id();
    $table->string('intent_name');
    $table->text('training_text');
    $table->string('source')->nullable(); // UI, import, etc.
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_trainings');
    }
};
