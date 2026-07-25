<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request, Project $project): View
    {
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

    public function create(Project $project): View
    {
        Gate::authorize('create', [Task::class, $project]);

        return view('tasks.create', compact('project'));
    }

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('create', [Task::class, $project]);

        try {
            $task = DB::transaction(
                fn () => $project->tasks()->create($request->validated())
            );
        } catch (QueryException $exception) {
            Log::error('Task creation failed.', [
                'exception' => $exception,
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
            ]);

            return back()->withInput()->with('error', 'The task could not be created. Please try again.');
        }

        return to_route('projects.tasks.show', [$project, $task])->with('success', 'Task created successfully.');
    }

    public function show(Project $project, Task $task): View
    {
        Gate::authorize('view', $task);

        return view('tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, Task $task): View
    {
        Gate::authorize('update', $task);

        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $wasCompleted = $task->status !== 'done' && $request->validated('status') === 'done';

        try {
            DB::transaction(fn () => $task->update($request->validated()));
        } catch (QueryException $exception) {
            Log::error('Task update failed.', [
                'exception' => $exception,
                'project_id' => $project->id,
                'task_id' => $task->id,
                'user_id' => $request->user()->id,
            ]);

            return back()->withInput()->with('error', 'The task could not be updated. Please try again.');
        }

        $redirect = to_route('projects.tasks.show', [$project, $task]);

        return $wasCompleted
            ? $redirect->with('completed', 'Task completed.')
            : $redirect->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Project $project, Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        try {
            DB::transaction(fn () => $task->delete());
        } catch (QueryException $exception) {
            Log::error('Task deletion failed.', [
                'exception' => $exception,
                'project_id' => $project->id,
                'task_id' => $task->id,
                'user_id' => $request->user()->id,
            ]);

            return back()->with('error', 'The task could not be deleted. Please try again.');
        }

        return to_route('projects.show', $project)->with('success', 'Task deleted successfully.');
    }
}
