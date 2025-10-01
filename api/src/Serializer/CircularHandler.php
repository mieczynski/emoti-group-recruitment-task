<?php
declare(strict_types=1);

namespace App\Serializer;

final class CircularHandler
{
    public static function handle(object $obj): mixed
    {
        return method_exists($obj, 'getId') ? (string)$obj->getId() : spl_object_id($obj);
    }
}
