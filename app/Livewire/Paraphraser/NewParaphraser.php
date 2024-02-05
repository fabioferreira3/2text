<?php

namespace App\Livewire\Paraphraser;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Exceptions\InsufficientUnitsException;
use App\Jobs\Paraphraser\CreateFromWebsite;
use App\Repositories\DocumentRepository;
use App\Traits\UnitCheck;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewParaphraser extends Component
{
    use UnitCheck;

    public $document;
    public $sourceType = SourceProvider::FREE_TEXT->value;
    public $sourceUrl = null;
    public $tone = null;
    public $displaySourceUrl = null;
    public $language = Language::ENGLISH->value;
    public $isProcessing;

    public function rules()
    {
        return [
            'sourceType' => ['required', Rule::in([
                SourceProvider::WEBSITE_URL->value,
                SourceProvider::FREE_TEXT->value
            ])],
            'sourceUrl' => 'nullable|url|required_if:sourceType,website_url,youtube',
        ];
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.WebsiteCrawled" => 'ready',
        ];
    }

    public function mount()
    {
        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.paraphraser.new')->title(__('paraphraser.paraphraser'));
    }

    public function ready()
    {
        $this->redirectToDocument();
    }

    public function start()
    {
        $this->validate();
        try {
            $document = DocumentRepository::createGeneric([
                'type' => DocumentType::PARAPHRASED_TEXT->value,
                'source' => $this->sourceType,
                'language' => $this->language,
                'meta' => [
                    'tone' => $this->tone,
                    'source_url' => $this->sourceUrl
                ]
            ]);
            $this->document = $document;

            if ($this->sourceType === SourceProvider::FREE_TEXT->value) {
                $this->redirectToDocument();
            } else {
                $this->validateUnitCosts();
                $this->dispatch(
                    'alert',
                    type: 'info',
                    message: __('alerts.working_request')
                );
                $this->isProcessing = true;
                $processId = Str::uuid();
                CreateFromWebsite::dispatchIf($this->sourceType === SourceProvider::WEBSITE_URL->value, $document, [
                    'process_id' => $processId
                ]);
            }
        } catch (InsufficientUnitsException $e) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: __('alerts.insufficient_units')
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function validateUnitCosts()
    {
        $this->estimateWordsGenerationCost(480);
        $this->authorizeTotalCost();
    }

    public function redirectToDocument()
    {
        redirect()->route('paraphrase-view', ['document' => $this->document]);
    }

    public function setTone($tone)
    {
        $this->tone = $tone;
    }

    public function updated()
    {
        $this->displaySourceUrl = in_array($this->sourceType, [
            SourceProvider::WEBSITE_URL->value
        ]);
    }
}
