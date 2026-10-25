<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

it('belongs to its user and is included in the user projects relationship', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    expect($project->user->is($user))->toBeTrue();
    expect($user->projects->sole()->is($project))->toBeTrue();
});

it('creates a project from the Filament resource', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(CreateProject::class)
        ->fillForm([
            'user_id' => $user->getKey(),
            'name' => 'Riverfront Offices',
            'description' => 'Office renovation project.',
            'building_type' => 'Commercial',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Project::class, [
        'user_id' => $user->getKey(),
        'name' => 'Riverfront Offices',
        'description' => 'Office renovation project.',
        'building_type' => 'Commercial',
    ]);
});
