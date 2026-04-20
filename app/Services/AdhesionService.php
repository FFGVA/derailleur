<?php

namespace App\Services;

use App\Enums\MemberStatus;
use App\Mail\AdhesionConfirmationMail;
use App\Mail\AdhesionWelcomeMail;
use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdhesionService
{
    /**
     * Process a new adhesion submission (email unknown or pending re-submission).
     * Creates member as P, sends activation email.
     */
    public static function submitNew(array $data): Member
    {
        $email = $data['email'];
        $member = Member::where('email', $email)->first();

        $photoOk = $data['photo_ok'] ?? true;
        $metadata = $data['metadata'] ?? null;

        if (! $member) {
            $member = Member::create([
                'first_name' => $data['prenom'],
                'last_name' => $data['nom'],
                'email' => $email,
                'is_invitee' => false,
                'photo_ok' => $photoOk,
                'statuscode' => MemberStatus::EnAttente->value,
                'metadata' => $metadata,
            ]);

            if (! empty($data['telephone'])) {
                MemberPhone::create([
                    'member_id' => $member->id,
                    'phone_number' => $data['telephone'],
                    'label' => 'Mobile principal',
                ]);
            }
        } elseif ($member->getRawOriginal('statuscode') === MemberStatus::EnAttente->value) {
            $member->update([
                'first_name' => $data['prenom'],
                'last_name' => $data['nom'],
                'photo_ok' => $photoOk,
                'metadata' => $metadata,
            ]);

            if (! empty($data['telephone'])) {
                $phone = $member->phones()->first();
                if ($phone) {
                    $phone->update(['phone_number' => $data['telephone']]);
                } else {
                    MemberPhone::create([
                        'member_id' => $member->id,
                        'phone_number' => $data['telephone'],
                        'label' => 'Mobile principal',
                    ]);
                }
            }
        }

        $rawToken = bin2hex(random_bytes(32));
        $member->update([
            'activation_token' => Hash::make($rawToken),
            'activation_sent_at' => now(),
        ]);

        $activationUrl = url("/adhesion/confirmer?token={$rawToken}&email={$member->email}");
        Mail::send(new AdhesionWelcomeMail($member, $activationUrl));

        return $member;
    }

    /**
     * Process an existing non-member requesting membership.
     * Sets membership_requested_at, sends invoice + confirmation directly.
     */
    public static function processExistingNonMember(Member $member, ?array $metadata = null, ?bool $photoOk = null): void
    {
        $updates = ['membership_requested_at' => now()];
        if ($metadata !== null) {
            $updates['metadata'] = $metadata ?: $member->metadata;
        }
        if ($photoOk !== null) {
            $updates['photo_ok'] = $photoOk;
        }
        $member->update($updates);

        InvoiceEmailService::createAndSendCotisation($member, (int) date('Y'));
        Mail::send(new AdhesionConfirmationMail($member));
    }

    /**
     * Confirm a member's email after they click the activation link.
     * Sets status to N, sets membership_requested_at, sends invoice + confirmation.
     */
    public static function confirmEmail(Member $member): void
    {
        $member->update([
            'email_verified_at' => now(),
            'activation_token' => null,
            'statuscode' => MemberStatus::NonMembre->value,
            'membership_requested_at' => now(),
        ]);

        InvoiceEmailService::createAndSendCotisation($member, (int) date('Y'));
        Mail::send(new AdhesionConfirmationMail($member));
    }
}
