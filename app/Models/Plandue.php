<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plandue extends Model
{
    use HasFactory;
    protected $fillable = ['plan_id', 'status', 'created_by'];

    public function createBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function listitems():HasMany
    {
        return $this->hasMany(Listitem::class);
    }

    public function scopeSearchpland($query, $value){
        $query->where('plan_id','like',"%{$value}%")
        ->orWhereHas('listitems' ,function($query) use ($value){
            $query->where('issue','like',"%{$value}%");
        });
    }

}
