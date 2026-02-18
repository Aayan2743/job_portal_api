<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'postedjob_id',
        'recruiter_id',
        'full_name',
        'email',
        'phone',
        'years_of_experience',
        'resume',
        'cover_letter',
        'status',
    ];

    public function job()
    {
        return $this->belongsTo(PostedJob::class, 'postedjob_id');
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

}
