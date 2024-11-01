<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    private static $alumnos = [
        ['id' => 1, 'nombres' => 'Jacob Jesus', 'apellidos' => 'Uc Cob', 'matricula' => '17000923', 'promedio' => 9.5],
        ['id' => 2, 'nombres' => 'Eduardo', 'apellidos' => 'Gómez', 'matricula' => '17000923', 'promedio' => 8.7],
        ['id' => 3, 'nombres' => 'Luis', 'apellidos' => 'Martínez', 'matricula' => '17000923', 'promedio' => 9.1],
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json( self::$alumnos, 200 );
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
        $newAlumno = [
            'id' => count( self::$alumnos) + 1,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'matricula' => $request->matricula,
            'promedio' => $request->promedio,
        ];

        self::$alumnos[] = $newAlumno; // Agrega el nuevo alumno al array

        return response()->json($newAlumno, 201); // Retorna el alumno creado con código 201
    }

    /**
     * Display the specified resource.
     */
    public function show( $id )
    {
        $alumno = collect(self::$alumnos)->firstWhere('id', $id);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        return response()->json($alumno);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alumno $alumno)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $index = collect(self::$alumnos)->search(fn($al) => $al['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        // Actualiza los datos del alumno en el array
        self::$alumnos[$index] = array_merge(self::$alumnos[$index], [
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'matricula' => $request->matricula,
            'promedio' => $request->promedio,
        ]);

        return response()->json(self::$alumnos[$index]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $index = collect(self::$alumnos)->search(fn($al) => $al['id'] === (int) $id);

        if ($index === false) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        // Elimina el alumno del array
        array_splice(self::$alumnos, $index, 1);

        return response()->json(['message' => 'Alumno eliminado con éxito']);
    }
}
