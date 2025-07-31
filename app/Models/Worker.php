<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'daily_salary',
    ];

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'mandor_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
