<?php

declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use XivApi\XivApi;

return new XivApi(new Client, new HttpFactory);
