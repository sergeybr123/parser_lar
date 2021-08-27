<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
//    use HasFactory;

    protected $fillable = [
        'name', 'link', 'description', 'author'
    ];

    protected $dates = [
        'date_created'
    ];
}
