<?php

namespace App\Livewire;

use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class ShowPost extends Component implements HasForms
{
    use InteractsWithForms;
    const string NAME = 'show-post';

    public Post $post;
    public ?array $data;
    public ?array $editData;

    protected $listeners = [
        'post.updated' => 'refreshPost',
    ];

    public function mount(Post $post)
    {
        $this->post = $post;

        $this->actionForm->fill($post->toArray());
        $this->editPostsForm->fill($post->toArray());
    }

    public function actionForm(Form $form): Form
    {
        return $form
            ->model($this->post)
            ->statePath('data')
            ->schema(self::formSchema());
    }

    public function editPostsForm(Form $form): Form
    {
        return $form
            ->model($this->post)
            ->statePath('editData')
            ->schema(self::editFormSchema());
    }

    public function getForms(): array
    {
        return [
            'actionForm',
            'editPostsForm',
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
        $this->post->update($this->editPostsForm->getState());

        Notification::make()
            ->title('Post edited')
            ->success()
            ->send();
        $this->dispatch('close-modal', id: 'edit-post');
        $this->dispatch('post.updated');
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
                                ->key('ShowPostSummaryTabListCommentsComponent'),
                            Actions::make([
                                Actions\Action::make('editPost')
                                    ->action(function ($livewire) {
                                        $livewire->dispatch('open-modal', id: 'edit-post');
                                    }),
                            ]),
                        ]),
                    Tab::make('Comments')
                        ->schema([
                            Livewire::make(ListComments::class, fn ($record) => ['post' => $record])
                                ->key('ShowPostCommentsTabListCommentsComponent'),
                        ]),
                ]),
        ];
    }

    public static function editFormSchema(): array
    {
        return [
            TextInput::make('title'),
            Livewire::make(ListComments::class, fn ($record) => ['post' => $record])
                ->key('EditPostCommentsComponent'),
        ];
    }

    public function refreshPost()
    {
        $this->actionForm->fill($this->post->toArray());
        $this->editPostsForm->fill($this->post->toArray());
    }
}
