<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartImage extends Model
{
    use HasFactory;
    protected $table = "part_image";
    protected $fillable = ["img_part","img_path","created_by","updated_by"];
}
