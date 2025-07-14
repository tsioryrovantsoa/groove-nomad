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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('last_name');              // nom
            $table->string('first_name');             // prénom
            $table->string('address');                // adresse
            $table->string('city');                   // ville
            $table->string('passport_country');       // pays du passeport (ISO2 ou nom)
            $table->string('nationality');            // nationalité (ISO2 ou nom)
            $table->string('phone_number');           // numéro de téléphone
            $table->enum('gender', ['Male', 'Female', 'Other']); // genre
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed']); // situation matrimoniale
            $table->date('birth_date')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->boolean('terms_accepted')->default(false); // acceptation des CGU/données

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
