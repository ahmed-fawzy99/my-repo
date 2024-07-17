<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
        $root2->contacts()->attach($root);

        Contact::where('user_id', $root->id)->where('contact_id', $root2->id)->update(['contact_accepted' => true]);
        Contact::where('user_id', $root2->id)->where('contact_id', $root->id)->update(['contact_accepted' => true]);

        $i = 1;
        foreach (User::all() as $user) {
            if ($user->id === $root->id || $user->id === $root2->id) {
                continue;
            }
            $root->contacts()->attach($user);
//            $user->contacts()->attach($root);
            $i = $i + 1;
            if ($i > 3) {
                break;
            }
        }

        $convo = Conversation::factory()->create([
            'user_1' => $root->id,
            'user_2' => $root2->id,
        ]);
        $convo2 = Conversation::factory()->create([
            'user_1' => $root2->id,
            'user_2' => $root->id,
        ]);


    }
}
