<?php

namespace App\Rules;

use App\Enums\SourceProvider;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CsvFile implements ValidationRule
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
        if ($this->source !== SourceProvider::CSV->value) {
            return;
        }

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $validMimeTypes = ['text/plain', 'text/csv', 'application/vnd.ms-excel', 'text/tsv'];
            if (!in_array($value->getMimeType(), $validMimeTypes)) {
                $fail("The file provided must be a valid CSV file.");
            }
        } else {
            $fail("It is required to provide a CSV file.");
        }
    }
}
