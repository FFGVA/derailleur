<?php

namespace App\Http\Controllers\Api;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Mail\EventRegistrationExistingMail;
use App\Mail\EventRegistrationNewMail;
use App\Models\Event;
use App\Models\Member;
use App\Models\MemberMagicToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EventRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'event_id' => ['required', 'integer'],
        ]);

        if ($request->filled('website')) {
            return response()->json(['ok' => true]);
        }

        $event = Event::where('id', $request->input('event_id'))
            ->where('statuscode', EventStatus::Publie->value)
            ->whereNull('deleted_at')
            ->first();

        if (!$event || $event->isFull()) {
            return response()->json(['ok' => true]);
        }

        $email = $request->input('email');
        $member = Member::where('email', $email)
            ->whereIn('statuscode', Member::PORTAL_ACCESSIBLE_STATUSES)
            ->first();

        if ($member) {
            [$token, $rawToken] = MemberMagicToken::generateFor($member, 60);

            $confirmUrl = url("/inscription-event/confirmer?token={$rawToken}&event_id={$event->id}");
            $applicablePrice = $event->priceForMember($member);

            Mail::send(new EventRegistrationExistingMail(
                member: $member,
                event: $event,
                confirmUrl: $confirmUrl,
                expiresAt: $token->expires_at->format('d.m.Y à H:i'),
                applicablePrice: $applicablePrice,
            ));
        } else {
            $registrationUrl = URL::temporarySignedRoute(
                'inscription-event.nouveau',
                now()->addHours(24),
                ['event_id' => $event->id, 'email' => $email],
            );

            $price = $event->price_non_member ?? $event->price;

            Mail::send(new EventRegistrationNewMail(
                email: $email,
                event: $event,
                registrationUrl: $registrationUrl,
                price: $price,
            ));
        }

        return response()->json(['ok' => true]);
    }
}
