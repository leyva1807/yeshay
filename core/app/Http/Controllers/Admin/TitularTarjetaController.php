<?php

namespace App\Http\Controllers;

use App\Models\TitularTarjeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TitularTarjetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $titulares = TitularTarjeta::with('cuentas')->paginate(10);
            return response()->json(['success' => true, 'data' => $titulares], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener la lista de titulares de tarjetas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al obtener la lista de titulares de tarjetas.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Este método no es necesario si se utiliza API, se usa para vistas.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:titular_tarjetas',
            'telefono' => 'required|string|max:20|unique:titular_tarjetas',
            'direccion' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $titularTarjeta = TitularTarjeta::create($request->only(['nombre', 'correo', 'telefono', 'direccion']));
            return response()->json(['success' => true, 'data' => $titularTarjeta], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear el titular de la tarjeta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el titular de la tarjeta.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TitularTarjeta $titularTarjeta)
    {
        try {
            $titularTarjeta->load('cuentas');
            return response()->json(['success' => true, 'data' => $titularTarjeta], 200);
        } catch (\Exception $e) {
            Log::error('Error al mostrar el titular de la tarjeta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al mostrar el titular de la tarjeta.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TitularTarjeta $titularTarjeta)
    {
        // Este método no es necesario si se utiliza API, se usa para vistas.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TitularTarjeta $titularTarjeta)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|string|email|max:255|unique:titular_tarjetas,correo,' . $titularTarjeta->id,
            'telefono' => 'sometimes|required|string|max:20|unique:titular_tarjetas,telefono,' . $titularTarjeta->id,
            'direccion' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $titularTarjeta->update($request->only(['nombre', 'correo', 'telefono', 'direccion']));
            return response()->json(['success' => true, 'data' => $titularTarjeta], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el titular de la tarjeta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el titular de la tarjeta.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TitularTarjeta $titularTarjeta)
    {
        try {
            $titularTarjeta->delete();
            return response()->json(['success' => true, 'message' => 'Titular de la tarjeta eliminado correctamente.'], 200);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el titular de la tarjeta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el titular de la tarjeta.'], 500);
        }
    }
}