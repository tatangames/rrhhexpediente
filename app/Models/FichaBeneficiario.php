<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaBeneficiario extends Model
{
    use HasFactory;

    protected $table = 'ficha_beneficiario';
    public $timestamps = false;
}
