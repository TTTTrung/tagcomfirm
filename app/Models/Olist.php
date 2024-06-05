<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Olist extends Model
{
    use HasFactory;
    protected $connection = "oracle";
    protected $table = "tru_so_lines_stg";
    // protected $primarykey = "legacy_so_num";
    public $incrementing = false;
    protected $keytype = "string";
    public $timestamps= false;
    protected $fillable = ['item_code','qty','price_unit','customer_part_number','po_number','issue_number','line_num','pr_number'];

    public function oplan():BelongsTo
    {
        return $this->belongsTo(Opland::class,'legacy_so_num','legacy_so_num');
    }
}