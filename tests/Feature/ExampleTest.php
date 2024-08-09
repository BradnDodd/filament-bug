<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

it('bug', function () {
    $model = \App\Models\Post::factory()->create();

    // Act: Simulate the ViewAction to view the Audiometry model
    $livewire = livewire(\App\Livewire\ListPosts::class)
        ->callTableAction(\Filament\Tables\Actions\DeleteAction::class, $model);

    $this->assertModelMissing($model);
});
