<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('project routes require authentication', function () {
    $this->get(route('projects.index'))->assertRedirect(route('login'));
    $this->get(route('projects.create'))->assertRedirect(route('login'));
});

test('authenticated users are redirected from the dashboard to projects', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('projects.index'));
});

test('users only see their own projects in the project list', function () {
    $user = User::factory()->create();
    $ownProject = Project::factory()->for($user)->create();
    $otherProject = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.index'))
        ->assertOk()
        ->assertSee($ownProject->title)
        ->assertDontSee($otherProject->title);
});

test('users can create and view a project', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'title' => 'Website redesign',
        'description' => 'Refresh the public website.',
    ]);

    $project = Project::query()->sole();

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('projects.show', $project))
        ->assertSessionHas('success', 'Project created successfully.');

    expect($project->user->is($user))->toBeTrue();
    expect($project->title)->toBe('Website redesign');

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('Website redesign')
        ->assertSee('Refresh the public website.');
});

test('users can edit and update their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('projects.edit', $project))
        ->assertOk()
        ->assertSee($project->title);

    $this->actingAs($user)
        ->put(route('projects.update', $project), [
            'title' => 'Updated project',
            'description' => 'Updated description.',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('projects.show', $project))
        ->assertSessionHas('success', 'Project updated successfully.');

    expect($project->refresh())
        ->title->toBe('Updated project')
        ->description->toBe('Updated description.');
});

test('users can delete a project and its tasks are deleted by cascade', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $task = Task::factory()->for($project)->create();

    $this->actingAs($user)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'))
        ->assertSessionHas('success', 'Project deleted successfully.');

    $this->assertModelMissing($project);
    $this->assertModelMissing($task);
});

test('empty project submissions are rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('projects.create'))
        ->post(route('projects.store'), [
            'title' => '',
            'description' => '',
        ])
        ->assertRedirect(route('projects.create'))
        ->assertSessionHasErrors('title');

    expect(Project::query()->count())->toBe(0);
});

test('invalid project fields are rejected', function (string $field, mixed $value) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('projects.store'), [
            'title' => 'Valid title',
            'description' => 'Valid description',
            $field => $value,
        ])
        ->assertSessionHasErrors($field);
})->with([
    'title is not a string' => ['title', ['invalid']],
    'title is too long' => ['title', str_repeat('a', 256)],
    'description is too long' => ['description', str_repeat('a', 2001)],
]);
