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

    public function editPostsForm(Form $form)
    {
        return $form->schema(self::formSchema());
    }

    public function actionForm(Form $form)
    {
        return $form->schema(self::actionFormSchema());
    }


    public function getForms(): array
    {
        return [
            'actionForm',
            'editPostsForm',
        ];
    }

    public static function formSchema(): array
    {
        return [
            Livewire::make(ListPosts::class, []),
        ];
    }

    public static function actionFormSchema(): array
    {
        return [
            Tabs::make()
            ->schema([
                Tab::make('Summary')
                    ->schema([
                        Section::make('Summary')
                        ->schema([
                            TextInput::make('title')
                        ]),
                        Section::make()
                            ->schema([
                                Actions::make([
                                    Actions\Action::make('editPosts')
                                        ->label('Edit Posts')
                                        ->button()
                                        ->action(fn($livewire) => $livewire->dispatch('open-modal', id: 'edit-posts'))
                                ])
                            ])
                    ]),
                Tab::make('Comments')
                ->schema([
                    Livewire::make(ListComments::class, fn ($record) => ['post' => $record])
                ]),
            ]),
        ];
    }


    public function getModalFooterActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->button()
                ->color('primary'),
            StaticAction::make('cancel')
                ->label('Cancel')
                ->button()
                ->close()
                ->color('gray'),
        ];
    }

    public function mountAction(string $name, array $arguments = [])
    {
        $this->dispatch('close-modal', id: 'edit-posts');

    }

    public function render()
    {
        return view('livewire.index-posts', []);
    }
}
