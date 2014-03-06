<?php

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();

        User::create(array(
            'email'    => 'aherstein@gmail.com',
            'password' => Hash::make('nintendo')
        ));
    }

}