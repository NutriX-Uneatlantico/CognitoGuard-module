<?php

namespace Modules\CognitoGuard\Entities;

use Illuminate\Database\Eloquent\Model;

class CognitoUser extends Model
{
    protected $fillable = [
        'username'
    ];

    public function getAuthIdentifier()
    {
        return $this->username;
    }
}
