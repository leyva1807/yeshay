<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PropietariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('titular_tarjetas')->insert([
            [
                'nombre' => 'PAPITO',
                'telefono' => '+53 5 0318845',
                'direccion' => 'Dirección 2',
                'correo' => 'papito@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'MAITE',
                'telefono' => '+53 53407777',
                'direccion' => 'Dirección 2',
                'correo' => 'maite@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'ELIER',
                'telefono' => '+53 54277258',
                'direccion' => 'Dirección 3',
                'correo' => 'elier@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'HAYDE',
                'telefono' => '+53 54370075',
                'direccion' => 'Dirección 4',
                'correo' => 'hayde@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'YARI',
                'telefono' => '+53 56558089',
                'direccion' => 'Dirección 5',
                'correo' => 'yari@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'MAIKEL',
                'telefono' => '+53 58103165',
                'direccion' => 'Dirección 6',
                'correo' => 'maikel@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'YAMI',
                'telefono' => '+53 58104788',
                'direccion' => 'Dirección 7',
                'correo' => 'yami@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'AMAURIS',
                'telefono' => '+53 58162628',
                'direccion' => 'Dirección 8',
                'correo' => 'amauris@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'SEYLI',
                'telefono' => '+53 58353427',
                'direccion' => 'Dirección 9',
                'correo' => 'seyli@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'SERGIO',
                'telefono' => '+53 58605325',
                'direccion' => 'Dirección 10',
                'correo' => 'sergio@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'M ESTER',
                'telefono' => '+53 58818876',
                'direccion' => 'Dirección 11',
                'correo' => 'mester@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'JULIO',
                'telefono' => '+53 58929271',
                'direccion' => 'Dirección 12',
                'correo' => 'julio@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'ALEJANDRO',
                'telefono' => '+53 59348438',
                'direccion' => 'Dirección 13',
                'correo' => 'alejandro@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'ISMAY',
                'telefono' => '+53 59381636',
                'direccion' => 'Dirección 14',
                'correo' => 'ismay@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'MAMI',
                'telefono' => '+53 59381988',
                'direccion' => 'Dirección 15',
                'correo' => 'mami@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],         
        ]);
    }
}