<?php
namespace App\Http\Controllers;

use App\Models\PostedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostedJobController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_title'       => 'required|string|max:255',
            'company'         => 'required|string|max:255',
            'location'        => 'required|string|max:255',
            'job_type'        => 'required|in:full-time,part-time,contract,internship',
            'salary_min'      => 'nullable|numeric|min:0',
            'salary_max'      => 'nullable|numeric|min:0|gte:salary_min',
            'job_description' => 'required|string',
            'requirements'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = Auth::user();

        if ($user->role != 'recruiter') {
            return response()->json([
                'success' => false,
                'message' => 'Only recruiters can post jobs',
            ], 403);
        }

        $job = PostedJob::create([
            'recruiter_id'    => $user->id,
            'job_title'       => $request->job_title,
            'company'         => $request->company,
            'location'        => $request->location,
            'job_type'        => $request->job_type,
            'salary_min'      => $request->salary_min,
            'salary_max'      => $request->salary_max,
            'job_description' => $request->job_description,
            'requirements'    => $request->requirements,
            'status'          => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job posted successfully',
            'data'    => $job,
        ]);
    }

    /* ================= MY JOBS ================= */

    public function myJobs()
    {
        $jobs = PostedJob::with('recruiter')->where('recruiter_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $jobs,
        ]);
    }

    /* ================= ALL JOBS ================= */
    public function allJobs()
    {
        $jobs = PostedJob::with('recruiter')
            ->withCount('applications')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $jobs,
        ]);
    }

    /* ================= ALL JOBS ================= */
    public function openJobs()
    {
        $jobs = PostedJob::with('recruiter')
            ->withCount('applications') // 👈 this adds applications_count
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $jobs,
        ]);
    }

    /* ================= JOB APPLICANTS ================= */

    public function jobApplicants($jobId)
    {
        $job = PostedJob::with(['applications'])
            ->withCount('applications')
            ->find($jobId);

        if (! $job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'job'     => $job,

        ]);
    }

    /* ================= UPDATE ================= */

    public function update(Request $request, $id)
    {
        $job = PostedJob::where('recruiter_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (! $job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
            ], 404);
        }

        $job->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully',
        ]);
    }

    /* ================= DELETE (Soft) ================= */

    public function destroy($id)
    {
        $job = PostedJob::where('recruiter_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (! $job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
            ], 404);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully',
        ]);
    }
}
