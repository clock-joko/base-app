<?php

use Illuminate\Database\Seeder;
use Clock\Baserepo\Test\DataSources\TUsers\TUser;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        factory(TUser::class)->create();
    }
}
