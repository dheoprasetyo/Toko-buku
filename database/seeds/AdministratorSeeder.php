<?php

use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrator = new \App\User;
        $administrator->username = "administrator";
        $administrator->name = "Site Administrator";
        $administrator->email = "admin@gmail.com";
//         karena kita ingin menyimpan tipe
// array ke dalam database sebagai string / teks, ingat field roles di desain database kita bertipe teks untuk
// menyimpan array roles. Artinya setiap user nantinya bisa memiliki beberapa role baik ADMIN, STAFF atau
// CUSTOMER secara bersamaan dalam array.
        $administrator->roles = json_encode(["ADMIN"]);
        $administrator->password = \Hash::make("admin123");
        $administrator->avatar = "saat-ini-tidak-ada-file.png";
        $administrator->address = "Sarmili, Bintaro, Tangerang Selatan";
        $administrator->phone = "082226360005";
        $administrator->save();
        $this->command->info("User Admin berhasil diinsert");
    }
}
