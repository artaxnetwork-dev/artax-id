<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OAuthToken extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'oauth_tokens';

    protected $fillable = [
        'client_id',
        'subject_id',
        'scopes',
        'jwt',
        'expires_at',
        'revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];
}