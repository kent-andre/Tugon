<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Support\Audit;
use App\Support\Current;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CalendarController extends Controller
{
    public function index()
    {
        $me = view()->shared('me');
        $canEdit = in_array(is_array($me) ? ($me['role'] ?? '') : ($me->role ?? ''), ['captain', 'secretary'], true);

        return view('admin.calendar', [
            'categories' => config('pabarang.event_categories'),
            'hearingColor' => config('pabarang.hearing_color'),
            'canEdit' => $canEdit,
        ]);
    }

    /** JSON for FullCalendar: all events (public and officials-only) plus blotter hearings. */
    public function feed(Request $request)
    {
        [$from, $to] = $this->range($request);

        $events = Event::between($from, $to)->orderBy('starts_at')->get()
            ->map(fn (Event $e) => $e->toCalendarArray(true));

        return response()->json($events->concat($this->hearings($from, $to))->values());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = Current::adminId();

        $event = Event::create($data);
        Audit::log('Added calendar event', $event->title, []);

        return response()->json(['event' => $event->toCalendarArray(true)]);
    }

    public function update(Request $request, Event $event)
    {
        $event->update($this->validated($request));
        Audit::log('Updated calendar event', $event->title, []);

        return response()->json(['event' => $event->fresh()->toCalendarArray(true)]);
    }

    public function destroy(Event $event)
    {
        Audit::log('Deleted calendar event', $event->title, []);
        $event->delete();

        return response()->json(['ok' => true]);
    }

    // ------------------------------------------------------------------

    private function validated(Request $request): array
    {
        $allDay = $request->boolean('all_day');

        $data = $request->validate([
            'title'       => 'required|string|max:160',
            'category'    => ['required', Rule::in(array_keys(config('pabarang.event_categories')))],
            'location'    => 'nullable|string|max:160',
            'description' => 'nullable|string|max:2000',
            'starts_at'   => 'required|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'all_day'     => 'nullable|boolean',
            'is_public'   => 'nullable|boolean',
            'allows_preregistration' => 'nullable|boolean',
        ], [
            'ends_at.after_or_equal' => 'The end must be on or after the start.',
        ]);

        $data['all_day'] = $allDay;
        $data['is_public'] = $request->boolean('is_public');
        // Pre-registration only makes sense for public Health events.
        $data['allows_preregistration'] = $request->boolean('allows_preregistration')
            && $data['category'] === 'health' && $data['is_public'];

        if ($allDay) {
            $data['starts_at'] = Carbon::parse($data['starts_at'])->startOfDay();
            $data['ends_at'] = ! empty($data['ends_at']) ? Carbon::parse($data['ends_at'])->startOfDay() : null;
        }

        return $data;
    }

    /** FullCalendar sends ?start=...&end=... ; default to this month. */
    private function range(Request $request): array
    {
        try {
            $from = Carbon::parse($request->query('start', now()->startOfMonth()->toDateString()))->startOfDay();
            $to = Carbon::parse($request->query('end', now()->endOfMonth()->toDateString()))->endOfDay();
        } catch (\Throwable $e) {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        }
        return [$from, $to];
    }

    /** Scheduled blotter hearings (read-only, admin calendar only). */
    private function hearings(Carbon $from, Carbon $to)
    {
        $color = config('pabarang.hearing_color', '#6B7280');

        return DB::table('blotters')
            ->whereNotNull('hearing_at')
            ->whereBetween('hearing_at', [$from, $to])
            ->whereIn('status', ['summons', 'hearing'])   // open cases with a hearing set
            ->orderBy('hearing_at')
            ->get(['id', 'complainant', 'respondent', 'hearing_at', 'hearing_place', 'mediator'])
            ->map(function ($b) use ($color) {
                $ref = 'BR-' . str_pad((string) $b->id, 5, '0', STR_PAD_LEFT);
                $at = Carbon::parse($b->hearing_at);
                return [
                    'id'              => 'h' . $b->id,
                    'title'           => 'Hearing ' . $ref,
                    'start'           => $at->format('Y-m-d\TH:i:s'),
                    'end'             => $at->copy()->addHour()->format('Y-m-d\TH:i:s'),
                    'allDay'          => false,
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'editable'        => false,
                    'extendedProps'   => [
                        'kind'          => 'hearing',
                        'categoryLabel' => 'Blotter hearing',
                        'parties'       => $b->complainant . ' vs. ' . $b->respondent,
                        'location'      => (string) ($b->hearing_place ?: config('pabarang.hearing_place')),
                        'mediator'      => (string) $b->mediator,
                        'when'          => $at->format('D, M j, Y, g:i A'),
                        'url'           => url('/admin/blotters'),
                    ],
                ];
            });
    }
}
