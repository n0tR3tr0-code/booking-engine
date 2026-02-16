<?php

namespace App\Traits;

trait Lockable
{
    protected static function bootLockable()
    {
        // la funzione aumenta solo se il modello ha subito modifiche
        static::updating(function ($model){
            if ($model->isDirty()) {
            $model->version++;
            }
        });
    }
}