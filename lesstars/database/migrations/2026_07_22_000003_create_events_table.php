<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up():void{Schema::create('events',function(Blueprint $table){$table->id();$table->string('title');$table->text('summary');$table->string('venue');$table->timestamp('starts_at');$table->timestamp('ends_at')->nullable();$table->string('image_url')->nullable();$table->string('status')->default('draft');$table->timestamps();});}public function down():void{Schema::dropIfExists('events');}};
