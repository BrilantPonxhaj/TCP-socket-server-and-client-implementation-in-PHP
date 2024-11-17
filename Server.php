<?php
include 'ClientsWithPermissions.php';
// Server IP and port
$host = '192.168.100.166';
$port = 8081;

// Create socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket!");
socket_bind($socket, $host, $port);
socket_listen($socket);

echo "Server is listening on IP $host and port $port\n";

$blockList = ['172.20.10.10'];
$clients = [];

while (true) {

    $readSockets = array_merge([$socket], $clients);
    $writeSockets = null;
    $exceptSockets = null;

    if (socket_select($readSockets, $writeSockets, $exceptSockets, null) === false) {
        echo "Error with socket_select\n";
        break;
    }

    if (in_array($socket, $readSockets)) {
        $clientSocket = socket_accept($socket);
        socket_getpeername($clientSocket, $clientIP);

        if (in_array($clientIP, $blockList)) {
            socket_write($clientSocket, "You are blocked\n");
            socket_close($clientSocket);
        } else {
            $clients[] = $clientSocket;
            echo "Client connected: $clientIP\n";
        }
    }

    // Handle data from existing client connections
    foreach ($clients as $key => $clientSocket) {
        if (in_array($clientSocket, $readSockets)) {
            $data = socket_read($clientSocket, 1024);
            if ($data === false) {

                socket_getpeername($clientSocket, $clientIP);
                unset($clients[$key]);
                socket_close($clientSocket);

                foreach ($clients as $otherClient) {
                    socket_write($otherClient, "$clientIP has disconnected.\n");
                }

                echo "Client $clientIP disconnected\n";
                continue;
            }

            $data = trim($data);
            if ($data == "exit") {
                socket_write($clientSocket, "Press [ENTER] to close the connection!\n");
                unset($clients[$key]);
                socket_close($clientSocket);

                foreach ($clients as $otherClient) {
                    socket_write($otherClient, "$clientIP has disconnected.\n");
                }

                echo "Client $clientIP disconnected\n";
                continue;
            }

            socket_getpeername($clientSocket, $clientIP);
            $alias = resolveClient($clientIP);
            $clientName = getClientName($clientIP);


            echo "Received data from $alias: $data\n";

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
            } else if (str_starts_with($data, "/send")) {
                sendMessage($data, $clientSocket, $clientName, $clientIP, $alias);
            } else {
                socket_write($clientSocket, "Unknown command\n");
            }
        }
    }
}

// Functions remain the same

function resolveClient($ip)
{
    global $knownClients;
    foreach ($knownClients as $client) {
        if ($client['ip'] === $ip) {
            return $client['ip'];
        }
    }
    return "Unknown Client";
}

function checkPermission($ip, $action)
{
    global $knownClients;
    foreach ($knownClients as $client) {
        if ($client['ip'] === $ip && !empty($client[$action])) {
            return true;
        }
    }
    return false;
}

function readFileContent($fileName, $socket)
{
    $filePath = "./Files/$fileName";
    if (!file_exists($filePath)) {
        socket_write($socket, "File not found\n");
        return;
    }
    $content = file_get_contents($filePath);
    socket_write($socket, $content);
}

function writeToFile($fileName, $content, $socket)
{
    $filePath = "./Files/$fileName";
    if (!file_exists($filePath)) {
        socket_write($socket, "File not found\n");
        return;
    }
    file_put_contents($filePath, $content, FILE_APPEND);
    socket_write($socket, "File content updated!\n");
}

function listFiles($socket)
{
    $files = scandir("./Files");
    if ($files === false) {
        socket_write($socket, "Cannot access files on server\n");
        return;
    }
    $fileList = implode("\n", array_diff($files, ['.', '..']));
    socket_write($socket, $fileList ?: "No files exist on the server\n");
}

function showHelp($socket)
{
    $helpMessage = "\n" .
        "Type /read [file name.txt] -> To read from a file!\n" .
        "Type /write [file name.txt] [content] -> To write in a file!\n" .
        "Type /exec [file name.txt] [action] -> (Actions: new - create new file, del - delete file, run - open a file)!\n" .
        "Type exit to close the connection with server!\n" .
        "Type /list to list the files on server!\n" .
        "Type /send [content] -> To send a message to the server.\n";
    socket_write($socket, $helpMessage);
}

function handleExec($fileName, $action, $socket)
{
    $filePath = "./Files/$fileName";

    switch ($action) {
        case "new":
            if (touch($filePath)) {
                socket_write($socket, "File $fileName created!\n");
            } else {
                socket_write($socket, "Error creating file $fileName\n");
            }
            break;
        case "del":
            if (unlink($filePath)) {
                socket_write($socket, "File $fileName has been deleted\n");
            } else {
                socket_write($socket, "Error deleting file $fileName\n");
            }
            break;
        default:
            socket_write($socket, "Invalid command! Type /help to see server commands!\n");
            break;
    }
}

function sendMessage($data, $clientSocket, $clientName, $clientIP, $alias)
{
    $message = trim(substr($data, strlen("/send")));
    $formattedMessage = "$clientName: $message";
    echo "$formattedMessage, IP: $alias\n";
    socket_write($clientSocket, "Message received\n");

}

?>