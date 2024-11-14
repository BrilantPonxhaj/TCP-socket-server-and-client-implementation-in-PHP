<?php

$host = = '192.168.100.140';
$port = 8080;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$result = socket_connect($socket, $host, $port);


