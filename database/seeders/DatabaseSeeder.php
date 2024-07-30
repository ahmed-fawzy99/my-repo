<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Globals;
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

        Globals::create([
            'max_file_size' => 5242880,
            'max_file_count' => 10,
        ]);

        User::factory()->create([
            'name' => 'Supreme Root',
            'email' => 'supreme@root.com',
            'password' => bcrypt('HelloKittyImSoPretty'),
        ]);
    }
}
