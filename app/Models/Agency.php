<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'logo_path',
        'poea',
        'cr_no',
        'status',
        'owner_name',
        'contact_number',
        'created_by',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        $logoPath = $this->attributes['logo_path'] ?? null;

        if (empty($logoPath)) {
            return null;
        }

        // Replace the old simsoh.xyz URL with the new simsoh.site URL
        $oldUrl = 'https://simsoh.xyz/storage/agency/c0vnPUrmJ3LMV4CFUlrMxSBIGGWTsC3k4M44Q18W.png';
        $oldPath = 'agency/c0vnPUrmJ3LMV4CFUlrMxSBIGGWTsC3k4M44Q18W.png';
        $newUrl = 'https://simsoh.site/assets/images/logosim.png';

        // Check both full URL and relative storage path
        if ($logoPath === $oldUrl || $logoPath === $oldPath) {
            return $newUrl;
        }

        // If it's already a full URL, return as-is
        if (Str::startsWith($logoPath, ['http://', 'https://'])) {
            return $logoPath;
        }

        // Otherwise, treat as local storage path
        return Storage::url($logoPath);
    }

    public static function list()
    {
        return self::query()->select(['id', 'name'])->get()->toArray();
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function deprives()
    {
        return $this->hasMany(Deprive::class, 'agency_id', 'id');
    }
}
