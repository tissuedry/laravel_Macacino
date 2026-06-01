<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('highlights', function (Blueprint $table) {
            $table->id(); // Otomatis membuat BIGSERIAL PRIMARY KEY
            
            // Relasi ke tabel documents (Otomatis BIGINT)
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            
            $table->integer('page_number');
            $table->text('text_content'); 
            $table->text('note')->nullable(); 
            
            // Koordinat Posisi Stabilo
            $table->double('position_x')->default(0.0);
            $table->double('position_y')->default(0.0);
            $table->double('position_width')->default(0.0);
            $table->double('position_height')->default(0.0);
            
            // Kolom Hasil Analisis Komprehensif AI
            $table->text('ai_translation')->nullable();
            $table->text('ai_explanation')->nullable();
            
            // Menyimpan log kosa kata JSON (sesuai struktur data JSONB di Postgres)
            $table->json('ai_vocabulary')->nullable(); 
            
            $table->text('ai_grammar')->nullable(); 
            $table->text('ai_idiom_note')->nullable();
            
            $table->string('color', 50)->default('rgba(255, 213, 79, 0.3)'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('highlights');
    }
};