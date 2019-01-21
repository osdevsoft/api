<?php

use Illuminate\Database\Seeder;
use Osds\CRM\Domain\Models\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@mymaindomain.es';
        $user->password = bcrypt('masterNexinPasswordJust1NC#se');
        $user->save();

        $user = new User();
        $user->name = 'Webmaster';
        $user->email = 'webmaster@mymaindomain.es';
        $user->password = bcrypt('userpassword');
        $user->save();

    }
}