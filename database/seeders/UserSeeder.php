<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $type = ['admin', 'user', 'super_admin'];

        // User::create([
        //     'name' => "To-heeb",
        //     'first_name' => 'Toheeb',
        //     'last_name' => 'Oyekola',
        //     'email' => 'toheeb.olawale.to23@gmail.com',
        //     'password' =>  Hash::make(env('APP_PASS')),
        //     'library_id' => rand(1, 15),
        //     'phone_number' => env('APP_PHONE'),
        //     'role' => $type[rand(0, 2)],
        //     'email_verified_at' => now(),
        //     'remember_token' => Str::random(10),
        // ]);

        User::factory(10)->create();
    }
}
