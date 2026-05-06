<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Armen Grigoryan',   'email' => 'armen.grigoryan@mail.am',   'phone' => '+37491234501'],
            ['name' => 'Narek Petrosyan',   'email' => 'narek.petrosyan@mail.am',   'phone' => '+37491234502'],
            ['name' => 'Ani Harutyunyan',   'email' => 'ani.harutyunyan@mail.am',   'phone' => '+37491234503'],
            ['name' => 'Davit Sargsyan',    'email' => 'davit.sargsyan@mail.am',    'phone' => '+37491234504'],
            ['name' => 'Mariam Hovhannisyan', 'email' => 'mariam.hovhannisyan@mail.am', 'phone' => '+37491234505'],
            ['name' => 'Tigran Mkrtchyan',  'email' => 'tigran.mkrtchyan@mail.am',  'phone' => '+37491234506'],
            ['name' => 'Lusine Abrahamyan', 'email' => 'lusine.abrahamyan@mail.am', 'phone' => '+37491234507'],
            ['name' => 'Vardan Karapetyan', 'email' => 'vardan.karapetyan@mail.am', 'phone' => '+37491234508'],
            ['name' => 'Narine Ghazaryan',  'email' => 'narine.ghazaryan@mail.am',  'phone' => '+37491234509'],
            ['name' => 'Suren Avagyan',     'email' => 'suren.avagyan@mail.am',     'phone' => '+37491234510'],
            ['name' => 'Kristine Danielyan','email' => 'kristine.danielyan@mail.am','phone' => '+37491234511'],
            ['name' => 'Aram Stepanyan',    'email' => 'aram.stepanyan@mail.am',    'phone' => '+37491234512'],
            ['name' => 'Gohar Simonyan',    'email' => 'gohar.simonyan@mail.am',    'phone' => '+37491234513'],
            ['name' => 'Hayk Muradyan',     'email' => 'hayk.muradyan@mail.am',     'phone' => '+37491234514'],
            ['name' => 'Anahit Galstyan',   'email' => 'anahit.galstyan@mail.am',   'phone' => '+37491234515'],
        ];

        $nationalIds = $this->generateUniqueNationalIds(15);

        foreach ($users as $index => $data) {
            User::create([
                'name'        => $data['name'],
                'national_id' => $nationalIds[$index],
                'email'       => $data['email'],
                'phone'       => $data['phone'],
                'password'    => Hash::make('password'),
                'is_admin'    => false,
            ]);
        }
    }

    private function generateUniqueNationalIds(int $count): array
    {
        $ids = [];
        while (count($ids) < $count) {
            $id = str_pad(random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            if (!in_array($id, $ids)) {
                $ids[] = $id;
            }
        }
        return $ids;
    }
}
