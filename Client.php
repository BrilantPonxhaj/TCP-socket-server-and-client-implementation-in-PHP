<?php

// Server IP and port
$host = = '192.168.100.140';
$port = 8080;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$result = socket_connect($socket, $host, $port);

if (!$result) {
       echo "Failed to connect to the server: " . socket_strerror(socket_last_error($socket)) . "\n";
       exit();
   }
   
   echo "Connected to the server at $host on port $port\n";
   echo "Type your command or 'exit' to close the connection.\n";


