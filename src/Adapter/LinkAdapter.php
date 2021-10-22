<?php

namespace Invertus\dpdBaltics\Adapter;

class LinkAdapter
{
    public function getUrlSmarty($params)
    {
        return \Link::getUrlSmarty($params);
    }
}
