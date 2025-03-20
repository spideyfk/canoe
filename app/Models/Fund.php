<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_year',
        'manager_id',
    ];

    public function aliases()
    {
        return $this->hasMany(Alias::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_fund');
    }

    public function manager()
    {
        return $this->belongsTo(FundManager::class, 'manager_id');
    }
}
