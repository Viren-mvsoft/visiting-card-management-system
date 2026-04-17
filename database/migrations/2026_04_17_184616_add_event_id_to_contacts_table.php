<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('company_name')->constrained('events')->nullOnDelete();
        });

        // Migrate existing data
        $contacts = DB::table('contacts')->whereNotNull('event')->get();
        foreach ($contacts as $contact) {
            $eventName = trim($contact->event);
            if (!empty($eventName)) {
                $eventId = DB::table('events')->updateOrInsert(
                    ['name' => $eventName],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                
                // Fetch the ID (updateOrInsert doesn't return it)
                $eventId = DB::table('events')->where('name', $eventName)->value('id');
                
                DB::table('contacts')
                    ->where('id', $contact->id)
                    ->update(['event_id' => $eventId]);
            }
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('event')->nullable()->after('company_name');
        });

        // Reverse migration
        $contacts = DB::table('contacts')->whereNotNull('event_id')->get();
        foreach ($contacts as $contact) {
            $eventName = DB::table('events')->where('id', $contact->event_id)->value('name');
            DB::table('contacts')
                ->where('id', $contact->id)
                ->update(['event' => $eventName]);
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });
    }
};
