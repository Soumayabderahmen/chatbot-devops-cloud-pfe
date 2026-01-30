<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;
    protected $fillable = ['name']; // ajoute d'autres colonnes si besoin

    public function startups()
    {
        return $this->hasMany(Startup::class);
    }
}

