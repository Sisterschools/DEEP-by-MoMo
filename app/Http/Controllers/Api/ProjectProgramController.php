<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Program;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Project\AttachStudentsToProgramRequest;
use App\Http\Requests\Project\AttachProgramProjectRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Project\GetProgramsByStatusRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\ProgramCollection;

class ProjectProgramController extends Controller
{
    use AuthorizesRequests;

    public function attachStudentsToProgram(
        AttachStudentsToProgramRequest $request,
        Project $project,
        Program $program
    ): JsonResponse {

        $this->authorize('create', Project::class);

        // Ensure the program is attached to the project
        $studentIds = $request->validated()['student_ids'];

        // prepare the the student ids with the program ids
        $data = array_map(fn($id) => ['student_id' => $id, 'program_id' => $program->id], $studentIds);

        // Attach the students to the project
        $project->programStudents($program)->syncWithoutDetaching($data);

        return response()->json([
            'message' => 'Students attached to program successfully.',
            'attached_student_count' => count($studentIds)
        ], 200);
    }

    public function attach(AttachProgramProjectRequest $request, Project $project, Program $program): JsonResponse
    {
        $this->authorize('create', Project::class);

        $project->programs()->attach($program->id);
        return response()->json(['message' => 'Program attached to the project successfully.']);
    }

    public function detach(Project $project, Program $program): JsonResponse
    {
        $this->authorize('create', Project::class);

        $project->programs()->detach($program->id);
        return response()->json(['message' => 'Program detached from the project successfully.']);
    }

    public function markAsReady(Project $project, Program $program)
    {
        $project->programs()->updateExistingPivot($program->id, [
            'status' => 'ready',
            'ready_at' => now(),
        ]);

        return response()->json(['message' => 'Program marked as ready'], 200);
    }

    public function markAsArchived(Project $project, Program $program)
    {
        $project->programs()->updateExistingPivot($program->id, [
            'status' => 'archived',
            'archived_at' => now(),
        ]);

        return response()->json(['message' => 'Program marked as archived'], 200);
    }


    /**
     * Get programs by status for a specific project
     */
    public function getProgramsByStatus(GetProgramsByStatusRequest $request, Project $project)
    {
        // The status is already validated and available in the request
        $status = $request->input('status');

        // Fetch programs based on the status
        $programs = $project->programs()->wherePivot('status', $status)->paginate();

        return ProgramCollection::make($programs);
    }

}

