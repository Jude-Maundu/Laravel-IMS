<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CrewSeeder extends Seeder
{
    public function run(): void
    {
        $crew = [
            ['name' => 'James Kamau', 'email' => 'j.kamau@greyapple.co.ke'],
            ['name' => 'Mary Wanjiku', 'email' => 'm.wanjiku@greyapple.co.ke'],
            ['name' => 'Peter Ochieng', 'email' => 'p.ochieng@greyapple.co.ke'],
            ['name' => 'Grace Akinyi', 'email' => 'g.akinyi@greyapple.co.ke'],
            ['name' => 'David Mwangi', 'email' => 'd.mwangi@greyapple.co.ke'],
            ['name' => 'Sarah Njeri', 'email' => 's.njeri@greyapple.co.ke'],
            ['name' => 'John Otieno', 'email' => 'j.otieno@greyapple.co.ke'],
            ['name' => 'Lucy Wambui', 'email' => 'l.wambui@greyapple.co.ke'],
            ['name' => 'Michael Kipchoge', 'email' => 'm.kipchoge@greyapple.co.ke'],
            ['name' => 'Anne Chebet', 'email' => 'a.chebet@greyapple.co.ke'],
            ['name' => 'Daniel Mutua', 'email' => 'd.mutua@greyapple.co.ke'],
            ['name' => 'Elizabeth Nyambura', 'email' => 'e.nyambura@greyapple.co.ke'],
            ['name' => 'Patrick Korir', 'email' => 'p.korir@greyapple.co.ke'],
            ['name' => 'Jane Wangari', 'email' => 'j.wangari@greyapple.co.ke'],
            ['name' => 'Francis Kibet', 'email' => 'f.kibet@greyapple.co.ke'],
            ['name' => 'Rose Wanjiru', 'email' => 'r.wanjiru@greyapple.co.ke'],
            ['name' => 'Samuel Omondi', 'email' => 's.omondi@greyapple.co.ke'],
            ['name' => 'Catherine Muthoni', 'email' => 'c.muthoni@greyapple.co.ke'],
            ['name' => 'Joseph Karanja', 'email' => 'j.karanja@greyapple.co.ke'],
            ['name' => 'Rebecca Njoki', 'email' => 'r.njoki@greyapple.co.ke'],
        ];

        foreach ($crew as $member) {
            User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make('password'),
                ]
            );
        }

        $this->command->info('20 crew members seeded successfully.');
    }
}
