<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Sector;
class Startup extends Model
{
    use HasFactory;

    protected $table = 'startup';

    protected $fillable = [
        'user_id',
        'logo_startup',
        'phone_number',
        'website_link',
        'description',
        'sector_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sector()
{
    return $this->belongsTo(Sector::class);
}
}
