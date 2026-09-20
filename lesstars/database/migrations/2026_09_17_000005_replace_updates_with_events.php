<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->text('body')->nullable()->after('summary');
            $table->string('author_name')->nullable()->after('venue');
            $table->text('author_bio')->nullable()->after('author_name');
            $table->string('thumbnail_path')->nullable()->after('author_bio');
            $table->timestamp('published_at')->nullable()->after('thumbnail_path');
        });

        DB::table('updates')->orderBy('id')->each(function (object $update): void {
            DB::table('events')->insert([
                'title' => $update->title,
                'summary' => $update->summary,
                'body' => $update->body,
                'venue' => 'Les Stars Du Theatre',
                'author_name' => $update->author_name,
                'author_bio' => $update->author_bio,
                'thumbnail_path' => $update->thumbnail_path,
                'published_at' => $update->published_at,
                'starts_at' => $update->published_at ?? $update->created_at,
                'ends_at' => null,
                'image_url' => null,
                'status' => $update->status,
                'created_at' => $update->created_at,
                'updated_at' => $update->updated_at,
            ]);
        });

        Schema::dropIfExists('updates');
    }

    public function down(): void
    {
        Schema::create('updates', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('summary');
            $table->text('body');
            $table->timestamp('published_at')->nullable();
            $table->string('status')->default('draft');
            $table->string('author_name')->nullable();
            $table->text('author_bio')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn(['body', 'author_name', 'author_bio', 'thumbnail_path', 'published_at']);
        });
    }
};
