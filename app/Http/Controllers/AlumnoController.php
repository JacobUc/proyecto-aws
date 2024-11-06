<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AlumnoController extends Controller
{
    private static $cacheAlumnosKey = 'alumnos';

    public function __construct() {
        // Si no hay alumnos en cache, inicializamos con algunos datos de prueba
        if (!Cache::has(self::$cacheAlumnosKey)) {
            Cache::put(self::$cacheAlumnosKey, [
                ['id' => 1, 'nombres' => 'Jacob Jesus', 'apellidos' => 'Uc Cob', 'matricula' => '17000923', 'promedio' => 9.5],
                ['id' => 2, 'nombres' => 'Eduardo', 'apellidos' => 'Gómez', 'matricula' => '17000923', 'promedio' => 8.7],
                ['id' => 3, 'nombres' => 'Luis', 'apellidos' => 'Martínez', 'matricula' => '17000923', 'promedio' => 9.1],
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Cache::get(self::$cacheAlumnosKey));
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
            'matricula' => 'required|string',
            'promedio' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $newAlumno = [
            'id' => $request->id,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'matricula' => $request->matricula,
            'promedio' => $request->promedio,
        ];

        // Obtener alumnos actuales y agregar el nuevo
        $alumnos = Cache::get(self::$cacheAlumnosKey);
        $alumnos[] = $newAlumno;

        // Actualizar cache con el nuevo array de alumnos
        Cache::put(self::$cacheAlumnosKey, $alumnos);

        return response()->json($newAlumno, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show( $id )
    {
        $alumnos = Cache::get(self::$cacheAlumnosKey);
        $alumno = collect($alumnos)->firstWhere('id', $id);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        return response()->json($alumno);
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
            'matricula' => 'required|string',
            'promedio' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors()
            ], 400);
        }

        $alumnos = Cache::get(self::$cacheAlumnosKey);
        $index = collect($alumnos)->search(fn($a) => $a['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        // Actualiza los datos del alumno en el array
        $alumnos[$index] = array_merge($alumnos[$index], [
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'matricula' => $request->matricula,
            'promedio' => $request->promedio,
        ]);

        // Guardar el array actualizado en cache
        Cache::put(self::$cacheAlumnosKey, $alumnos);

        return response()->json($alumnos[$index]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $alumnos = Cache::get(self::$cacheAlumnosKey);
        $index = collect($alumnos)->search(fn($a) => $a['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        // Eliminar el alumno y actualizar el cache
        array_splice($alumnos, $index, 1);
        Cache::put(self::$cacheAlumnosKey, $alumnos);

        return response()->json(['message' => 'Alumno eliminado con éxito']);
    }
}
