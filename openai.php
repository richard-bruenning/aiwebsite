<?php
// Fehlerberichterstattung deaktivieren (für Produktionsumgebung)
error_reporting(0);

// OpenAI API-Schlüssel sicher laden
require '../openaisecret/config.php';

// Überprüfen, ob der API-Schlüssel gesetzt ist
if (!$api_key) {
    http_response_code(500);
    echo 'API-Schlüssel nicht konfiguriert.';
    exit();
}

// Den Prompt im Backend definieren
$prompt = 'Bitte erzeuge eine komplette Website zum Thema KI, stell positive Interaktionen und einen Fahrplan in die Zukunft vor. 
    Wie kann die Menschheit und die KI zusammen großes erreichen? Suche auch nach aktuellen Themen. Versuche eigenständig die Website interessant zu machen. Gib nur den HTML Code aus.';

// Die OpenAI API aufrufen
$ch = curl_init();

$data = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'user', 'content' => $prompt],
        ['role' => 'system', 'content' => 'Du bist ein Service der reinen HTML Code liefert, keine Kommentare oder Erklärungen oder Formatierungen.
                                        Du nutzt auch CSS und JavaScript um die Website ansprechend und modern zu gestalten. Gib nur eine Html Seite zurück.' ]
    ],
    'temperature' => 0.7,
];

curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key,
    'OpenAI-Project: proj_Ng3xjUehL94Qt1iEXWCciNIp',
    'OpenAI-Organization: org-FYFlk77z0JTK1hx9unQgcmth'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpcode != 200) {
    http_response_code(500);
    echo 'Fehler bei der Anfrage an die OpenAI API.';
    echo $httpcode;
    echo $response;
    exit();
}

curl_close($ch);

$result = json_decode($response, true);
$text = $result['choices'][0]['message']['content'] ?? '';

// Das Ergebnis zurückgeben
echo $text;
?>
