<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Official;
use App\Models\ResidentPopup;
use Illuminate\Database\Seeder;

/**
 * Moves the hardcoded midterm content into the database.
 * Run: php artisan db:seed --class=Step6Seeder   (safe to re-run)
 * NOTE: names are not an official government record - verify with the barangay hall.
 */
class Step6Seeder extends Seeder
{
    public function run(): void
    {
        $officials = [
            ['council', 'Liza Bugais Adran', 'Punong Barangay', 'Overall Barangay Administration', null, '2023 - 2026', 'images/captain.jpg'],
            ['council', 'Catherine Peregrino Inso', 'Barangay Kagawad', null, null, '2023 - 2026', 'images/sb-cath.jpg'],
            ['council', 'Gil Berth Bahulay Maay', 'Barangay Kagawad', null, null, '2023 - 2026', 'images/sb-gil.jpg'],
            ['council', 'Francisco Sususco Intong Jr.', 'Barangay Kagawad', null, null, '2023 - 2026', 'images/sb-intong.jpg'],
            ['council', 'Rizza Mae Etulle Perges', 'Barangay Secretary', 'Records & Documentation', null, 'Appointed', null],
            ['council', 'Olivia Requillo Caduyac', 'Barangay Treasurer', 'Finance & Budget', null, 'Appointed', null],
            ['sk', 'Kim R. Villanueva', 'SK Chairman', 'Sangguniang Kabataan', '0927 555 0111', '2023 - 2026', null],
            ['sk', 'Aira Mae T. Domingo', 'SK Kagawad', 'Youth Sports & Recreation', '0928 555 0112', '2023 - 2026', null],
            ['sk', 'Jhon Paul S. Ferrer', 'SK Kagawad', 'Youth Livelihood', '0929 555 0113', '2023 - 2026', null],
            ['tanod', 'Federico L. Bautista', 'Chief Barangay Tanod', 'Peace & Security', '0930 555 0114', 'Appointed', null],
            ['tanod', 'Renato P. Custodio', 'Barangay Tanod', 'Peace & Security', '0931 555 0115', 'Appointed', null],
            ['tanod', 'Alfredo M. Bello', 'Barangay Tanod', 'Peace & Security', '0932 555 0116', 'Appointed', null],
            ['lupon', 'Ernesto P. Villareal', 'Lupong Tagapamayapa Member', 'Katarungang Pambarangay', '0933 555 0117', 'Appointed', null],
        ];
        foreach ($officials as $i => [$group, $name, $position, $committee, $contact, $term, $photo]) {
            Official::updateOrCreate(
                ['name' => $name],
                compact('group', 'position', 'committee', 'contact', 'term') + ['photo_path' => $photo, 'sort_order' => $i + 1]
            );
        }

        $announcements = [
            ['health', true, 'Free Anti-Rabies Vaccination Drive - September 20', 'Barangay Health Center', '2026-09-10 09:00',
             "Free anti-rabies vaccination for dogs and cats will be held at the Barangay Alejal covered court from 8:00 AM to 3:00 PM. Bring your pet's vaccination record if available. Limited to Barangay Alejal residents only."],
            ['utility', false, 'Scheduled Water Interruption - Zone 3 & Zone 4', 'Barangay Alejal Office', '2026-09-08 09:00',
             'Davao del Norte Water District has advised a scheduled water interruption in Zone 3 and Zone 4 on September 12, 6:00 AM to 6:00 PM, due to pipeline maintenance. Residents are advised to store enough water in advance.'],
            ['advisory', false, 'Reminder: Curfew for Minors Strictly Enforced', 'Barangay Tanod Office', '2026-09-05 09:00',
             'This is a reminder that the municipal curfew ordinance for minors (10:00 PM to 4:00 AM) is strictly enforced within Barangay Alejal. Parents and guardians are reminded of their responsibility under the ordinance.'],
            ['event', false, 'Barangay Assembly - September 28, 2026', 'Office of the Punong Barangay', '2026-09-03 09:00',
             'All registered residents are invited to the Barangay Assembly at 2:00 PM at the Barangay Hall covered court, where the annual budget and community programs will be presented.'],
            ['assistance', false, '4Ps Beneficiary Validation - Ongoing until September 30', 'Barangay Social Worker', '2026-08-30 09:00',
             'Pantawid Pamilyang Pilipino Program (4Ps) beneficiaries are requested to visit the Barangay Hall for annual re-validation. Bring a valid ID and your 4Ps household ID.'],
        ];
        foreach ($announcements as [$category, $pinned, $title, $postedBy, $at, $body]) {
            $a = Announcement::firstOrNew(['title' => $title]);
            $a->fill(['category' => $category, 'pinned' => $pinned, 'posted_by' => $postedBy, 'body' => $body]);
            $a->created_at = $at;
            $a->updated_at = $at;
            $a->save();
        }

        ResidentPopup::firstOrCreate(['id' => 1], [
            'enabled'  => true,
            'category' => 'event',
            'title'    => 'Barangay Assembly, September 28',
            'body'     => 'All registered residents are invited to the Barangay Assembly at 2:00 PM at the Barangay Hall covered court, where the annual budget and community programs will be presented.',
        ]);
    }
}
