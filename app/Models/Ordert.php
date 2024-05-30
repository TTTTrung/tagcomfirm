<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordert extends Model
{
    use HasFactory;
    protected $connection = 'oracle';
    protected $table = 'oe_transaction_types_tl';
}
