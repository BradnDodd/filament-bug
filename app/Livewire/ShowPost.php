<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PostComment;
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
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ShowPost extends Component implements HasForms
{
    use InteractsWithForms;
    const string NAME = 'show-post';

    public Post $post;
    public ?array $data;
    public ?array $editData;
    public array $selectedComments;

    protected $listeners = [
        'post.updated' => 'refreshPost',
        'post.comments-selected' => 'commentsSelected',
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
            ->schema(self::editFormSchema($this->post));
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

        $newComments = collect([]);

        foreach ($this->selectedComments as $comment) {
            $commentObj = new PostComment([
                'comment_id' => $comment,
                'post_id' => $this->post->id,
                'pinned' => true,
            ]);
            $newComments->push($commentObj);
        }

        $this->post->pinnedComments()->delete();
        $this->post->pinnedComments()->saveMany($newComments);

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

    public static function editFormSchema(Post $post): array
    {

        return [
            TextInput::make('title'),
            Section::make(
                new HtmlString(
                    Blade::render(
                        <<<'BLADE'
                        <div class="flex items-centre gap-x-2">
                        <span>Pinned Comments</span>
                        <x-filament::badge color="success" size="xs">
                                {{ $numComments }}
                        </x-filament::badge>
                        </div>
                        BLADE, ['numComments' => $post->pinnedComments()->count() ?: 0]
                    )
                )
            )
                ->schema([
                    Livewire::make(ListPinnedComments::class, fn ($record) => ['post' => $record])
                        ->key('PinnedCommentsComponent'),
                ])
                ->collapsible(true)
                ->collapsed(true),
        ];
    }

    public function refreshPost()
    {
        $this->actionForm->fill($this->post->toArray());
        $this->editPostsForm->fill($this->post->toArray());
    }

    public function commentsSelected(array $commentsSelected)
    {
        $this->selectedComments = $commentsSelected['selectedComments'];
    }
}
