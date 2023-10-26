<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DocxFile implements ValidationRule
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
        if ($this->source !== 'docx') {
            return;
        }

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            if ($value->getMimeType() !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                $fail("The {$attribute} must be a valid .docx file.");
            }
        } else {
            $fail("The {$attribute} is required.");
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The file provided must be a valid .docx file.';
    }
}
