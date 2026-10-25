<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkins', function ($table) {
            $table->text('notes')->nullable()->change();
        });

        foreach (DB::table('checkins')->whereNotNull('notes')->get() as $row) {
            $notes = trim((string) $row->notes);

            if ($notes === '' || str_starts_with($notes, '{')) {
                continue;
            }

            DB::table('checkins')->where('id', $row->id)->update([
                'notes' => json_encode(['movement' => $notes], JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('checkins', function ($table) {
            $table->string('notes')->nullable()->change();
        });
    }
};
