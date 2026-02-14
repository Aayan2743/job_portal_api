<?php
namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\PostedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postedjob_id'        => 'required|exists:postedjobs,id',
            'full_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:20',
            'years_of_experience' => 'required|integer|min:0',
            'resume'              => 'required|file|mimes:pdf,doc,docx|max:10240',
            'cover_letter'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        // Get Job + Recruiter automatically
        $job = PostedJob::find($request->postedjob_id);

        // Upload Resume
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')
                ->store('resumes', 'public');
        }

        $application = JobApplication::create([
            'postedjob_id'        => $job->id,
            'recruiter_id'        => $job->recruiter_id,
            'full_name'           => $request->full_name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'years_of_experience' => $request->years_of_experience,
            'resume'              => $resumePath,
            'cover_letter'        => $request->cover_letter,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Application submitted successfully',
            'data'    => $application,
        ]);
    }

    public function index()
    {
        $applications = JobApplication::with(['job', 'recruiter'])
            ->latest()
            ->get();

        return response()->json($applications);
    }

    public function show($id)
    {
        $application = JobApplication::with(['job', 'recruiter'])
            ->find($id);

        if (! $application) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($application);
    }

}
