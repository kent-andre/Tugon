<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Support\Audit;
use App\Support\Current;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentRequestController extends Controller
{
    public function index()
    {
        $docTypes = collect(config('pabarang.doc_types'))
            ->map(fn ($t) => ['name' => $t['name'], 'icon' => $t['icon']]);

        $requests = DocumentRequest::with(['resident', 'logs'])->latest()->get()
            ->map->toAdminArray()->values();

        return view('admin.documents', compact('docTypes', 'requests'));
    }

    /** Status change from the drawer (AJAX). Returns the updated request. */
    public function update(Request $request, DocumentRequest $document)
    {
        $data = $request->validate([
            'status'   => 'required|in:processing,ready,completed,rejected',
            'note'     => 'nullable|string|max:1000',
            'assessed' => 'nullable|boolean',
        ]);

        $from = $document->status;
        $to   = $data['status'];
        $note = trim((string) ($data['note'] ?? ''));

        if (! in_array($to, DocumentRequest::NEXT[$from] ?? [], true)) {
            return response()->json(['message' => "A {$from} request can't be moved to {$to}. Refresh the page."], 422);
        }
        if ($to === 'rejected' && $note === '') {
            return response()->json(['message' => 'Add a reason so the resident knows what to fix.'], 422);
        }
        if ($document->type === 'indigency' && in_array($from, ['pending', 'processing'], true) && array_key_exists('assessed', $data)) {
            $document->assessed = (bool) $data['assessed'];
        }
        if ($to === 'ready' && $document->type === 'indigency' && ! $document->assessed) {
            return response()->json(['message' => 'Complete the social worker assessment first.'], 422);
        }

        DB::transaction(function () use ($document, $to, $note) {
            $document->status = $to;
            $document->note = $note !== '' ? $note : null;
            $document->handled_by = Current::adminId();
            $document->save();
            $document->logs()->create(['status' => $to, 'admin_id' => Current::adminId()]);
        });

        Audit::log("Document request: {$from} -> {$to}", $document->ref, array_filter([
            'type' => $document->type,
            'note' => $note ?: null,
        ]));

        return response()->json(['request' => $document->fresh(['resident', 'logs'])->toAdminArray()]);
    }

    /** Uploaded ID / medical certificate. Private disk, admins only. */
    public function file(DocumentRequest $document, int $index)
    {
        $file = ($document->files ?? [])[$index] ?? null;
        abort_unless($file && Storage::disk('local')->exists($file['path']), 404);

        Audit::log('Viewed document request file', $document->ref, ['file' => $file['label'] ?? 'file']);

        return Storage::disk('local')->response($file['path'], $file['name'] ?? null, [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
