<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $projects = $request->user()
            ->projects()
            ->withCount('tasks')
            ->latest()
            ->paginate(12);

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        try {
            $project = DB::transaction(
                fn () => $request->user()->projects()->create($request->validated())
            );
        } catch (QueryException $exception) {
            Log::error('Project creation failed.', [
                'exception' => $exception,
                'user_id' => $request->user()->id,
            ]);

            return back()->withInput()->with('error', 'The project could not be created. Please try again.');
        }

        return to_route('projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function show(Request $request, Project $project): View
    {
        Gate::authorize('view', $project);
        Gate::authorize('viewAny', [Task::class, $project]);

        $requestedStatus = $request->query('status');
        $status = is_string($requestedStatus) && in_array($requestedStatus, Task::STATUSES, true)
            ? $requestedStatus
            : null;

        $tasks = $project->tasks()
            ->when($status, fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('projects.show', compact('project', 'tasks', 'status'));
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        try {
            DB::transaction(fn () => $project->update($request->validated()));
        } catch (QueryException $exception) {
            Log::error('Project update failed.', [
                'exception' => $exception,
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
            ]);

            return back()->withInput()->with('error', 'The project could not be updated. Please try again.');
        }

        return to_route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        try {
            DB::transaction(fn () => $project->delete());
        } catch (QueryException $exception) {
            Log::error('Project deletion failed.', [
                'exception' => $exception,
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
            ]);

            return back()->with('error', 'The project could not be deleted. Please try again.');
        }

        return to_route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
