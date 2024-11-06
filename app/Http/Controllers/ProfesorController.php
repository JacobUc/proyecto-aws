<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ProfesorController extends Controller
{
    private static $cacheKey = 'profesores';

    public function __construct()
    {
        // Si no hay profesores en cache, inicializamos con algunos datos de prueba
        if (!Cache::has(self::$cacheKey)) {
            Cache::put(self::$cacheKey, [
                ['id' => 1, 'nombres' => 'Eduardo', 'apellidos' => 'Rodríguez', 'numeroEmpleado' => 1, 'horasClase' => 20],
                ['id' => 2, 'nombres' => 'María', 'apellidos' => 'Fernández', 'numeroEmpleado' => 2, 'horasClase' => 15],
                ['id' => 3, 'nombres' => 'José', 'apellidos' => 'López', 'numeroEmpleado' => 3, 'horasClase' => 10],
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Cache::get(self::$cacheKey));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'numeroEmpleado' => 'required|numeric',
            'horasClase' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors()
            ], 400);
        }

        $newProfesor = [
            'id' => $request->id,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'numeroEmpleado' => $request->numeroEmpleado,
            'horasClase' => $request->horasClase,
        ];

        // Obtener profesores actuales y agregar el nuevo
        $profesores = Cache::get(self::$cacheKey);
        $profesores[] = $newProfesor;

        // Actualizar cache con el nuevo array de profesores
        Cache::put(self::$cacheKey, $profesores);

        return response()->json($newProfesor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $profesores = Cache::get(self::$cacheKey);
        $profesor = collect($profesores)->firstWhere('id', $id);

        if (!$profesor) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        return response()->json($profesor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'numeroEmpleado' => 'required|numeric',
            'horasClase' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors()
            ], 400);
        }

        $profesores = Cache::get(self::$cacheKey);
        $index = collect($profesores)->search(fn($p) => $p['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        // Actualizar los datos del profesor
        $profesores[$index] = array_merge($profesores[$index], [
            'numeroEmpleado' => $request->numeroEmpleado,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'horasClase' => $request->horasClase,
        ]);

        // Guardar el array actualizado en cache
        Cache::put(self::$cacheKey, $profesores);

        return response()->json($profesores[$index]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $profesores = Cache::get(self::$cacheKey);
        $index = collect($profesores)->search(fn($p) => $p['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        // Eliminar el profesor y actualizar el cache
        array_splice($profesores, $index, 1);
        Cache::put(self::$cacheKey, $profesores);

        return response()->json(['message' => 'Profesor eliminado con éxito']);
    }
}
