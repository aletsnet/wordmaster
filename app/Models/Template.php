<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name', 'slug', 'content', 'description', 'assets_path'];
}
