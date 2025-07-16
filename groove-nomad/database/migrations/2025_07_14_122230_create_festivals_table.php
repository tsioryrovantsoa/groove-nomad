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
        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('image')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->json('description'); // Translatable
            $table->string('location')->nullable();
            $table->string('city');
            $table->string('region')->nullable();
            $table->unsignedInteger('page')->default(1);
            $table->string('region_abbr')->nullable();
            $table->timestamps();

            $table->index(['region', 'start_date', 'end_date'], 'festivals_region_dates_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festivals');
        $table->dropIndex('festivals_region_dates_index');
    }
};
