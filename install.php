<?php
$dir = __DIR__ . '/../../avatec/opencart/';

copy(
    __DIR__ . '/admin/controller/extension/shipping/paczkawruchu.php',
    $dir . 'admin/controller/extension/shipping/paczkawruchu.php'
);

copy(
    __DIR__ . '/admin/language/pl-PL/extension/shipping/paczkawruchu.php',
    $dir . 'admin/language/pl-PL/extension/shipping/paczkawruchu.php'
);

copy(
    __DIR__ . '/admin/view/template/extension/shipping/paczkawruchu.twig',
    $dir . 'admin/view/template/extension/shipping/paczkawruchu.twig'
);

copy(
    __DIR__ . '/catalog/language/pl-PL/extension/shipping/paczkawruchu.php',
    $dir . 'catalog/language/pl-PL/extension/shipping/paczkawruchu.php'
);

copy(
    __DIR__ . '/catalog/model/extension/shipping/paczkawruchu.php',
    $dir . 'catalog/model/extension/shipping/paczkawruchu.php'
);
