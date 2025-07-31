<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'worker_id', 'date', 
        'check_in', 'check_out', 'overtime_hours', 
        'count_as_two_days', 'notes'
    ];
    
    protected $casts = [
        'date' => 'date',  // Pastikan ini ada
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
