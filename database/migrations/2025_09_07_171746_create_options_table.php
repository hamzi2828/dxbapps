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
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index(['question_id', 'display_order']);
            $table->index(['question_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
