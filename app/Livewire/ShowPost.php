<?php

namespace App\Livewire;

use App\Models\Post;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class ShowPost extends Component implements HasForms
{
    use InteractsWithForms;
    const string NAME = 'show-post';

    public Post $post;
    public ?array $data;

    public function mount(Post $post)
    {
        $this->post = $post;

        $this->form->fill($post->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->post)
            ->statePath('data')
            ->schema(self::formSchema());
    }

    public static function formSchema(): array
    {
        return [
            Tabs::make()
            ->schema([
                Tab::make('Summary')
                ->schema([
                    TextInput::make('title'),
                    Livewire::make(ListComments::class, fn ($record) => ['post' => $record])
                        ->key('ShowPostSummaryTabListCommentsComponent')
                ]),
                Tab::make('Comments')
                    ->schema([
                        Livewire::make(ListComments::class, fn ($record) => ['post' => $record])
                            ->key('ShowPostCommentsTabListCommentsComponent')
                    ]),
            ])
        ];
    }
}
