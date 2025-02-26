<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component;

class ListPinnedComments extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    const string NAME = 'list-pinned-comments';
    public ?Post $post;
    public int $renderedCount = 0;

    protected $listeners = [
        'post-comment-added' => '$refresh',
    ];

    public function rendered()
    {
        if (empty($this->post)) {
            return;
        }

        $this->renderedCount++;
        // Page load has 2 renders before its actually ready, dispatching the event any other time won't have it work
        if ($this->renderedCount === 2) {
            $this->dispatch('trigger-select-records-' . $this->id(), [
                'records' => $this->post->pinnedComments->pluck('comment_id'),
                'livewireId' => $this->id(),
            ]);
        }
    }

    public function mount($post)
    {
        $this->post = $post;
    }

    public function getEloquentQuery(): Builder
    {
        return ! empty($this->post)
            ? Comment::query()
                ->join('comments_posts', 'comments.id', '=', 'comments_posts.comment_id')
                ->where('comments_posts.post_id', '=', $this->post->id)
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ])
            : Comment::query()
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]);
    }

    /**
     * Generates and configures a table object.
     *
     * @param  Table  $table  The table object to configure.
     * @return Table The configured table object.
     *
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getEloquentQuery())
            ->bulkActions(self::bulkActions())
            ->columns(self::columns());
    }

    /**
     * Returns an array of columns for the application.
     *
     * @return array The array of columns.
     */
    public static function columns(): array
    {
        return [
            TextColumn::make('comment'),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            BulkAction::make('pinComments')
                ->action(function (array $data, Component $livewire) {
                    $livewire->dispatch('post.comments-selected', ['selectedComments' => $livewire->selectedTableRecords]);
                    //                    $livewire->dispatch('appointment.show.refresh');
                }),
        ];
    }
}
