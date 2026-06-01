<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Highlight extends Model
{
    protected $table = 'highlights';

    protected $fillable = [
        'document_id',
        'page_number',
        'text_content',
        'note',
        'position_x',
        'position_y',
        'position_width',
        'position_height',
        'ai_translation',
        'ai_explanation',
        'ai_vocabulary',
        'ai_grammar',
        'ai_idiom_note',
        'color'
    ];

    // Array casting untuk memastikan ai_vocabulary terbaca sebagai JSON objek oleh database
    protected $casts = [
        'ai_vocabulary' => 'array',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}