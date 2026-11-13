<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** Resident calendar: public barangay events only, view-only. Hearings are never shown. */
class CalendarController extends Controller
{
    public function index()
    {
        $upcoming = Event::public()
            ->where(fn ($q) => $q->where('starts_at', '>=', now()->startOfDay())
                ->orWhere('ends_at', '>=', now()->startOfDay()))
            ->orderBy('starts_at')->take(8)->get();

        return view('resident.calendar', [
            'upcoming'   => $upcoming,
            'categories' => config('pabarang.event_categories'),
        ]);
    }

    public function feed(Request $request)
    {
        try {
            $from = Carbon::parse($request->query('start', now()->startOfMonth()->toDateString()))->startOfDay();
            $to = Carbon::parse($request->query('end', now()->endOfMonth()->toDateString()))->endOfDay();
        } catch (\Throwable $e) {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        }

        return response()->json(
            Event::public()->between($from, $to)->orderBy('starts_at')->get()
                ->map(fn (Event $e) => $e->toCalendarArray(false))->values()
        );
    }
}
