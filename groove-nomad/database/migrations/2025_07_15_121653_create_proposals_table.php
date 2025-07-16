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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->onDelete('cascade');
            $table->foreignId('festival_id')->nullable()->constrained()->onDelete('set null');
            $table->text('prompt_text');
            $table->text('response_text');
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('pending');
            $table->string('quotation_pdf')->nullable();
            $table->timestamp('send_email_at')->nullable();
            $table->timestamp('email_read_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->index(['request_id', 'status', 'created_at'], 'proposals_request_status_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
        $table->dropIndex('proposals_request_status_created_index');
    }
};
