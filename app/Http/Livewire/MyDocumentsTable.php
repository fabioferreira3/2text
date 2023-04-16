<?php

namespace App\Http\Livewire;

use App\Models\Document;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\TextRequest;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class MyDocumentsTable extends DataTableComponent
{
    protected $model = TextRequest::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setRefreshTime(8000);
    }

    public function builder(): Builder
    {
        return Document::query()->latest();
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->format(fn ($value, $row) => $row->id)
                ->hideIf(true),
            Column::make("Type", "type")
                ->format(fn ($value, $row) => $row->type->label())
                ->searchable()
                ->sortable(),
            Column::make('Status')
                ->label(
                    fn ($row) => $row->is_completed ? 'Ready' : 'In Progress'
                ),
            Column::make("Language", "language")
                ->format(fn ($value, $row) => $row->language->label())
                ->searchable()
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Created at", "created_at")
                ->format(fn ($value, $row) => $row->created_at->setTimezone(session('user_timezone') ?? 'America/New_York')->format('m/d/Y - h:ia'))
                ->sortable()
                ->collapseOnMobile(),
            LinkColumn::make('Action')
                ->title(fn () => 'View')
                ->location(fn ($row) => route('document-view', ['document' => $row->id]))
                ->attributes(fn () => ['class' => 'bg-zinc-200 px-3 py-2 rounded-lg']),
        ];
    }

    public function filters(): array
    {
        return [
            MultiSelectFilter::make('Type')
                ->options([
                    'blog_post' => 'Blog Post'
                ])
                ->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('type', $value);
                }),
            MultiSelectFilter::make('Languages')
                ->options(
                    [
                        'en' => 'English',
                        'pt' => 'Portuguese'
                    ]
                )->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('language', $value);
                }),
        ];
    }
}
