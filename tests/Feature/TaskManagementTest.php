<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('users can list tasks through the nested task index', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $task = Task::factory()->for($project)->create();

    $this->actingAs($user)
        ->get(route('projects.tasks.index', $project))
        ->assertOk()
        ->assertSee($task->title);
});

test('users can create and view a task inside their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    $response = $this->actingAs($user)->post(route('projects.tasks.store', $project), [
        'title' => 'Write acceptance tests',
        'description' => 'Cover the task workflow.',
        'status' => 'in_progress',
    ]);

    $task = Task::query()->sole();

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('projects.tasks.show', [$project, $task]))
        ->assertSessionHas('success', 'Task created successfully.');

    expect($task->project->is($project))->toBeTrue();

    $this->actingAs($user)
        ->get(route('projects.tasks.show', [$project, $task]))
        ->assertOk()
        ->assertSee('Write acceptance tests')
        ->assertSee('In Progress');
});

test('users can edit and update a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $task = Task::factory()->for($project)->todo()->create();

    $this->actingAs($user)
        ->get(route('projects.tasks.edit', [$project, $task]))
        ->assertOk()
        ->assertSee($task->title);

    $this->actingAs($user)
        ->put(route('projects.tasks.update', [$project, $task]), [
            'title' => 'Updated task',
            'description' => 'Updated description.',
            'status' => 'in_progress',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('projects.tasks.show', [$project, $task]))
        ->assertSessionHas('success', 'Task updated successfully.');

    expect($task->refresh())
        ->title->toBe('Updated task')
        ->status->toBe('in_progress');
});

test('users can delete a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $task = Task::factory()->for($project)->create();

    $this->actingAs($user)
        ->delete(route('projects.tasks.destroy', [$project, $task]))
        ->assertRedirect(route('projects.show', $project))
        ->assertSessionHas('success', 'Task deleted successfully.');

    $this->assertModelMissing($task);
    $this->assertModelExists($project);
});

test('tasks can be filtered by every allowed status', function (string $status) {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $matchingTask = Task::factory()->for($project)->create([
        'title' => "Matching {$status}",
        'status' => $status,
    ]);
    $otherTask = Task::factory()->for($project)->create([
        'title' => "Other {$status}",
        'status' => $status === 'todo' ? 'done' : 'todo',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', ['project' => $project, 'status' => $status]))
        ->assertOk()
        ->assertSee($matchingTask->title)
        ->assertDontSee($otherTask->title);
})->with(['todo', 'in_progress', 'done']);

test('the all filter displays tasks of every status', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $tasks = collect(Task::STATUSES)->map(
        fn (string $status) => Task::factory()->for($project)->create(['status' => $status])
    );

    $response = $this->actingAs($user)->get(route('projects.show', $project));

    $response->assertOk();
    $tasks->each(fn (Task $task) => $response->assertSee($task->title));
});

test('changing an existing task to done flashes a completion alert', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $task = Task::factory()->for($project)->inProgress()->create();

    $response = $this->actingAs($user)->put(route('projects.tasks.update', [$project, $task]), [
        'title' => $task->title,
        'description' => $task->description,
        'status' => 'done',
    ]);

    $response
        ->assertRedirect(route('projects.tasks.show', [$project, $task]))
        ->assertSessionHas('completed', 'Task completed.');

    $this->followingRedirects()
        ->actingAs($user)
        ->get(route('projects.tasks.show', [$project, $task]))
        ->assertSee('Task completed.');
});

test('saving an already completed task uses the normal update alert', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $task = Task::factory()->for($project)->done()->create();

    $this->actingAs($user)
        ->put(route('projects.tasks.update', [$project, $task]), [
            'title' => 'Still complete',
            'description' => $task->description,
            'status' => 'done',
        ])
        ->assertSessionMissing('completed')
        ->assertSessionHas('success', 'Task updated successfully.');
});

test('empty task submissions are rejected', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('projects.tasks.create', $project))
        ->post(route('projects.tasks.store', $project), [
            'title' => '',
            'description' => '',
            'status' => '',
        ])
        ->assertRedirect(route('projects.tasks.create', $project))
        ->assertSessionHasErrors(['title', 'status']);

    expect(Task::query()->count())->toBe(0);
});

test('invalid task fields are rejected', function (string $field, mixed $value) {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('projects.tasks.store', $project), [
            'title' => 'Valid title',
            'description' => 'Valid description',
            'status' => 'todo',
            $field => $value,
        ])
        ->assertSessionHasErrors($field);
})->with([
    'title is not a string' => ['title', ['invalid']],
    'title is too long' => ['title', str_repeat('a', 256)],
    'description is too long' => ['description', str_repeat('a', 2001)],
    'status is invalid' => ['status', 'blocked'],
]);

test('a task cannot be accessed through a different project route', function () {
    $user = User::factory()->create();
    $firstProject = Project::factory()->for($user)->create();
    $secondProject = Project::factory()->for($user)->create();
    $task = Task::factory()->for($firstProject)->create();

    $this->actingAs($user)
        ->get(route('projects.tasks.show', [$secondProject, $task]))
        ->assertNotFound();
});
