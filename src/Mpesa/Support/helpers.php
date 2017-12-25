<?php

use Kabangi\Mpesa\Repositories\EndpointsRepository;

function mpesa_endpoint($endpoint)
{
    return EndpointsRepository::build($endpoint);
}
