<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitularTarjeta extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar de manera masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'direccion',
    ];

    /**
     * Relación con otras tablas (si corresponde).
     *
     * Puedes definir aquí las relaciones que tenga TitularTarjeta con otras tablas.
     * Ejemplos:
     *  - Una relación de uno a muchos con "Cuentas" si cada titular puede tener varias cuentas.
     */
    public function cuentas()
    {
        return $this->hasMany(Cuenta::class, 'propietario_id');
    }

    /**
     * Búsqueda por nombre de titular.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $nombre
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarPorNombre($query, $nombre)
    {
        return $query->where('nombre', 'LIKE', "%$nombre%");
    }

    /**
     * Búsqueda por correo del titular.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $correo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarPorCorreo($query, $correo)
    {
        return $query->where('correo', $correo);
    }

    /**
     * Obtiene el nombre del titular en mayúsculas.
     *
     * @return string
     */
    public function getNombreEnMayusculasAttribute()
    {
        return strtoupper($this->nombre);
    }
}
