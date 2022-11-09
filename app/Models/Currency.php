<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string code
 * @property string name
 */
class Currency extends Model
{
    use HasFactory;
}
