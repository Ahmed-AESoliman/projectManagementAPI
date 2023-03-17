<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('user_mobile')->nullable();
            $table->string('role');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('active')->default(0);
            $table->boolean('company_owner')->default(0);
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            // for creator users is admin
            $table->boolean('is_mangement_team')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
