<?php

require 'vendor/autoload.php';
$ref = new ReflectionClass('Filament\Resources\RelationManagers\RelationManager');
echo 'form static: '.($ref->getMethod('form')->isStatic() ? 'yes' : 'no')."\n";
echo 'table static: '.($ref->getMethod('table')->isStatic() ? 'yes' : 'no')."\n";
