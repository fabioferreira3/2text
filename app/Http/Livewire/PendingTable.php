<?php

namespace App\Http\Livewire;

use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Enums\Tone;
use App\Helpers\TextRequestHelper;
use App\Models\TextRequest;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button, Column, Footer, Header, PowerGrid, PowerGridComponent, PowerGridEloquent};

final class PendingTable extends PowerGridComponent
{
    use ActionButton;

    /*
    |--------------------------------------------------------------------------
    |  Features Setup
    |--------------------------------------------------------------------------
    | Setup Table's general features
    |
    */
    public function setUp(): array
    {
        //$this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |  Datasource
    |--------------------------------------------------------------------------
    | Provides data to your Table using a Model or Collection
    |
    */

    /**
     * PowerGrid datasource.
     *
     * @return Builder<\App\Models\TextRequest>
     */
    public function datasource(): Builder
    {
        return TextRequest::query()->whereIn('status', ['pending', 'processing'])->latest();
    }

    /*
    |--------------------------------------------------------------------------
    |  Relationship Search
    |--------------------------------------------------------------------------
    | Configure here relationships to be used by the Search and Table Filters.
    |
    */

    /**
     * Relationship search.
     *
     * @return array<string, array<int, string>>
     */
    public function relationSearch(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    |  Add Column
    |--------------------------------------------------------------------------
    | Make Datasource fields available to be used as columns.
    | You can pass a closure to transform/modify the data.
    |
    | ❗ IMPORTANT: When using closures, you must escape any value coming from
    |    the database using the `e()` Laravel Helper function.
    |
    */
    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('source_provider', fn (TextRequest $model) => TextRequestHelper::parseSource($model->source_provider))
            ->addColumn('keyword')
            ->addColumn('language', fn (TextRequest $model) => TextRequestHelper::parseLanguage($model->language))
            ->addColumn('tone', fn (TextRequest $model) => Str::of($model->tone)->ucfirst())
            ->addColumn('progress', fn (TextRequest $model) => $model->progress . '%')
            ->addColumn('created_at_formatted', fn (TextRequest $model) => Carbon::parse($model->created_at)->format('d/m/Y - H:i:s'));
    }

    /*
    |--------------------------------------------------------------------------
    |  Include Columns
    |--------------------------------------------------------------------------
    | Include the columns added columns, making them visible on the Table.
    | Each column can be configured with properties, filters, actions...
    |
    */

    /**
     * PowerGrid Columns.
     *
     * @return array<int, Column>
     */
    public function columns(): array
    {
        return [
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->makeInputDatePicker()
                ->searchable(),
            Column::make('Progress', 'progress')
                ->sortable(),
            Column::make('Source', 'source_provider')
                ->makeInputEnumSelect(SourceProvider::cases(), 'source_provider')
                ->sortable(),
            Column::make('Language', 'language')
                ->makeInputEnumSelect(Language::cases(), 'language')
                ->sortable(),
            Column::make('Keyword', 'keyword')
                ->makeInputText()
                ->sortable(),
            Column::make('Tone', 'tone')
                ->makeInputEnumSelect(Tone::cases(), 'tone')
                ->sortable(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Actions Method
    |--------------------------------------------------------------------------
    | Enable the method below only if the Routes below are defined in your app.
    |
    */

    /**
     * PowerGrid TextRequest Action Buttons.
     *
     * @return array<int, Button>
     */

    /*
    public function actions(): array
    {
       return [
           Button::make('edit', 'Edit')
               ->class('bg-indigo-500 cursor-pointer text-white px-3 py-2.5 m-1 rounded text-sm')
               ->route('text-request.edit', ['text-request' => 'id']),

           Button::make('destroy', 'Delete')
               ->class('bg-red-500 cursor-pointer text-white px-3 py-2 m-1 rounded text-sm')
               ->route('text-request.destroy', ['text-request' => 'id'])
               ->method('delete')
        ];
    }
    */

    /*
    |--------------------------------------------------------------------------
    | Actions Rules
    |--------------------------------------------------------------------------
    | Enable the method below to configure Rules for your Table and Action Buttons.
    |
    */

    /**
     * PowerGrid TextRequest Action Rules.
     *
     * @return array<int, RuleActions>
     */

    /*
    public function actionRules(): array
    {
       return [

           //Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($text-request) => $text-request->id === 1)
                ->hide(),
        ];
    }
    */
}
