<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => bcrypt('test1234')
        ]);
    }
}
