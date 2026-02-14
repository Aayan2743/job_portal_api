<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostedJob extends Model
{
    use SoftDeletes;

    protected $table = 'postedjobs';

    protected $fillable = [
        'recruiter_id',
        'job_title',
        'company',
        'location',
        'job_type',
        'salary_min',
        'salary_max',
        'job_description',
        'requirements',
        'status',
    ];

    protected $casts = [
        'salary_min' => 'float',
        'salary_max' => 'float',
        'status'     => 'boolean',
    ];

    // Relationship
    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'postedjob_id');
    }
}
