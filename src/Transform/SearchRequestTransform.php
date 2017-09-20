<?php

namespace Elucidate\Transform;

use Elucidate\Search\ServiceQuery;
use Psr\Http\Message\ServerRequestInterface;

class SearchRequestTransform extends AbstractRequestTransform
{
    public function transform(ServerRequestInterface $request)
    {
        $query = $request->getQueryParams();
        $path = $request->getUri()->getPath();

        if (isset($query['fields'])) {
            $query['fields'] = is_array($query['fields']) ? implode($query['fields'], ',') : $query['fields'];
        }
        if (substr($path, 0, 1) === '/') {
            $path = substr($path, 1);
        }

        return new ServiceQuery($path, $query);
    }
}
