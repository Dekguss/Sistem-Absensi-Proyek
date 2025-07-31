<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'mandor_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function mandor()
    {
        return $this->belongsTo(Worker::class, 'mandor_id');
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
