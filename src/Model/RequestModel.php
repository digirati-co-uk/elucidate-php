<?php

namespace Elucidate\Model;

use JsonSerializable;

interface RequestModel extends JsonSerializable
{
    public function getHeaders(): array;
}
