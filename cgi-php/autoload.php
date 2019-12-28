<?php

spl_autoload_register(function($class) {
    require __DIR__ . '/' . str_replace(
                    '\\', '/', explode('\\', $class)[0] === 'Run' ? (
                            substr($class, 4)
                            ) : 'tech/libs/' . $class
            ) . '.php';
}, true);
