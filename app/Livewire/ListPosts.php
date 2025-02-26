<?php

namespace App\Livewire;

use App\Models\Post;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component;

class ListPosts extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    const string NAME = 'list-posts';

    public function getEloquentQuery(): Builder
    {
        return Post::query()
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
            ->headerActions(self::headerActions())
            ->actions(self::actions())
            ->columns(self::columns());
    }

    /**
     * Returns an array of header actions.
     *
     * Each action in the array represents a header action for the application.
     * The actions may include creating new Medicals, editing existing Medicals, etc.
     *
     * @return array The array of header actions, each defined as a `CreateAction` object.
     *
     * @see CreateAction
     */
    public static function headerActions(): array
    {
        return [
            CreateAction::make()
                ->color(Color::Slate)
                ->label('New Post')
                ->model(Post::class)
                ->form(self::formSchema())
                ->slideOver(),
        ];
    }

    /**
     * Returns an array of actions for the application.
     *
     * @return array The array of actions.
     */
    public static function actions(): array
    {
        return [
            ActionGroup::make([
                Action::make('showPost')
                    ->label('view')
                    ->action(fn ($record) => redirect(route('post.show', ['post' => $record]))),
                EditAction::make('Edit')
                    ->model(Post::class)
                    ->form(self::formSchema())
                    ->slideOver(),
                DeleteAction::make('Delete')
                    ->requiresConfirmation(false),
            ]),
        ];
    }

    /**
     * Returns an array of columns for the application.
     *
     * @return array The array of columns.
     */
    public static function columns(): array
    {
        return [
            TextColumn::make('title')
                ->searchable(isIndividual: true)
                ->sortable(),
        ];
    }

    public static function formSchema(): array
    {
        return [
            Section::make()
                ->schema([
                    TextInput::make('title')
                        ->prefixIcon('heroicon-o-identification')
                        ->maxLength(200)
                        ->required(),
                ]),
        ];
    }
}
