<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'method', 'code', 'body'
    ];

    protected $dates = [
        'date_created'
    ];
}
