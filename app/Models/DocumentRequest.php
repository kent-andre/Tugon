<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentRequest extends Model
{
    protected $fillable = ['ref', 'resident_id', 'type', 'status', 'summary', 'details', 'files', 'note', 'assessed', 'handled_by'];

    protected $casts = [
        'details'  => 'array',
        'files'    => 'array',
        'assessed' => 'boolean',
    ];

    /** Allowed status moves. Anything else is refused by the controller. */
    public const NEXT = [
        'pending'    => ['processing', 'rejected'],
        'processing' => ['ready', 'rejected'],
        'ready'      => ['completed'],
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(DocumentRequestLog::class)->orderBy('id');
    }

    /** DOC-2026-0001. Call inside a DB transaction. */
    public static function nextRef(): string
    {
        $prefix = 'DOC-' . now()->year . '-';
        $last = static::where('ref', 'like', $prefix . '%')->lockForUpdate()->orderByDesc('ref')->value('ref');
        $n = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    public function typeName(): string
    {
        return config("pabarang.doc_types.{$this->type}.name", ucfirst($this->type));
    }

    public function residentName(): string
    {
        $r = $this->resident;
        if (! $r) {
            return 'Deleted account';
        }
        $name = $r->name ?? trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? ''));
        return $name !== '' ? $name : ($r->resident_code ?? 'Resident #' . $r->id);
    }

    /** [[status, iso], ...] - the shape both pages' timelines use. */
    public function logArray(): array
    {
        return $this->logs->map(fn ($l) => [$l->status, $l->created_at->toIso8601String()])->values()->all();
    }

    /** Shape the admin Document Requests page expects (see documents.blade.php). */
    public function toAdminArray(): array
    {
        $r = $this->resident;

        return [
            'id'       => $this->id,
            'ref'      => $this->ref,
            'type'     => $this->type,
            'status'   => $this->status,
            'resident' => $this->residentName(),
            'zone'     => (string) ($r?->zone ?? $r?->purok ?? ''),
            'contact'  => (string) ($r?->contact_number ?? $r?->contact ?? $r?->phone ?? ''),
            'summary'  => $this->summary,
            'details'  => (object) ($this->details ?? []),
            'files'    => collect($this->files ?? [])->map(fn ($f, $i) => [
                'name' => ($f['label'] ?? 'File') . ' - ' . ($f['name'] ?? 'file'),
                'url'  => route('admin.documents.file', [$this->id, $i]),
            ])->values()->all(),
            'note'     => (string) ($this->note ?? ''),
            'assessed' => (bool) $this->assessed,
            'log'      => $this->logArray(),
        ];
    }

    /** Shape My Reports / home expect. */
    public function toResidentArray(): array
    {
        return [
            'id'      => $this->id,
            'ref'     => $this->ref,
            'type'    => $this->type,
            'status'  => $this->status,
            'summary' => $this->summary,
            'note'    => (string) ($this->note ?? ''),
            'log'     => $this->logArray(),
        ];
    }
}
