<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('title');
        });

        $usedSlugs = [];

        DB::table('events')->orderBy('id')->each(function (object $event) use (&$usedSlugs): void {
            $baseSlug = Str::slug($event->title) ?: 'event';
            $slug = $baseSlug;
            $suffix = 2;

            while (isset($usedSlugs[$slug])) {
                $slug = $baseSlug.'-'.$suffix++;
            }

            $usedSlugs[$slug] = true;
            DB::table('events')->where('id', $event->id)->update(['slug' => $slug]);
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
