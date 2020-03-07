<?php

foreach ($_REQUEST as $key => $value) {
    $container->setParameter($key, $value);
}