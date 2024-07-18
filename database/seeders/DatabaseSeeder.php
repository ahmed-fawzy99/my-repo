<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
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
            'public_key' => '1e252f694f3a2840d9c9cadd4059c5f87359cfcb68fe45c39ad242e1ebb55728',
        ]);
        $root2 = User::factory()->create([
            'name' => 'Normal Root',
            'email' => 'normal@root.com',
            'public_key' => '1fdc00478c5562bef15e75da7343f5d0efc88a0da23028df7f05e506a609454d',
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
            Contact::where('user_id', $root->id)->where('contact_id', $user->id)->update(['contact_accepted' => true]);

            $user->contacts()->attach($root);
            Contact::where('user_id', $user->id)->where('contact_id', $root->id)->update(['contact_accepted' => true]);

            Conversation::create([
                'user_1' => $root->id,
                'user_2' => $user->id,
            ]);
            $i = $i + 1;
            if ($i > 3) {
                break;
            }
        }

        $convo = Conversation::factory()->create([
            'user_1' => $root->id,
            'user_2' => $root2->id,
        ]);
        Message::create([
            'conversation_id' => $convo->id,
            'sender_id' => $root->id,
            'content' => 'Hello World',
            'signature' => '0DFCFC18188978B11AD410BD8C8DA062A8C8DCF67D1EE418D4EC6E8C2EB59B4D723C5723F91CC4AA691D55F03A57E131A4232C11CEAB04BE6014779087796700',
        ]);
        Message::create([
            'conversation_id' => $convo->id,
            'sender_id' => $root2->id,
            'content' => 'NOOOOO ',
            'signature' => '0DFCFC18188978B11AD410BD8C8DA062A8C8DCF67D1EE418D4EC6E8C2EB59B4D723C5723F91CC4AA691D55F03A57E131A4232C11CEAB04BE6014779087796700',
        ]);
    }
}
