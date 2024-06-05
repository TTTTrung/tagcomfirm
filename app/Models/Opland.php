<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opland extends Model
{
    use HasFactory;
    protected $connection = 'oracle';
    protected $table = 'tru_so_header_stg';
    protected $primarykey = "legacy_so_num";
    public $incrementing = false;
    protected $keytype = "string";
    public $timestamps= false;
    protected $fillable = [
        'legacy_so_num',
        'customer_no',
        'bill_to',
        'transaction_type',
        'order_date',
        'price_list',
        'salesperson',
        'warehouse',
        'customer_po_no'
        ];

    public function olist():HasMany
    {
       return $this->hasMany(Olist::class,'legacy_so_num','legacy_so_num'); 
    }
}
