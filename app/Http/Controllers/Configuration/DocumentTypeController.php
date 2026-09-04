<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\StoreDocumentTypeRequest;
use App\Http\Requests\Configuration\UpdateDocumentTypeRequest;
use App\Models\Configuration\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function index(): View
    {
        $documentTypes = DocumentType::latest('id')->paginate(10);

        return view('configuration.document-types.index', compact('documentTypes'));
    }

    public function create(): View
    {
        return view('configuration.document-types.create');
    }

    public function store(StoreDocumentTypeRequest $request): RedirectResponse
    {
        DocumentType::create($request->validated());

        Cache::forget('configuration_document_types');

        return redirect()->route('configuration.document-types.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    public function edit(DocumentType $documentType): View
    {
        return view('configuration.document-types.edit', compact('documentType'));
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType): RedirectResponse
    {
        $documentType->update($request->validated());

        Cache::forget('configuration_document_types');

        return redirect()->route('configuration.document-types.index')
            ->with('success', 'Tipo de documento actualizado correctamente.');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $inUse = $documentType->customers()->exists()
            || $documentType->companyClients()->exists()
            || $documentType->purchases()->exists()
            || $documentType->sales()->exists();

        if ($inUse) {
            return redirect()
                ->route('configuration.document-types.index')
                ->withErrors('No se puede eliminar un tipo de documento que está en uso.');
        }

        $documentType->delete();

        Cache::forget('configuration_document_types');

        return redirect()->route('configuration.document-types.index')
            ->with('success', 'Tipo de documento eliminado correctamente.');
    }
}
