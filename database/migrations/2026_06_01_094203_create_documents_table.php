<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // Otomatis membuat BIGSERIAL PRIMARY KEY
            
            // Relasi ke tabel users (Otomatis BIGINT)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            $table->string('title');
            $table->string('filename');
            $table->text('content')->nullable();
            
            $table->integer('total_pages')->default(0);
            $table->integer('last_page')->default(1);
            $table->timestamp('last_read_at')->nullable();
            
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};