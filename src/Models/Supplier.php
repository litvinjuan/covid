<?php

namespace Store\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Parental\HasParent;

class Supplier extends User
{
    use HasParent;
}
