<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->json('excluded_branches')
                ->nullable()
                ->after('repository_credential_id')
                ->comment('Glob patterns of branch names to exclude from the build (e.g. dependabot/*)');
        });
    }

    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->dropColumn('excluded_branches');
        });
    }
};
