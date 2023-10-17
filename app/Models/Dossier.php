<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Dossier extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
  


    protected $table='dossiers';
    protected $guarded =[];
    protected $fillable = ['perso_id', 'email', 'description'];
}
