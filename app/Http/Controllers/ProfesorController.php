<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use Illuminate\Http\Request;

class ProfesorController extends Controller
{
    // Array estático para almacenar profesores
    private static $profesores = [
        ['id' => 1, 'numeroEmpleado' => 'P001', 'nombres' => 'Carlos', 'apellidos' => 'Ramírez', 'horasClase' => 20],
        ['id' => 2, 'numeroEmpleado' => 'P002', 'nombres' => 'María', 'apellidos' => 'Fernández', 'horasClase' => 15],
        ['id' => 3, 'numeroEmpleado' => 'P003', 'nombres' => 'José', 'apellidos' => 'López', 'horasClase' => 10],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(self::$profesores);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $newProfesor = [
            'id' => count(self::$profesores) + 1,
            'numeroEmpleado' => $request->numeroEmpleado,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'horasClase' => $request->horasClase,
        ];

        self::$profesores[] = $newProfesor;

        return response()->json($newProfesor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $profesor = collect(self::$profesores)->firstWhere('id', $id);

        if (!$profesor) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        return response()->json($profesor);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Profesor $profesor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $index = collect(self::$profesores)->search(fn($p) => $p['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        self::$profesores[$index] = array_merge(self::$profesores[$index], [
            'numeroEmpleado' => $request->numeroEmpleado,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'horasClase' => $request->horasClase,
        ]);

        return response()->json(self::$profesores[$index]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $index = collect(self::$profesores)->search(fn($p) => $p['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        array_splice(self::$profesores, $index, 1);

        return response()->json(['message' => 'Profesor eliminado con éxito']);
    }
}
