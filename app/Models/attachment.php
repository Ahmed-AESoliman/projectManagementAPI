<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class attachment extends Model
{
    use HasFactory;
    protected $appends = ['file_link'];

    public function getFileLinkAttribute()
    {
        $path = Storage::disk()->url($this->file_path);
        return $path;
    }

    protected $fillable = [
        'file_path',
        'file_name',
        'attachable_id',
        'attachable_type',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
