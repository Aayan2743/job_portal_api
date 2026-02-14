<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RecruiterController extends Controller
{
    /* ================= CREATE ================= */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|digits:10|unique:users,phone',
            'password' => 'required|min:6',
            'status'   => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'recruiter',
            'status'   => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recruiter created successfully',
            'data'    => $user,
        ]);
    }

    /* ================= READ ALL ================= */

    public function index()
    {
        $recruiters = User::where('role', 'recruiter')->get();

        return response()->json([
            'success' => true,
            'data'    => $recruiters,
        ]);
    }

    /* ================= READ SINGLE ================= */

    public function show($id)
    {
        $recruiter = User::where('role', 'recruiter')
            ->where('id', $id)
            ->first();

        if (! $recruiter) {
            return response()->json([
                'success' => false,
                'message' => 'Recruiter not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $recruiter,
        ]);
    }

    /* ================= UPDATE ================= */

    public function update(Request $request, $id)
    {
        $recruiter = User::where('role', 'recruiter')
            ->where('id', $id)
            ->first();

        if (! $recruiter) {
            return response()->json([
                'success' => false,
                'message' => 'Recruiter not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $id,
            'phone'  => 'required|digits:10|unique:users,phone,' . $id,
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $recruiter->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'phone'  => $request->phone,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recruiter updated successfully',
        ]);
    }

    /* ================= DELETE ================= */

    public function destroy($id)
    {
        $recruiter = User::where('role', 'recruiter')
            ->where('id', $id)
            ->first();

        if (! $recruiter) {
            return response()->json([
                'success' => false,
                'message' => 'Recruiter not found',
            ], 404);
        }

        $recruiter->delete(); // soft delete

        return response()->json([
            'success' => true,
            'message' => 'Recruiter deleted successfully (soft deleted)',
        ]);
    }

    // /* ================= LOGIN ================= */

    public function deletedRecruiters()
    {
        $recruiters = User::onlyTrashed()
            ->where('role', 'recruiter')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $recruiters,
        ]);
    }

    public function restore($id)
    {
        $recruiter = User::onlyTrashed()
            ->where('role', 'recruiter')
            ->where('id', $id)
            ->first();

        if (! $recruiter) {
            return response()->json([
                'success' => false,
                'message' => 'Recruiter not found in trash',
            ], 404);
        }

        $recruiter->restore();

        return response()->json([
            'success' => true,
            'message' => 'Recruiter restored successfully',
        ]);
    }
}