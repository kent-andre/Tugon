<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Support\ConcernStore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminConcernController extends Controller
{
    private const ICONS = [
        'Crime/Safety'   => ['ti-alert-triangle', 'var(--ref-red)'],
        'Environment'    => ['ti-trash', '#92650C'],
        'Infrastructure' => ['ti-road', 'var(--ref-blue)'],
        'Water Supply'   => ['ti-droplet', 'var(--ref-blue)'],
        'Streetlights'   => ['ti-bulb', 'var(--ref-blue)'],
        'Noise'          => ['ti-volume', 'var(--ref-blue)'],
        'Other'          => ['ti-dots', 'var(--ref-blue)'],
    ];

    public function dashboard()
    {
        $all = collect(ConcernStore::all()); // already newest first

        $pins = $all->filter(fn ($c) => ($c['latitude'] ?? null) !== null && ($c['longitude'] ?? null) !== null)
            ->map(fn ($c) => [
                'lat'   => (float) $c['latitude'],
                'lng'   => (float) $c['longitude'],
                'label' => $c['tracking_code'] . ' - ' . $c['category'] . ' (' . $c['purok'] . ')',
            ])
            ->values()
            ->all();

        return view('admin.dashboard', [
            'counts' => $this->counts($all),
            'recent' => $all->take(4)->map(fn ($c) => $this->present($c))->values()->all(),
            'pins'   => $pins,
            'pendingAccounts' => Resident::where('status', 'pending')->count(),
            // Photo check tile: newest 3 flagged photos waiting for an official
            'photoQueue' => $all->filter(fn ($c) => self::photoNeedsReview($c))->take(3)
                ->map(fn ($c) => $this->present($c))->values()->all(),
        ]);
    }

    public function index()
    {
        $all = collect(ConcernStore::all());

        return view('admin.concerns', [
            'counts'   => $this->counts($all),
            'concerns' => $all->map(fn ($c) => $this->present($c))->values()->all(),
        ]);
    }

    // Concern Map page: only reports that have a saved location.
    public function map()
    {
        return view('admin.map', ['concerns' => $this->mapData()]);
    }

    // Old link /admin/map/3d: opens the Concern Map with the 3D view on.
    public function map3d()
    {
        return redirect('/admin/map?view=3d');
    }

    // Reports that have a saved location, shaped for the 2D and 3D maps.
    private function mapData(): array
    {
        return collect(ConcernStore::all())
            ->filter(fn ($c) => ($c['latitude'] ?? null) !== null && ($c['longitude'] ?? null) !== null)
            ->map(fn ($c) => [
                'ref'      => $c['tracking_code'],
                'title'    => mb_strimwidth($c['description'], 0, 60, '...'),
                'category' => $c['category'],
                'zone'     => $c['purok'],
                'status'   => $c['status'] ?? 'pending',
                'verify'   => $c['verification'] ?? 'unverified',
                // Privacy: the real name is never sent for anonymous reports.
                'reporter' => $this->reporterName($c),
                'lat'      => (float) $c['latitude'],
                'lng'      => (float) $c['longitude'],
                'filed'    => date('M j, Y', strtotime($c['created_at'])),
            ])
            ->values()
            ->all();
    }

    // One call saves everything in the modal: verification, status, assignee, notes, public update.
    public function update(Request $request, string $code)
    {
        $data = $request->validate([
            'status'         => ['sometimes', Rule::in(['pending', 'progress', 'resolved', 'blotter'])],
            'verification'   => ['sometimes', Rule::in(['unverified', 'verified', 'flagged'])],
            'flag_reason'    => ['nullable', 'string', 'max:300', 'required_if:verification,flagged'],
            'assigned_to'    => ['nullable', 'string', 'max:120'],
            'internal_notes' => ['nullable', 'string', 'max:1000'],
            'public_update'  => ['nullable', 'string', 'max:500'],
            'blotter_ref'    => ['nullable', 'string', 'max:20'],
        ]);

        // A flag reason only makes sense while the report is flagged.
        if (($data['verification'] ?? null) !== null && $data['verification'] !== 'flagged') {
            $data['flag_reason'] = null;
        }

        $concern = ConcernStore::update($code, $data);
        abort_if($concern === null, 404);

        return response()->json(['ok' => true, 'concern' => $this->present($concern)]);
    }

    private function counts($all): array
    {
        return [
            'total'      => $all->count(),
            'unverified' => $all->where('verification', 'unverified')->count(),
            'pending'    => $all->where('status', 'pending')->count(),
            'progress'   => $all->whereIn('status', ['progress', 'blotter'])->count(),
            'resolved'   => $all->where('status', 'resolved')->count(),
            // Photo check (Step 8): flagged photos no official has decided on yet
            'photos_to_review' => $all->filter(fn ($c) => self::photoNeedsReview($c))->count(),
        ];
    }

    private static function photoNeedsReview(array $c): bool
    {
        return ($c['photo_check_status'] ?? '') === 'flagged' && empty($c['photo_review']);
    }

    // Photo check result for the admin views (also used by Admin\PhotoReviewController).
    // Residents never see this.
    public static function photoFor(array $c): array
    {
        $flags = $c['photo_flags'] ?? [];
        if (is_string($flags)) {
            $flags = json_decode($flags, true) ?: [];
        }
        $reviewedAt = $c['photo_reviewed_at'] ?? null;

        return [
            'status'       => $c['photo_check_status'] ?? 'pending', // pending | clear | flagged | error
            'flags'        => array_values($flags),                  // [{code, label, severity, source, related?}]
            'source'       => $c['photo_check_source'] ?? '',        // basic | basic+ai | basic+ai_failed
            'sensitive'    => ! empty($c['photo_sensitive']),        // blur until clicked
            'review'       => $c['photo_review'] ?? '',              // '' | ok | kept
            'reviewed_by'  => $c['photo_reviewed_by'] ?? '',
            'reviewed_at'  => $reviewedAt ? date('M j, Y g:i A', strtotime($reviewedAt)) : '',
            'needs_review' => self::photoNeedsReview($c),
        ];
    }

    private function reporterName(array $c): string
    {
        $anonymous = ! empty($c['is_anonymous']) || empty($c['reporter_name']);
        return $anonymous ? 'Anonymous' : $c['reporter_name'];
    }

    // Shapes a concern for the admin views. Names are never shown for anonymous reports.
    private function present(array $c): array
    {
        [$icon, $color] = self::ICONS[$c['category']] ?? self::ICONS['Other'];

        return [
            'ref'         => $c['tracking_code'],
            'title'       => mb_strimwidth($c['description'], 0, 60, '...'),
            'cat'         => $c['category'],
            'zone'        => $c['purok'],
            'reporter'    => $this->reporterName($c),
            'contact'     => $c['contact'] ?? '',
            'desc'        => $c['description'],
            'filed'       => date('M j, Y', strtotime($c['created_at'])),
            'status'      => $c['status'] ?? 'pending',
            'verify'      => $c['verification'] ?? 'unverified',
            'flag_reason' => $c['flag_reason'] ?? '',
            'assigned'    => $c['assigned_to'] ?? '',
            'internal'    => $c['internal_notes'] ?? '',
            'public'      => $c['public_update'] ?? '',
            'blotter'     => $c['blotter_ref'] ?? '',
            // Officials-only photo route, never a public /storage link
            'evidence'    => ! empty($c['evidence_path']) ? url('/admin/concerns/' . rawurlencode($c['tracking_code']) . '/photo') : '',
            'anonymous'   => ! empty($c['is_anonymous']) || empty($c['reporter_name']),
            'photo'       => self::photoFor($c),
            'icon'        => $icon,
            'color'       => $color,
        ];
    }
}