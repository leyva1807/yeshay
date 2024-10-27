<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class TarjetasSeeder extends Seeder
{
   


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cuentas')->insert([
            [
                'numero_tarjeta' => '9204-1299-7055-2222',
                'numero_cuenta' => '0699-9121-9103-8810',
                'titular_id' => 1,
                'tipo_moneda' => 'CUP',
                'tipo_cuenta' => 'Ahorro',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 1825.2,
                'saldo_personal' => 500.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_tarjeta' => '9202-1299-7031-6810',
                'numero_cuenta' => '0699-9431-4706-7031',
                'titular_id' => 1,
                'tipo_moneda' => 'CUP',
                'tipo_cuenta' => 'Ahorro',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 440161.97,
                'saldo_personal' => 20000.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_tarjeta' => '9235-1299-7162-4411',
                'numero_cuenta' => '0699-9110-8463-7112',
                'titular_id' => 1,
                'tipo_moneda' => 'MLC',
                'tipo_cuenta' => 'Corriente',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 20.62,
                'saldo_personal' => 50.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_tarjeta' => '9227-0699-9722-5860',
                'numero_cuenta' => '0699-9430-7620-0332',
                'titular_id' => 4,
                'tipo_moneda' => 'CUP',
                'tipo_cuenta' => 'Ahorro',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 543.01,
                'saldo_personal' => 100.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_tarjeta' => '9204-0699-9665-0545',
                'numero_cuenta' => '0699-9111-5330-0714',
                'titular_id' => 5,
                'tipo_moneda' => 'CUP',
                'tipo_cuenta' => 'Corriente',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 3.79,
                'saldo_personal' => 5.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_tarjeta' => '9205-1299-7194-1290',
                'numero_cuenta' => '0699-9111-6205-0011',
                'titular_id' => 6,
                'tipo_moneda' => 'CUP',
                'tipo_cuenta' => 'Ahorro',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 372.6,
                'saldo_personal' => 50.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_tarjeta' => '9205-0699-2466-9741',
                'numero_cuenta' => '0699-9431-4481-8934',
                'titular_id' => 7,
                'tipo_moneda' => 'CUP',
                'tipo_cuenta' => 'Corriente',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 702.66,
                'saldo_personal' => 100.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_tarjeta' => '9235-0699-9502-7227',
                'numero_cuenta' => '0699-9431-4715-7334',
                'titular_id' => 8,
                'tipo_moneda' => 'USD',
                'tipo_cuenta' => 'Ahorro',
                'banco_asociado' => 'BPA',
                'saldo_empresa' => 1366.52,
                'saldo_personal' => 500.0,
                'estado' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}