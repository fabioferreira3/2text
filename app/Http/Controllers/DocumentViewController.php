<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\Document;
use Livewire\Livewire;

class DocumentViewController extends Controller
{
    public function index(Document $document)
    {
        $routeName = DocumentType::routeNames()[$document->type->value];
        return redirect()->route($routeName, ['document' => $document]);
    }
}
