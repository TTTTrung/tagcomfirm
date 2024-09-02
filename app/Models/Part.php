<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Part extends Model
{
    use HasFactory;
    protected $fillable = ['customer','type','partname','outpart','trupart','snp','weight','created_by','updated_by','pl_size','order_type','sale_reps','price_list','bill_to','pallet_name'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearchPart($query, $value)
    {
        $query->where('type','like',"%{$value}%")
            ->orWhere('partname','like',"%{$value}%")
            ->orWhere('outpart','like',"%{$value}%")
            ->orWhere('trupart','like',"%{$value}%");
    }
}
