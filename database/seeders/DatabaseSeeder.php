<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


         User::factory(10)->create();
        $root = User::factory()->create([
            'name' => 'Super Root',
            'email' => 'super@root.com',
        ]);
        $root2 = User::factory()->create([
            'name' => 'Normal Root',
            'email' => 'normal@root.com',
        ]);
        $root->contacts()->attach($root2);

        Contact::where('user_id', $root->id)->where('contact_id', $root2->id)->update(['contact_accepted' => true]);
        $root->contacts()->attach(User::all()->random(3));
    }
}
