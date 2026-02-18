<?php
namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\PostedJob;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Jobs
        $totalJobs  = PostedJob::count();
        $activeJobs = PostedJob::where('status', 'active')->count();

        // Total Applications
        $totalApplications = JobApplication::count();

        $applicationsThisWeek = JobApplication::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();

        // Total Recruiters
        $totalRecruiters = User::where('role', 'recruiter')->count();

        // Hire Rate (example logic)
        $totalHired = JobApplication::where('status', 'hired')->count();
        $hireRate   = $totalApplications > 0
            ? round(($totalHired / $totalApplications) * 100)
            : 0;

        // Recent Applications
        $recentApplications = JobApplication::with(['recruiter', 'job'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($application) {
                return [
                    'candidate_name' => $application->user->name ?? '',
                    'job_title'      => $application->job->title ?? '',
                    'status'         => $application->status,
                    'applied_at'     => $application->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Active Job Postings with applicant count
        $activeJobsList = PostedJob::where('status', '1')
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($job) {
                return [
                    'job_title'  => $job->job_title,
                    'location'   => $job->location,
                    'applicants' => $job->applications_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => [
                'stats'               => [
                    'total_jobs'             => $totalJobs,
                    'active_jobs'            => $activeJobs,
                    'total_applications'     => $totalApplications,
                    'applications_this_week' => $applicationsThisWeek,
                    'total_recruiters'       => $totalRecruiters,
                    'hire_rate'              => $hireRate . '%',
                ],
                'recent_applications' => $recentApplications,
                'active_jobs'         => $activeJobsList,
            ],
        ]);
    }
}
