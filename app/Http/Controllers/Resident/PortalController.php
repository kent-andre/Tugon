<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Concern;
use App\Models\DocumentRequest;
use App\Models\Event;
use App\Models\Official;
use App\Models\ResidentPopup;
use App\Support\Current;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Resident pages that only READ data: home, announcements, officials,
 * My Reports. (/transparency now points to the landing page.)
 */
class PortalController extends Controller
{
    // Concern statuses -> the 3 buckets residents see everywhere.
    private const BUCKET = ['pending' => 'pending', 'review' => 'progress', 'progress' => 'progress', 'blotter' => 'progress', 'resolved' => 'resolved', 'rejected' => 'rejected'];

    public function home()
    {
        $rid = Current::residentId();

        $mine = $this->myConcerns($rid);
        $stats = [
            'total'    => $mine->count(),
            'pending'  => $mine->where('bucket', 'pending')->count(),
            'progress' => $mine->where('bucket', 'progress')->count(),
            'resolved' => $mine->where('bucket', 'resolved')->count(),
        ];

        // Latest 3 things this resident filed (documents + concerns).
        $docs = DocumentRequest::where('resident_id', $rid)->latest()->take(3)->get()->map(fn ($d) => [
            'title'  => $d->typeName() . ' (' . $d->summary . ')',
            'status' => $d->status,
            'label'  => ($d->type === 'vaccination' && $d->status === 'ready') ? 'Scheduled' : self::docLabel($d->status),
            'meta'   => 'Requested ' . $d->created_at->format('M j'),
            'at'     => $d->created_at,
        ]);
        $concerns = $mine->take(3)->map(fn ($c) => [
            'title'  => $c['title'],
            'status' => $c['bucket'],
            'label'  => $c['label'],
            'meta'   => 'Filed ' . $c['filed']->format('M j'),
            'at'     => $c['filed'],
        ]);
        $recent = $docs->concat($concerns)->sortByDesc('at')->take(3)->values();

        $news = Announcement::forDisplay()->take(3)->get();

        // Popup: once per login session; an edited popup counts as new.
        $popup = ResidentPopup::current();
        $showPopup = null;
        if ($popup->enabled && session('popup_seen') !== $popup->version()) {
            $showPopup = $popup;
            session(['popup_seen' => $popup->version()]);
        }

        // Next public barangay events for the calendar card.
        $upcoming = Event::public()
            ->where(fn ($q) => $q->where('starts_at', '>=', now()->startOfDay())
                ->orWhere('ends_at', '>=', now()->startOfDay()))
            ->orderBy('starts_at')->take(5)->get();

        return view('resident.home', compact('stats', 'recent', 'news', 'showPopup', 'upcoming'));
    }

    public function announcements()
    {
        return view('resident.announcements', [
            'announcements' => Announcement::forDisplay()->get(),
            'categories'    => config('pabarang.announcement_categories'),
        ]);
    }

    public function officials()
    {
        return view('resident.officials', [
            'officials'   => Official::ordered()->get()->groupBy('group'),
            'groupLabels' => config('pabarang.official_groups'),
        ]);
    }

    public function myReports()
    {
        $rid = Current::residentId();

        $myDocs = DocumentRequest::with('logs')->where('resident_id', $rid)->latest()->get()
            ->map->toResidentArray()->values()->all();
        $myConcerns = $this->myConcerns($rid);

        return view('resident.my-reports', compact('myDocs', 'myConcerns'));
    }

    /**
     * The Transparency Dashboard moved to the public landing page (Nov 8).
     * Old links and bookmarks to /transparency land on that section instead.
     * The totals now come from App\Support\TransparencyStats.
     */
    public function transparency()
    {
        return redirect(url('/') . '#transparency');
    }

    // ------------------------------------------------------------------

    /**
     * This resident's concerns as plain arrays, using the real concerns columns:
     * tracking_code, description, purok and public_update (the official's note
     * the resident may see). The array keys stay the same for the views.
     */
    private function myConcerns(?int $rid): Collection
    {
        if (! $rid || ! $this->hasCol('resident_id')) {
            return collect();
        }

        $labels = ['pending' => 'Pending', 'review' => 'Reviewed', 'progress' => 'In Progress', 'blotter' => 'Referred to Blotter', 'resolved' => 'Resolved', 'rejected' => 'Rejected'];

        return Concern::where('resident_id', $rid)->latest()->get()->map(fn ($c) => [
            'id'       => $c->id,
            'ref'      => $c->tracking_code ?: ('#' . $c->id),
            // No title column: the first part of the description stands in for it
            'title'    => $c->description ? mb_strimwidth($c->description, 0, 60, '...') : (string) $c->category,
            'category' => (string) $c->category,
            'zone'     => (string) $c->purok,
            'status'   => $c->status,
            'filter'   => $c->status === 'review' ? 'review' : (self::BUCKET[$c->status] ?? 'progress'),
            'bucket'   => self::BUCKET[$c->status] ?? 'progress',
            'label'    => $labels[$c->status] ?? ucfirst((string) $c->status),
            'filed'    => $c->created_at,
            'changed'  => $c->resolved_at ?? $c->updated_at,
            'note'     => $c->public_update ?: null,
        ]);
    }

    private function hasCol(string $col): bool
    {
        static $cols = null;
        $cols ??= Schema::getColumnListing((new Concern)->getTable());
        return in_array($col, $cols, true);
    }

    private static function docLabel(string $s): string
    {
        return ['pending' => 'Pending', 'processing' => 'Processing', 'ready' => 'Ready for pickup', 'completed' => 'Completed', 'rejected' => 'Rejected'][$s] ?? ucfirst($s);
    }
}
