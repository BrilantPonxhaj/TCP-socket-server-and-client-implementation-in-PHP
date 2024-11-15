<?php

// Server IP and port
$host = '172.16.105.53';
$port = 8080;

// Create socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket);

echo "Server is listening on IP $host and port $port\n";

$knownClients = [
    ['ip' => '172.16.105.53', 'writePermission' => true, 'executePermission' => true],
    ['ip' => '192.168.100.140', 'writePermission' => false, 'executePermission' => true, 'readPermission' => false],
];

$blockList = ['172.20.10.10'];

while (true) {
    $clientSocket = socket_accept($socket);
    socket_getpeername($clientSocket, $clientIP);

    if (in_array($clientIP, $blockList)) {
        socket_write($clientSocket, "You are blocked\n");
        socket_close($clientSocket);
        continue;
    }

    echo "Client connected: $clientIP\n";
    $alias = resolveClient($clientIP);

    while ($data = socket_read($clientSocket, 1024)) {
        $data = trim($data);
        echo "Received data from $alias: $data\n";

        if ($data == "exit") {
            socket_write($clientSocket, "Press [ENTER] to close the connection!\n");
            break;
        }

        if (str_starts_with($data, "/read")) {
            if (!checkPermission($clientIP, 'readPermission')) {
                socket_write($clientSocket, "You don't have permission to read\n");
                continue;
            }
            $fileName = explode(" ", $data)[1];
            readFileContent($fileName, $clientSocket);
        } elseif (str_starts_with($data, "/write")) {
            if (!checkPermission($clientIP, 'writePermission')) {
                socket_write($clientSocket, "You don't have permission to write\n");
                continue;
            }
            $parts = explode(" ", $data, 3);
            $fileName = $parts[1];
            $content = isset($parts[2]) ? $parts[2] : "";
            writeToFile($fileName, $content, $clientSocket);
        } elseif (str_starts_with($data, "/list")) {
            listFiles($clientSocket);
        } elseif (str_starts_with($data, "/exec")) {
            if (!checkPermission($clientIP, 'executePermission')) {
                socket_write($clientSocket, "You don't have permission to execute\n");
                continue;
            }
            $parts = explode(" ", $data, 3);
            $fileName = $parts[1];
            $action = $parts[2] ?? null;
            handleExec($fileName, $action, $clientSocket);
        } elseif ($data == "/help") {
            showHelp($clientSocket);
        } else {
            socket_write($clientSocket, "Unknown command\n");
        }
    }

    echo "Connection closed by $alias\n";
    socket_close($clientSocket);
}

// Functions

function resolveClient($ip) {
    global $knownClients;
    foreach ($knownClients as $client) {
        if ($client['ip'] === $ip) {
            return $client['ip'];
        }
    }
    return "Unknown Client";
}

function checkPermission($ip, $action) {
    global $knownClients;
    foreach ($knownClients as $client) {
        if ($client['ip'] === $ip && !empty($client[$action])) {
            return true;
        }
    }
    return false;
}

function readFileContent($fileName, $socket) {
    $filePath = "./files/$fileName";
    if (!file_exists($filePath)) {
        socket_write($socket, "File not found\n");
        return;
    }
    $content = file_get_contents($filePath);
    socket_write($socket, $content);
}

function writeToFile($fileName, $content, $socket) {
    $filePath = "./files/$fileName";
    if (!file_exists($filePath)) {
        socket_write($socket, "File not found\n");
        return;
    }
    file_put_contents($filePath, $content, FILE_APPEND);
    socket_write($socket, "File content updated!\n");
}

function listFiles($socket) {
    $files = scandir("./files");
    if ($files === false) {
        socket_write($socket, "Cannot access files on server\n");
        return;
    }
    $fileList = implode("\n", array_diff($files, ['.', '..']));
    socket_write($socket, $fileList ? $fileList : "No files exist on the server\n");
}

function showHelp($socket) {
    $helpMessage = "\n" .
        "Type /read [file name.txt] -> To read from a file!\n" .
        "Type /write [file name.txt] [content] -> To write in a file!\n" .
        "Type /exec [file name.txt] [action] -> (Actions: new - create new file, del - delete file, run - open a file)!\n" .
        "Type exit to close the connection with server!\n" .
        "Type /list to list the files on server!\n";
    socket_write($socket, $helpMessage);
}