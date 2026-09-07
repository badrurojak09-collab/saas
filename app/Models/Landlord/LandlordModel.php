<?php

namespace App\Models\Landlord;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Model;

abstract class LandlordModel extends Model
{
    use HasUuidV7;

    protected $connection = 'landlord';
}
