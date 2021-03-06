<?php

namespace Okipa\LaravelBrickables\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Okipa\LaravelBrickables\Contracts\HasBrickables;
use Okipa\LaravelBrickables\Traits\HasBrickablesTrait;

class Page extends Model implements HasBrickables
{
    use HasBrickablesTrait;

    /** @var string */
    protected $table = 'pages';

    /** @var array */
    protected $fillable = ['slug'];
}
