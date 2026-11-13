<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'category', 'description', 'location', 'starts_at', 'ends_at',
        'all_day', 'is_public', 'allows_preregistration', 'created_by',
    ];

    protected $casts = [
        'starts_at'              => 'datetime',
        'ends_at'                => 'datetime',
        'all_day'                => 'boolean',
        'is_public'              => 'boolean',
        'allows_preregistration' => 'boolean',
    ];

    public function scopePublic(Builder $q): Builder
    {
        return $q->where('is_public', true);
    }

    /** Events that touch the [from, to) range. */
    public function scopeBetween(Builder $q, $from, $to): Builder
    {
        return $q->where('starts_at', '<', $to)
            ->where(fn ($w) => $w->where('ends_at', '>=', $from)
                ->orWhere(fn ($x) => $x->whereNull('ends_at')->where('starts_at', '>=', $from)));
    }

    public function label(): string
    {
        return config("pabarang.event_categories.{$this->category}.label", ucfirst((string) $this->category));
    }

    public function color(): string
    {
        return config("pabarang.event_categories.{$this->category}.color", '#4F5FE0');
    }

    /** Residents may pre-register only for a public, upcoming Health event that allows it. */
    public function canPreregister(): bool
    {
        return $this->is_public && $this->allows_preregistration && $this->category === 'health'
            && $this->starts_at->copy()->endOfDay()->isFuture();
    }

    /** "Sat, Nov 21, 2026, 8:00 AM to 12:00 PM" style text. */
    public function whenText(): string
    {
        $s = $this->starts_at;
        $e = $this->ends_at;
        if ($this->all_day) {
            if (! $e || $e->isSameDay($s)) {
                return $s->format('D, M j, Y') . ' (whole day)';
            }
            return $s->format('M j') . ' to ' . $e->format('M j, Y') . ' (whole day)';
        }
        if (! $e) {
            return $s->format('D, M j, Y, g:i A');
        }
        return $e->isSameDay($s)
            ? $s->format('D, M j, Y, g:i A') . ' to ' . $e->format('g:i A')
            : $s->format('M j, g:i A') . ' to ' . $e->format('M j, Y, g:i A');
    }

    /**
     * Shape FullCalendar reads. All-day end dates are stored inclusive (the
     * last day), FullCalendar wants the day after, so one day is added here.
     */
    public function toCalendarArray(bool $forAdmin = false): array
    {
        $end = $this->ends_at;
        if ($this->all_day) {
            $end = ($end ?? $this->starts_at)->copy()->addDay()->startOfDay();
        }
        $color = $this->color();

        return [
            'id'              => 'e' . $this->id,
            'title'           => $this->title,
            'start'           => $this->all_day ? $this->starts_at->toDateString() : $this->starts_at->format('Y-m-d\TH:i:s'),
            'end'             => $end ? ($this->all_day ? $end->toDateString() : $end->format('Y-m-d\TH:i:s')) : null,
            'allDay'          => $this->all_day,
            'backgroundColor' => $color,
            'borderColor'     => $color,
            'extendedProps'   => array_filter([
                'kind'         => 'event',
                'eventId'      => $this->id,
                'category'     => $this->category,
                'categoryLabel' => $this->label(),
                'location'     => (string) $this->location,
                'description'  => (string) $this->description,
                'when'         => $this->whenText(),
                'isPublic'     => $forAdmin ? $this->is_public : null,
                'prereg'       => $this->allows_preregistration,
                'preregUrl'    => $this->canPreregister() ? url('/request-document?event=' . $this->id) : null,
                // raw values for the admin edit form
                'rawStart'     => $forAdmin ? $this->starts_at->format($this->all_day ? 'Y-m-d' : 'Y-m-d\TH:i') : null,
                'rawEnd'       => $forAdmin && $this->ends_at ? $this->ends_at->format($this->all_day ? 'Y-m-d' : 'Y-m-d\TH:i') : null,
            ], fn ($v) => $v !== null),
        ];
    }
}
