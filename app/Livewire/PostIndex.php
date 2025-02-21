<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
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
            Livewire::make(ListPosts::class, []),
        ];
    }


    public function render()
    {
        return view('livewire.index-posts', []);
    }
}
