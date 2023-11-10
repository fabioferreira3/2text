<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Livewire\TemporaryUploadedFile;

class JsonFile implements ValidationRule
{
    protected $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->source !== 'json') {
            return;
        }

        if ($value instanceof UploadedFile) {
            if ($this->isValidJson($value)) {
                return;
            }

            $fail("The file provided must contain a valid JSON structure.");
        } else {
            $fail("It is required to provide a .JSON file.");
        }
    }

    /**
     * Determine if the file's content is a valid JSON.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    protected function isValidJson(UploadedFile $file): bool
    {
        $content = '';

        if ($file instanceof TemporaryUploadedFile) {
            // Access the content directly from Livewire's TemporaryUploadedFile
            $content = $file->get();
        } else {
            // For non-Livewire (standard Laravel UploadedFile), read the file's content
            $content = file_get_contents($file->getRealPath());
        }

        if ($content === false) {
            return false;
        }

        json_decode($content);
        return json_last_error() == JSON_ERROR_NONE;
    }
}
