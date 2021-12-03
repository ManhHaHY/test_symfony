<?php

exec(sprintf(
    'php "%s/../bin/console" cache:clear --env=test --no-warmup',
    __DIR__
));

require __DIR__ . '/../vendor/autoload.php';
