<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Http\Requests\School\StoreSchoolRequest;
use App\Events\UserRegisteredEvent;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\School\SearchSchoolRequest;
use App\Http\Resources\SchoolResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SchoolController extends Controller
{
    use AuthorizesRequests; // Add this line to import the trait
    public function index()
    {
        $this->authorize('viewAny', School::class);
        $schools = School::paginate(10);
        return SchoolResource::collection($schools);
    }

    public function store(StoreSchoolRequest $request)
    {
        $this->authorize('create', School::class);

       

        DB::beginTransaction();

        try {

            // Create the user first
            $userData = $request->only(['name', 'email', 'password', 'role']);
            $userData['role'] = 'student';
            $user = User::create($userData);

            
            // Create the school
            $schoolData = $request->only(['title', 'photo', 'address', 'description',
            'phone_number', 'website', 'founding_year', 'student_capacity']);
            $school = Student::create($schoolData);


            $school->user()->save($user);
            event(new UserRegisteredEvent($user, $userData['password']));

            DB::commit();

            return SchoolResource::make($school);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error creating school and user',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function show(School $school)
    {
        $this->authorize('view', $school);
        return SchoolResource::make($school);
    }

    public function update(UpdateSchoolRequest $request, School $school)
    {
        $this->authorize('update', $school);
        $school->update($request->validated());
        return response()->json([
            'message' => 'School updated successfully.',
            'data' => SchoolResource::make($school)
        ]);
    }

    public function destroy(School $school)
    {
        $this->authorize('delete', $school);
        $school->delete();
        return response()->json(null, 204);
    }

    public function search(SearchSchoolRequest $request)
    {
        $term = $request->input('search');
        $schools = School::search($term)->get();

        return SchoolResource::collection($schools);
    }
}