<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_name',
        'parent_id',
    ];
    public function subCategory()
    {
        return $this->belongsTo(OrderCategory::class, 'parent_id');
    }
}
