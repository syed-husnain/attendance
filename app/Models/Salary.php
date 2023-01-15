<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

protected $fillable = [
        'user_id', 
        'from_date',
        'to_date', 
        'basic_salary',
        'travel_allowance', 
        'medical_allowance', 
        'bonus',
        'working_days', 
        'working_hours', 
        'late',
        'absent',
        'salary'
];

public function user()
    {
        return $this->belongsTo(User::class);
    }
}
