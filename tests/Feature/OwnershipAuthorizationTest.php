<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('another user receives forbidden for every existing project detail action', function (string $method, string $routeName, array $data = []) {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $response = $this->actingAs($otherUser)->call(
        $method,
        route($routeName, $project),
        $data,
    );

    $response->assertForbidden();
    $this->assertModelExists($project);
})->with([
    'view' => ['GET', 'projects.show'],
    'edit' => ['GET', 'projects.edit'],
    'update' => ['PUT', 'projects.update', [
        'title' => 'Unauthorized update',
        'description' => 'This must not be saved.',
    ]],
    'delete' => ['DELETE', 'projects.destroy'],
]);

test('another user cannot create a task in a project they do not own', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $this->actingAs($otherUser)
        ->post(route('projects.tasks.store', $project), [
            'title' => 'Unauthorized task',
            'description' => null,
            'status' => 'todo',
        ])
        ->assertForbidden();

    expect(Task::query()->count())->toBe(0);
});

test('another user receives forbidden for every existing task detail action', function (string $method, string $routeName, array $data = []) {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->for($owner)->create();
    $task = Task::factory()->for($project)->todo()->create();

    $response = $this->actingAs($otherUser)->call(
        $method,
        route($routeName, [$project, $task]),
        $data,
    );

    $response->assertForbidden();
    $this->assertModelExists($task);
})->with([
    'view' => ['GET', 'projects.tasks.show'],
    'edit' => ['GET', 'projects.tasks.edit'],
    'update' => ['PUT', 'projects.tasks.update', [
        'title' => 'Unauthorized update',
        'description' => null,
        'status' => 'done',
    ]],
    'delete' => ['DELETE', 'projects.tasks.destroy'],
]);
