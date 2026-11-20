<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProcessCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessCategory extends Model
{
    /** @use HasFactory<ProcessCategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];
}
