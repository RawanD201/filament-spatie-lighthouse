<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lighthouse_audit_results', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->json('raw_results')->nullable();         // null when raw_results_driver = 'filesystem'
            $table->string('raw_result_path')->nullable();   // path on disk when raw_results_driver = 'filesystem'
            $table->json('scores');
            $table->unsignedTinyInteger('performance_score')->nullable();
            $table->unsignedTinyInteger('accessibility_score')->nullable();
            $table->unsignedTinyInteger('best_practices_score')->nullable();
            $table->unsignedTinyInteger('seo_score')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index('url');
            $table->index('finished_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lighthouse_audit_results');
    }
};
