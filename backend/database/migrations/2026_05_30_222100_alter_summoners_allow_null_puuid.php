<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE summoners ALTER COLUMN puuid DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE summoners SET puuid = CONCAT('pending_', summoner_id) WHERE puuid IS NULL");
        DB::statement('ALTER TABLE summoners ALTER COLUMN puuid SET NOT NULL');
    }
};
