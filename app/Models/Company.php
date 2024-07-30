<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'cnpj_cpf',
        'corporate_name',
        'fantasy_name',
        'email',
        'phone_number',
        'public_place',
        'home_number',
        'complement',
        'district',
        'zip_code',
        'county',
        'uf',
    ];

    protected static function booted()
    {
        $user = auth('web')->user();

        static::addGlobalScope('linked_user', function (Builder $builder) use ($user) {
            if ($user->is_admin == "N") {
                $builder->whereRelation('users', 'user_id', $user->id);
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_company');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'cnpj_cpf', 'cnpj_cpf');
    }
}
