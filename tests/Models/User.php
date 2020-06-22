<?php

namespace PWRDK\CustomAttributes\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use PWRDK\CustomAttributes\HasCustomAttributes;

class User extends Model
{
    use HasCustomAttributes;
}
