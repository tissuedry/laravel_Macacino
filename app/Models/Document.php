<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'user_id',
        'title',
        'filename',
        'content',
        'total_pages',
        'last_page',
        'last_read_at'
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];


    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        return asset('storage/uploads/' . $this->filename);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function highlights()
    {
        return $this->hasMany(Highlight::class, 'document_id');
    }
}