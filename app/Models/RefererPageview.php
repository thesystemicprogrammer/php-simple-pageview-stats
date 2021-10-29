<?php

namespace App\Models;

use Database\Factories\RefererPageviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefererPageview extends Model
{
    use HasFactory;

    /**
     * Don't use create and modified timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uri', 'timestamp', 'referer_hash'
    ];

    protected static function newFactory()
    {
        return RefererPageviewFactory::new();
    }
}
