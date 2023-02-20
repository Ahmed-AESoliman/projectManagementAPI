<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'full_name' => 'admin',
            'email' => 'admin@demo.com',
            'role' => 'admin',
            'active' => true,
            'password' => Hash::make('123456'),
            'email_verified_at' => Carbon::now(),
            'is_mangement_team' => true,
        ]);
    }
}
