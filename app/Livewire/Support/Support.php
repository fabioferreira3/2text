<?php

namespace App\Livewire\Support;

use App\Models\Comm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Support extends Component
{
    public string $reason;
    public array $tool = [];
    public string $message;
    public bool $messageSent = false;
    public $reasons = ['Help', 'Feedback', 'Bug', 'Billing', 'Suggestion', 'Other'];
    public $tools = [
        'Social Media',
        'Blog Post',
        'AI Image',
        'Paraphraser',
        'Text to Audio',
        'Transcription',
        'Summarizer',
        'Insight Hub'
    ];

    public function rules()
    {
        return [
            'reason' => ['required', 'string', Rule::in($this->reasons)],
            'tool' => ['array', function ($attribute, $value, $fail) {
                if (!empty($value) && array_diff($value, $this->tools)) {
                    $fail("The selected {$attribute} is invalid.");
                }
            }],
            'message' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return
            [
                'reason.required' => __('validation.reason_required'),
                'reason.in' => __('validation.reason_in'),
                'tool.in' => __('validation.tool_in')
            ];
    }

    public function submit()
    {
        $this->validate();

        Comm::create([
            'type' => 'user',
            'user_id' => Auth::user()->id,
            'content' => $this->message,
            'meta' => [
                'reason' => $this->reason,
                'tools' => $this->tools
            ]
        ]);

        $this->messageSent = true;
    }

    public function render()
    {
        return view('livewire.support.support')->title(__('support.help_support'));
    }
}
