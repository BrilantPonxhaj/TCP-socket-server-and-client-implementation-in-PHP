<?php

$knownClients = [
       [
           'id' => 1,
           'ip' => '192.168.100.161',
           'name' => 'Brikenda',
           'readPermission' => true,
           'writePermission' => true,
           'executePermission' => true,
       ],
       [
           'id' => 2,
           'ip' => '192.168.100.106',
           'name' => 'Brineta',
           'readPermission' => true,
           'writePermission' => true,
           'executePermission' => true,
       ],
       [
           'id' => 3,
           'ip' => '',
           'name' => 'Brilant Ponxhaj',
           'readPermission' => true,
           'writePermission' => true,
           'executePermission' => false,
       ],
       [
           'id' => 4,
           'ip' => '',
           'name' => 'Çlirimtar',
           'readPermission' => true,
           'writePermission' => true,
           'executePermission' => false,
           
       ],
   ];

    function getClientName($ip) {
        global $knownClients;

        foreach ($knownClients as $client) {
            if ($client['ip'] === $ip) {
                return $client['name'];
            }
        }

        // Return IP if no matching client is found
        return false;
    } 