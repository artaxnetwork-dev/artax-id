<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OAuthClient extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'oauth_clients';

    protected $fillable = [
        'name',
        'client_id',
        'client_secret_hash',
        'allowed_scopes',
    ];

    protected $casts = [
        'allowed_scopes' => 'array',
    ];
}