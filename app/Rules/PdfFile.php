<?php

namespace App\Rules;

use App\Enums\SourceProvider;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PdfFile implements ValidationRule
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
        if ($this->source !== SourceProvider::PDF->value) {
            return;
        }

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            if ($value->getMimeType() !== 'application/pdf') {
                $fail("The file provided must be a valid PDF file.");
            }
        } else {
            $fail("It is required to provide a PDF file.");
        }
    }
}
