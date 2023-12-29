<?php

namespace App\Http\Livewire;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use WireUi\Traits\Actions;

class MyDocumentsTable extends DataTableComponent
{
    use Actions;

    protected $model = Document::class;
    protected $repo;
    public $documentTypes;

    public function mount($documentTypes = [])
    {
        if (!count($documentTypes)) {
            $this->documentTypes = [
                DocumentType::AUDIO_TRANSCRIPTION,
                DocumentType::BLOG_POST,
                DocumentType::INQUIRY,
                DocumentType::PARAPHRASED_TEXT,
                DocumentType::SOCIAL_MEDIA_GROUP,
                DOcumentType::SUMMARIZER,
                DocumentType::TEXT_TO_SPEECH,
            ];
        }
    }

    public function configure(): void
    {
        $this->repo = new DocumentRepository();
        $this->setPrimaryKey('id');
        $this->setRefreshTime(10000);
    }

    public function viewDoc($documentId)
    {
        $document = Document::findOrFail($documentId);
        if (in_array(
            $document->status,
            [DocumentStatus::FINISHED, DocumentStatus::DRAFT, DocumentStatus::IN_PROGRESS]
        )) {
            return redirect()->route('document-view', ['document' => $document]);
        }
    }

    public function deleteDoc($documentId)
    {
        try {
            $this->repo->delete($documentId);
            $this->notification([
                'icon' => 'success',
                'iconColor' => 'text-green-400',
                'timeout' => 5000,
                'title' => 'Document moved to the trash can!'
            ]);
        } catch (Exception) {
            $this->notification([
                'icon' => 'error',
                'iconColor' => 'text-red-700',
                'timeout' => 5000,
                'title' => 'There was an error while deleting this document'
            ]);
        }
    }

    public function builder(): Builder
    {
        return Document::whereIn('type', $this->documentTypes)->latest();
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
                ->format(function ($value) {
                    return Str::limit($value, 30, '...');
                })
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhereRaw("LOWER(title) LIKE ? ", ['%' . strtolower($searchTerm) . '%']);
                })
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('dashboard.status'))
                ->label(function ($row) {
                    return view('livewire.tables.my-documents.document-status', ['status' => $row->status]);
                }),

            Column::make(__('dashboard.created_at'), "created_at")
                ->format(fn ($value, $row) => $row->created_at->format('m/d/Y - h:ia'))
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('dashboard.actions'))
                ->label(
                    function ($row, Column $column) {
                        return view('livewire.tables.my-documents.view-action', [
                            'rowId' => $row->id,
                            'status' => $row->status,
                            'canView' => in_array($row->status->value, ['finished', 'draft', 'in_progress']),
                            'canDelete' => in_array($row->status->value, ['finished', 'aborted', 'failed', 'draft'])
                        ]);
                    }
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
