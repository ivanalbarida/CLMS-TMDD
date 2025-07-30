<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareProfile extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function softwareItems()
    {
        return $this->belongsToMany(SoftwareItem::class, 'profile_software_item');
    }
}