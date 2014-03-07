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

        User::create(array(
            'email'    => 'dev@karmajobs.net',
            'password' => Hash::make('up vote down vote')
        ));
    }

}