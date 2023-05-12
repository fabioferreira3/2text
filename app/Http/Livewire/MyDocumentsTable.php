<?php

namespace App\Http\Livewire;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use WireUi\Traits\Actions;

class MyDocumentsTable extends DataTableComponent
{
    use Actions;

    protected $model = Document::class;
    protected $repo;

    public function configure(): void
    {
        $this->repo = new DocumentRepository();
        $this->setPrimaryKey('id');
        $this->setRefreshTime(8000);
    }

    public function viewDoc($documentId)
    {
        return redirect()->route('document-view', ['document' => $documentId]);
    }

    public function deleteDoc($documentId)
    {
        try {
            $this->repo->delete($documentId);
            $this->notification(['icon' => 'success', 'iconColor' => 'text-green-400', 'timeout' => 5000, 'title' => 'Document moved to the trash can']);
        } catch (Exception) {
            $this->notification(['icon' => 'error', 'iconColor' => 'text-red-700', 'timeout' => 5000, 'title' => 'There was an error while deleting this document']);
        }
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
                    fn ($row) => $row->is_completed ? 'Finished' : 'In Progress'
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
            Column::make('Actions')
                ->label(
                    fn ($row, Column $column) => view('livewire.tables.my-documents.view-action', ['rowId' => $row->id])
                ),
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
                        'ar' => 'Arabic',
                        'ch' => 'Chinese',
                        'de' => 'German',
                        'fr' => 'French',
                        'it' => 'Italian',
                        'pl' => 'Polnish',
                        'pt' => 'Portuguese',
                        'es' => 'Spanish',
                        'tr' => 'Turkish',
                        'el' => 'Greek',
                        'ja' => 'Japanese',
                        'ko' => 'Korean',
                    ]
                )->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('language', $value);
                }),
        ];
    }
}
