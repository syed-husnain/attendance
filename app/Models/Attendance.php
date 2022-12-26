<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'due_date', 'check_in','check_out'
    ];
    // protected $casts = [
    //     'check_in' => 'date:hh:mm',
    //     'check_out' => 'date:hh:mm'
    // ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
