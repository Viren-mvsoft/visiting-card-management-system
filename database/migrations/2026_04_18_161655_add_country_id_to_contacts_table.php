<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('user_id')->constrained('countries')->nullOnDelete();
        });

        // Migrate existing data (attempt to match by name)
        DB::statement('UPDATE contacts SET country_id = (SELECT id FROM countries WHERE name = contacts.country LIMIT 1)');

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('country')->nullable()->after('country_id');
        });

        // Revert data
        DB::statement('UPDATE contacts SET country = (SELECT name FROM countries WHERE id = contacts.country_id LIMIT 1)');

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('country_id');
        });
    }
};
