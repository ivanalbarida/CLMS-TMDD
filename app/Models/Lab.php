<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
        use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lab_name',
        'building_name',
    ];

    /**
     * Get all of the equipment for the Lab.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
