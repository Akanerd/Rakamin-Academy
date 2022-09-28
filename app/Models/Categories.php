<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $fillable = [
      'name', 'user_id'
    ];

    public function userid()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
