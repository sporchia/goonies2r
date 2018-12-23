<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

/**
 * model of stored game that has been randomized.
 */
class Game extends Model
{
    /** @var array */
    protected $fillable = [
        'bps',
        'spoiler',
    ];
    /** @var array */
    protected $attributes = [
        'spoiler' => '[]',
    ];


    /**
     * {@inheritdoc}
     */
    public static function boot()
    {
        parent::boot();

        $hasher = new Hashids(env('HASH_IDS', 'vt'), 10);

        // This causes a 2x save, but it's small, so we are okay with it.
        static::created(function ($game) use ($hasher) {
            $game->hash = $hasher->encode($game->id);
            $game->save();
        });
    }

    /**
     * mutate Spoiler from json string to array.
     *
     * @return array
     */
    public function getSpoilerAttribute() : array
    {
        return json_decode($this->attributes['spoiler'], true);
    }

    /**
     * mutate Spoiler from array to json string.
     *
     * @param array $spoiler data being set
     *
     * @return void
     */
    public function setSpoilerAttribute(array $spoiler) : void
    {
        $this->attributes['spoiler'] = json_encode($spoiler);
    }
}
