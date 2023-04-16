<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\TextRequest;
use Illuminate\Database\Eloquent\Builder;

class PendingTable extends DataTableComponent
{
    protected $model = TextRequest::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return TextRequest::query()->whereNotIn('status', ['finished', 'aborted'])->latest();
    }

    public function columns(): array
    {
        return [
            Column::make("Source", "source_provider")
                ->sortable(),
            Column::make("Progress", "progress")
                ->format(fn ($value, $row, Column $column) => $row->progress . "%")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'pending') {
                        $builder->where('status', 'pending');
                    } elseif ($value === 'processing') {
                        $builder->where('status', 'processing');
                    }
                }),
        ];
    }
}
