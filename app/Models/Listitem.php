<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listitem extends Model
{
    use HasFactory;
    protected $fillable = ['created_by','updated_by','duedate','issue','outpart','quantity','plandue_id','po','body','customer','ship_to','prize','pr','flag','remark'];

    public function plandue():BelongsTo
    {
        return $this->belongsTo(Plandue::class);
    }

    public function createBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearchlist($query ,$value)
    {
        return $query->where('issue', 'like', "%{$value}%");
    }
    public function part():BelongsTo{
        return $this->belongsTo(Part::class)
                ->whereColumn('listitems.outpart','parts.outpart')
                ->whereColumn('listitems.customer','parts.customer'); 
    }
}
