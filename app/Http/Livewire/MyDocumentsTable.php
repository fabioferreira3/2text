<?php

namespace App\Http\Livewire;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use WireUi\Traits\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
        $document = Document::findOrFail($documentId);
        return redirect()->route('document-view', ['document' => $document]);
    }

    public function deleteDoc($documentId)
    {
        try {
            $this->repo->delete($documentId);
            $this->notification(['icon' => 'success', 'iconColor' => 'text-green-400', 'timeout' => 5000, 'title' => 'Document moved to the trash can!']);
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
            Column::make("Content", "content")
                ->hideIf(true),
            Column::make(__('dashboard.type'), "type")
                ->format(function ($value, $row) {
                    return view('livewire.tables.my-documents.document-type', ['type' => $row->type]);
                })
                ->searchable()
                ->sortable(),
            Column::make(__('dashboard.title'), "title")
                ->format(function ($value, $row) {
                    return Str::limit($value ?? $row['content'], 30, '...');
                })
                ->searchable()
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('dashboard.status'))
                ->label(function ($row) {
                    return view('livewire.tables.my-documents.document-status', ['status' => $row->status]);
                }),

            Column::make(__('dashboard.created_at'), "created_at")
                ->format(fn ($value, $row) => $row->created_at->setTimezone(session('user_timezone') ?? 'America/New_York')->format('m/d/Y - h:ia'))
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('dashboard.actions'))
                ->label(
                    fn ($row, Column $column) => view('livewire.tables.my-documents.view-action', [
                        'rowId' => $row->id,
                        'status' => $row->status,
                        'canView' => in_array($row->status->value, ['finished']),
                        'canDelete' => in_array($row->status->value, ['finished', 'aborted', 'failed'])
                    ])
                ),
        ];
    }

    public function filters(): array
    {
        return [
            MultiSelectFilter::make(__('dashboard.type'))
                ->options(DocumentType::getKeyValues())
                ->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('type', $value);
                }),
            MultiSelectFilter::make(__('dashboard.language'))
                ->options(Language::getKeyValues())->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('language', $value);
                }),
        ];
    }
}
