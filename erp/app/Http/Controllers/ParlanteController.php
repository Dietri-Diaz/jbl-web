<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParlanteRequest;
use App\Models\Parlante;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ParlanteController extends Controller
{
    /** Carpeta (dentro de public/) donde se guardan las fotos. */
    private const CARPETA_FOTOS = 'uploads/parlantes';

    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todos');

        $parlantes = Parlante::query()
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('tipo', 'like', "%{$buscar}%")))
            ->when($estado === 'publicados', fn ($c) => $c->where('visible', true))
            ->when($estado === 'ocultos', fn ($c) => $c->where('visible', false))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('erp.parlantes.index', compact('parlantes', 'buscar', 'estado'));
    }

    public function create()
    {
        return view('erp.parlantes.crear', [
            'parlante' => new Parlante(['visible' => true, 'orden' => Parlante::max('orden') + 1]),
            'resistencias' => ParlanteRequest::RESISTENCIAS,
        ]);
    }

    public function store(ParlanteRequest $request)
    {
        $datos = $request->safe()->except('foto');
        $datos['slug'] = $this->slugUnico($datos['nombre']);
        $datos['visible'] = $request->boolean('visible');
        $datos['orden'] ??= Parlante::max('orden') + 1;
        $datos['foto'] = $this->guardarFoto($request->file('foto'), $datos['slug']);

        $parlante = Parlante::create($datos);

        return redirect()->route('parlantes.index')
            ->with('ok', "Se agregó el {$parlante->nombre} al catálogo.");
    }

    public function edit(Parlante $parlante)
    {
        return view('erp.parlantes.editar', [
            'parlante' => $parlante,
            'resistencias' => ParlanteRequest::RESISTENCIAS,
        ]);
    }

    public function update(ParlanteRequest $request, Parlante $parlante)
    {
        $datos = $request->safe()->except('foto');
        $datos['visible'] = $request->boolean('visible');
        $datos['orden'] ??= $parlante->orden;

        if ($datos['nombre'] !== $parlante->nombre) {
            $datos['slug'] = $this->slugUnico($datos['nombre'], $parlante->id);
        }

        if ($request->hasFile('foto')) {
            $this->borrarFoto($parlante->foto);
            $datos['foto'] = $this->guardarFoto($request->file('foto'), $datos['slug'] ?? $parlante->slug);
        }

        $parlante->update($datos);

        return redirect()->route('parlantes.index')
            ->with('ok', "Se guardaron los cambios del {$parlante->nombre}.");
    }

    public function destroy(Parlante $parlante)
    {
        $this->borrarFoto($parlante->foto);
        $parlante->delete();

        return redirect()->route('parlantes.index')
            ->with('ok', "Se eliminó el {$parlante->nombre} del catálogo.");
    }

    /** Publica u oculta el parlante en la landing sin entrar a editarlo. */
    public function alternarVisible(Parlante $parlante)
    {
        $parlante->update(['visible' => ! $parlante->visible]);

        return back()->with('ok', $parlante->visible
            ? "El {$parlante->nombre} ya se muestra en la landing."
            : "El {$parlante->nombre} quedó oculto en la landing.");
    }

    /** "JBL Flip 3" -> "jbl-flip-3"; si ya existe, "jbl-flip-3-2". */
    private function slugUnico(string $nombre, ?int $ignorarId = null): string
    {
        $base = Str::slug($nombre) ?: 'parlante';
        $slug = $base;
        $n = 2;

        while (Parlante::where('slug', $slug)->when($ignorarId, fn ($c) => $c->where('id', '!=', $ignorarId))->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }

    private function guardarFoto(UploadedFile $foto, string $slug): string
    {
        $nombre = $slug.'-'.now()->format('YmdHis').'.'.$foto->extension();
        $foto->move(public_path(self::CARPETA_FOTOS), $nombre);

        return self::CARPETA_FOTOS.'/'.$nombre;
    }

    private function borrarFoto(?string $ruta): void
    {
        // Solo se borran archivos de la carpeta de fotos, nunca otra ruta.
        if ($ruta && str_starts_with($ruta, self::CARPETA_FOTOS.'/')) {
            File::delete(public_path($ruta));
        }
    }
}
