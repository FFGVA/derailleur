<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $member->load('phones');

        $upcomingEvents = Event::where('starts_at', '>=', now())
            ->where('statuscode', EventStatus::Publie)
            ->whereNull('deleted_at')
            ->orderBy('starts_at')
            ->get();

        // Load member's registration status for each event
        $registrations = $member->events()
            ->whereIn('events.id', $upcomingEvents->pluck('id'))
            ->get()
            ->keyBy('id');

        foreach ($upcomingEvents as $event) {
            $event->memberRegistration = $registrations->get($event->id)?->pivot;
        }

        $isChef = $member->ledEvents()
            ->whereNull('events.deleted_at')
            ->where('starts_at', '>=', now()->subWeek())
            ->exists();

        return view('portail.dashboard', [
            'member' => $member,
            'upcomingEvents' => $upcomingEvents,
            'isChef' => $isChef,
        ]);
    }
}
