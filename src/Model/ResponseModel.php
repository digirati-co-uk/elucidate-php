<?php


namespace Elucidate\Model;


interface ResponseModel
{
    public static function fromJson(string $json);
}
