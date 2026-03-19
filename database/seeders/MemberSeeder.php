<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'first_name' => 'Sophie',
                'last_name' => 'Dupont',
                'email' => 'sophie.dupont@gmail.com',
                'date_of_birth' => '1990-03-15',
                'address' => 'Rue du Rhône 14',
                'postal_code' => '1204',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2024-01-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 22 301 45 67', 'label' => 'Fixe', 'is_whatsapp' => false, 'sort_order' => 0],
                    ['phone_number' => '+41 79 301 45 67', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Favre',
                'email' => 'marie.favre@bluewin.ch',
                'date_of_birth' => '1985-07-22',
                'address' => 'Avenue de Champel 38',
                'postal_code' => '1206',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2023-06-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 79 234 56 78', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                    ['phone_number' => '+41 22 234 56 78', 'label' => 'Bureau', 'is_whatsapp' => false, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Isabelle',
                'last_name' => 'Rochat',
                'email' => 'isabelle.rochat@sunrise.ch',
                'date_of_birth' => '1992-11-08',
                'address' => 'Rue de Carouge 72',
                'postal_code' => '1205',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2025-01-01',
                'membership_end' => '2026-12-31',
                'is_invitee' => true,
                'phones' => [
                    ['phone_number' => '+41 78 345 67 89', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                    ['phone_number' => '+41 22 345 67 89', 'label' => 'Domicile', 'is_whatsapp' => false, 'sort_order' => 1],
                    ['phone_number' => '+41 22 345 67 00', 'label' => 'Bureau', 'is_whatsapp' => false, 'sort_order' => 2],
                ],
            ],
            [
                'first_name' => 'Claire',
                'last_name' => 'Bonnet',
                'email' => 'claire.bonnet@proton.me',
                'date_of_birth' => '1988-04-30',
                'address' => 'Chemin des Crêts 5',
                'postal_code' => '1202',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2024-03-15',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 76 456 78 90', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                    ['phone_number' => '+41 22 456 78 90', 'label' => 'Fixe', 'is_whatsapp' => false, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Nathalie',
                'last_name' => 'Perret',
                'email' => 'nathalie.perret@gmail.com',
                'date_of_birth' => '1995-09-12',
                'address' => 'Rue de Lausanne 45',
                'postal_code' => '1201',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'P',
                'membership_start' => null,
                'membership_end' => null,
                'is_invitee' => true,
                'phones' => [
                    ['phone_number' => '+41 79 567 89 01', 'label' => 'Mobile', 'is_whatsapp' => false, 'sort_order' => 0],
                ],
            ],
            [
                'first_name' => 'Valérie',
                'last_name' => 'Muller',
                'email' => 'valerie.muller@outlook.com',
                'date_of_birth' => '1983-01-25',
                'address' => 'Route de Chêne 88',
                'postal_code' => '1207',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2024-01-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 22 789 01 23', 'label' => 'Fixe', 'is_whatsapp' => false, 'sort_order' => 0],
                    ['phone_number' => '+41 76 789 01 23', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Léa',
                'last_name' => 'Girard',
                'email' => 'lea.girard@gmail.com',
                'date_of_birth' => '1998-06-18',
                'address' => 'Boulevard Carl-Vogt 20',
                'postal_code' => '1205',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2025-01-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 78 678 90 12', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                ],
            ],
            [
                'first_name' => 'Catherine',
                'last_name' => 'Blanc',
                'email' => 'catherine.blanc@bluewin.ch',
                'date_of_birth' => '1979-12-03',
                'address' => 'Rue des Eaux-Vives 15',
                'postal_code' => '1207',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'I',
                'membership_start' => '2023-01-01',
                'membership_end' => '2024-12-31',
                'phones' => [
                    ['phone_number' => '+41 76 789 01 23', 'label' => 'Mobile', 'is_whatsapp' => false, 'sort_order' => 0],
                    ['phone_number' => '+41 22 789 01 24', 'label' => 'Domicile', 'is_whatsapp' => false, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Émilie',
                'last_name' => 'Roux',
                'email' => 'emilie.roux@gmail.com',
                'date_of_birth' => '1993-08-27',
                'address' => 'Chemin de la Vendée 12',
                'postal_code' => '1213',
                'city' => 'Petit-Lancy',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2024-06-01',
                'membership_end' => '2026-12-31',
                'is_invitee' => true,
                'phones' => [
                    ['phone_number' => '+41 79 890 12 34', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                    ['phone_number' => '+41 22 890 12 34', 'label' => 'Bureau', 'is_whatsapp' => false, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Sandrine',
                'last_name' => 'Martin',
                'email' => 'sandrine.martin@sunrise.ch',
                'date_of_birth' => '1987-05-14',
                'address' => 'Route de Florissant 62',
                'postal_code' => '1206',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2025-01-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 78 901 23 45', 'label' => 'Mobile', 'is_whatsapp' => false, 'sort_order' => 0],
                ],
            ],
            [
                'first_name' => 'Aurélie',
                'last_name' => 'Jolivet',
                'email' => 'aurelie.jolivet@proton.me',
                'date_of_birth' => '1991-02-09',
                'address' => 'Rue de la Servette 30',
                'postal_code' => '1202',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'D',
                'membership_start' => null,
                'membership_end' => null,
                'phones' => [
                    ['phone_number' => '+41 76 012 34 56', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                    ['phone_number' => '+41 22 012 34 56', 'label' => 'Fixe', 'is_whatsapp' => false, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Céline',
                'last_name' => 'Jacquet',
                'email' => 'celine.jacquet@gmail.com',
                'date_of_birth' => '1996-10-31',
                'address' => 'Avenue du Mail 9',
                'postal_code' => '1205',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2025-02-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 79 123 45 67', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                ],
            ],
            [
                'first_name' => 'Françoise',
                'last_name' => 'Lévy',
                'email' => 'francoise.levy@bluewin.ch',
                'date_of_birth' => '1975-07-07',
                'address' => 'Quai du Mont-Blanc 3',
                'postal_code' => '1201',
                'city' => 'Genève',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2023-01-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 22 456 78 90', 'label' => 'Fixe', 'is_whatsapp' => false, 'sort_order' => 0],
                    ['phone_number' => '+41 79 456 78 90', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 1],
                ],
            ],
            [
                'first_name' => 'Morgane',
                'last_name' => 'Savoy',
                'email' => 'morgane.savoy@outlook.com',
                'date_of_birth' => '1999-03-21',
                'address' => 'Chemin de Pinchat 28',
                'postal_code' => '1227',
                'city' => 'Carouge',
                'country' => 'CH',
                'statuscode' => 'A',
                'membership_start' => '2025-03-01',
                'membership_end' => '2026-12-31',
                'phones' => [
                    ['phone_number' => '+41 78 234 56 78', 'label' => 'Mobile', 'is_whatsapp' => false, 'sort_order' => 0],
                ],
            ],
            [
                'first_name' => 'Delphine',
                'last_name' => 'Wenger',
                'email' => 'delphine.wenger@gmail.com',
                'date_of_birth' => '1986-11-16',
                'address' => 'Avenue de Vaudagne 10',
                'postal_code' => '1217',
                'city' => 'Meyrin',
                'country' => 'CH',
                'statuscode' => 'P',
                'membership_start' => null,
                'membership_end' => null,
                'phones' => [
                    ['phone_number' => '+41 76 345 67 89', 'label' => 'Mobile', 'is_whatsapp' => true, 'sort_order' => 0],
                    ['phone_number' => '+41 22 345 67 00', 'label' => 'Bureau', 'is_whatsapp' => false, 'sort_order' => 1],
                ],
            ],
        ];

        foreach ($members as $memberData) {
            $phones = $memberData['phones'] ?? [];
            unset($memberData['phones']);

            $member = Member::create($memberData);

            foreach ($phones as $phone) {
                $member->phones()->create($phone);
            }
        }
    }
}
