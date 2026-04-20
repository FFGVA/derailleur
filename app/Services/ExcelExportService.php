<?php

namespace App\Services;

use App\Enums\EventMemberStatus;
use App\Models\Event;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class ExcelExportService
{
    /**
     * Export event participants to XLSX.
     * Returns ['path' => absolute path, 'filename' => download name].
     */
    public static function exportParticipants(Event $event): array
    {
        $participants = $event->members()
            ->whereIn('event_member.status', [EventMemberStatus::Inscrit->value, EventMemberStatus::Confirme->value])
            ->whereNull('event_member.deleted_at')
            ->with('phones')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $filename = Str::slug($event->title) . '-' . $event->starts_at->format('Y-m-d') . '.xlsx';
        $tempPath = storage_path('app/private/' . $filename);

        $writer = new Writer(new Options());
        $writer->openToFile($tempPath);

        $boldStyle = new Style();
        $boldStyle->setFontBold();

        // Event header
        $writer->addRow(Row::fromValues([$event->title], $boldStyle));
        $writer->addRow(Row::fromValues(['Début', $event->starts_at->format('d.m.Y H:i')]));
        if ($event->ends_at) {
            $writer->addRow(Row::fromValues(['Fin', $event->ends_at->format('d.m.Y H:i')]));
        }
        $writer->addRow(Row::fromValues([]));

        // Column headers
        $writer->addRow(Row::fromValues(
            ['Nom', 'Prénom', 'E-mail', 'Téléphone', 'Statut', 'Présence'],
            $boldStyle
        ));

        // Data rows
        foreach ($participants as $p) {
            $phone = $p->phones->first()?->phone_number ?? '';
            $status = $p->pivot->getRawOriginal('status') === EventMemberStatus::Confirme->value ? 'Confirmée' : 'Inscrite';
            $presence = match ($p->pivot->getRawOriginal('present')) {
                1, true => 'Oui',
                0, false => 'Non',
                default => '',
            };
            $writer->addRow(Row::fromValues([
                $p->last_name,
                $p->first_name,
                $p->email,
                $phone,
                $status,
                $presence,
            ]));
        }

        $writer->close();

        return [
            'path' => $tempPath,
            'filename' => $filename,
        ];
    }
}
