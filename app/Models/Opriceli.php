<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opriceli extends Model
{
    use HasFactory;
    protected $connection = 'oracle';
    protected $table = 'qp_list_headers_tl';
}
