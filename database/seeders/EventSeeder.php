<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Member;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Sortie vélo autour du lac Léman',
                'description' => '<p>Une magnifique sortie à vélo autour du lac Léman. Parcours de 80 km avec pause café à Nyon. Niveau intermédiaire recommandé.</p>',
                'location' => 'Départ : Bains des Pâquis, Genève',
                'starts_at' => '2026-04-12 08:00:00',
                'ends_at' => '2026-04-12 16:00:00',
                'max_participants' => 20,
                'price' => 15.00,
                'statuscode' => 'P',
            ],
            [
                'title' => 'Atelier mécanique vélo - Les bases',
                'description' => '<p>Apprenez à entretenir votre vélo : réglage des freins, changement de chambre à air, lubrification de la chaîne. Tout le matériel est fourni.</p>',
                'location' => 'Local FFGVA, Rue de Carouge 42, Genève',
                'starts_at' => '2026-04-05 14:00:00',
                'ends_at' => '2026-04-05 17:00:00',
                'max_participants' => 12,
                'price' => 25.00,
                'statuscode' => 'P',
            ],
            [
                'title' => 'Gravel ride dans le Jura genevois',
                'description' => '<p>Exploration des chemins de gravel dans le Jura genevois. Parcours vallonné de 55 km avec dénivelé positif de 800 m. Vélo gravel obligatoire.</p>',
                'location' => 'Parking de la Plaine de Plainpalais, Genève',
                'starts_at' => '2026-05-03 07:30:00',
                'ends_at' => '2026-05-03 14:00:00',
                'max_participants' => 15,
                'price' => 10.00,
                'statuscode' => 'N',
            ],
            [
                'title' => 'Assemblée générale annuelle 2026',
                'description' => '<p>Assemblée générale annuelle du club Fast and Female Geneva. Bilan de l\'année, élection du comité, et apéritif offert.</p>',
                'location' => 'Salle communale de Plainpalais, Genève',
                'starts_at' => '2026-03-28 18:30:00',
                'ends_at' => '2026-03-28 21:00:00',
                'max_participants' => 50,
                'price' => 0.00,
                'statuscode' => 'P',
            ],
            [
                'title' => 'Stage initiation vélo de route',
                'description' => '<p>Stage de deux jours pour débutantes souhaitant se mettre au vélo de route. Encadrement par des monitrices diplômées. Prêt de vélo possible.</p>',
                'location' => 'Vélodrome de Genève, Carouge',
                'starts_at' => '2026-05-16 09:00:00',
                'ends_at' => '2026-05-17 16:00:00',
                'max_participants' => 10,
                'price' => 75.00,
                'statuscode' => 'N',
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        // Attach some members to events
        $activeMembers = Member::where('statuscode', 'A')->get();

        // Sortie vélo - 6 participantes
        $sortie = Event::where('title', 'like', '%lac Léman%')->first();
        if ($sortie && $activeMembers->count() >= 6) {
            foreach ($activeMembers->take(6) as $member) {
                $sortie->members()->attach($member->id, ['status' => 'C', 'updated_at' => now()]);
            }
        }

        // Atelier mécanique - 4 participantes
        $atelier = Event::where('title', 'like', '%mécanique%')->first();
        if ($atelier && $activeMembers->count() >= 8) {
            foreach ($activeMembers->slice(2, 4) as $member) {
                $atelier->members()->attach($member->id, ['status' => 'N', 'updated_at' => now()]);
            }
        }

        // AG - 8 participantes
        $ag = Event::where('title', 'like', '%Assemblée%')->first();
        if ($ag && $activeMembers->count() >= 8) {
            foreach ($activeMembers->take(8) as $member) {
                $ag->members()->attach($member->id, ['status' => 'C', 'updated_at' => now()]);
            }
        }
    }
}
