<?php

namespace App\Livewire;

use Filament\Forms\Components\Livewire;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class PostIndex extends Component implements HasForms
{
    use InteractsWithForms;

    public function form(Form $form): Form
    {
        return $form->schema(self::formSchema());
    }

    public static function formSchema(): array
    {
        return [
            Livewire::make(ListPosts::class, [])->key('PostsIndexListPosts'),
        ];
    }

    public function render()
    {
        return view('livewire.index-posts', []);
    }
}
