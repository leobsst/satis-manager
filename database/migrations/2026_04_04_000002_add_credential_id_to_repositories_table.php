<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->foreignId('repository_credential_id')
                ->nullable()
                ->after('provider')
                ->constrained('repository_credentials')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\RepositoryCredential::class);
            $table->dropColumn('repository_credential_id');
        });
    }
};
