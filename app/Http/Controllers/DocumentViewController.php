<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use Illuminate\Support\Str;

class DocumentViewController extends Controller
{
    public function index(Document $document)
    {
        $documentType = Str::ucfirst(Str::camel($document->type->value));
        if (in_array($documentType, $this->handlers())) {
            $handler = "handle" . $documentType;
            return $this->$handler($document);
        }
        $routeName = DocumentType::routeNames()[$document->type->value];
        return redirect()->route($routeName, ['document' => $document]);
    }

    protected function handleBlogPost(Document $document)
    {
        if (in_array($document->status, [DocumentStatus::IN_PROGRESS, DocumentStatus::DRAFT])) {
            return redirect()->route('blog-post-processing-view', ['document' => $document]);
        }

        return redirect()->route('blog-post-view', ['document' => $document]);
    }

    protected function handlers()
    {
        return [
            'BlogPost'
        ];
    }
}
