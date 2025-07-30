<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareItem extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'version', 'license_details'];

    public function profiles()
    {
        return $this->belongsToMany(SoftwareProfile::class, 'profile_software_item');
    }
}