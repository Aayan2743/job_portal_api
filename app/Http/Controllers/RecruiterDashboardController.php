<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\PostedJob;
use Illuminate\Http\Request;

class RecruiterDashboardController extends Controller
{
    public function index(Request $request)
    {
        $recruiterId = auth()->id(); // logged recruiter

        // My Jobs Count
        $totalJobs  = PostedJob::where('recruiter_id', $recruiterId)->count();
        $activeJobs = PostedJob::where('recruiter_id', $recruiterId)
            ->where('status', 'active')
            ->count();

        // Total Applicants (for recruiter's jobs)
        $totalApplicants = JobApplication::where('recruiter_id', $recruiterId)->count();

        // Pending Review
        $pendingReview = JobApplication::where('recruiter_id', $recruiterId)
            ->where('status', 'pending')
            ->count();

        // Views Today (if you have views column)
        $viewsToday = PostedJob::where('recruiter_id', $recruiterId)
            ->sum('views_today');

        // 🔥 ONLY 4 JOB LISTINGS
        $jobs = PostedJob::where('recruiter_id', $recruiterId)
            ->withCount('applications')
            ->latest()
            ->take(4) // 👈 ONLY 4 JOBS
            ->get()
            ->map(function ($job) {
                return [
                    'id'           => $job->id,
                    'title'        => $job->job_title,
                    'company'      => $job->company,
                    'location'     => $job->location,
                    'job_type'     => $job->job_type,
                    'salary_range' => $job->salary_min . ' - ' . $job->salary_max,
                    'status'       => $job->status,
                    'applicants'   => $job->applications_count,
                    'created_at'   => $job->created_at->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => [
                'stats'        => [
                    'total_jobs'       => $totalJobs,
                    'active_jobs'      => $activeJobs,
                    'total_applicants' => $totalApplicants,
                    'pending_review'   => $pendingReview,
                    'views_today'      => $viewsToday,
                ],
                'job_listings' => $jobs,
            ],
        ]);
    }
}
