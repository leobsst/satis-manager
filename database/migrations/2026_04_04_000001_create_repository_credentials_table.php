<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repository_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            $table->text('token');
            $table->text('username')->nullable();
            $table->string('domain')->nullable()->comment('Required for custom provider');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repository_credentials');
    }
};
