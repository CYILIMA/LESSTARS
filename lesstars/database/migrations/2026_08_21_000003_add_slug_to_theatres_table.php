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
        Schema::table('theatres', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('title');
        });

        foreach (DB::table('theatres')->select('id', 'title')->orderBy('id')->get() as $theatre) {
            $base = Str::slug($theatre->title) ?: 'theatre';
            $slug = $base;
            $suffix = 2;

            while (DB::table('theatres')->where('slug', $slug)->where('id', '!=', $theatre->id)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            DB::table('theatres')->where('id', $theatre->id)->update(['slug' => $slug]);
        }

        Schema::table('theatres', function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('theatres', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
