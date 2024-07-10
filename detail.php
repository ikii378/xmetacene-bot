<?php
// Fungsi untuk mengirim pesan ke Telegram
function kirimKeTelegram($pesan) {
    $tokenBotTelegram = 'YOUR_TOKEN'; // Ganti dengan token bot Anda
    $chatId = 'CHAT_ID'; // Ganti dengan ID chat Anda
    $urlTelegram = "https://api.telegram.org/bot$tokenBotTelegram/sendMessage";

    $dataPost = [
        'chat_id' => $chatId,
        'text' => $pesan
    ];

    // Inisialisasi sesi cURL
    $ch = curl_init($urlTelegram);

    // Set opsi cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);

    // Eksekusi permintaan cURL
    $response = curl_exec($ch);

    // Periksa kesalahan cURL
    if (curl_errno($ch)) {
        echo 'Kesalahan cURL Telegram: ' . curl_error($ch);
    } else {
        // Tampilkan respon dari server
        echo 'Respon Telegram: ' . $response;
    }

    // Tutup sesi cURL
    curl_close($ch);
}

// URL dari endpoint API
$url = "https://x.metacene.io/cmd.php";

// The JSON body to include in the POST request
$body = json_encode([
    'guide' => [
        'login' => new stdClass() // Objek kosong untuk permintaan petLvUp
    ]
]);

// Load cookies and aliases from config.php
$configFile = 'config.php';
if (!file_exists($configFile)) {
    die('Config file not found.');
}

$cookies = require $configFile;
if (!is_array($cookies) || empty($cookies)) {
    die('Invalid config data.');
}

foreach ($cookies as $item) {
    $alias = $item['alias'];
    $cookie = $item['cookie'];

    // Header permintaan dengan cookie yang berbeda
    $headers = [
        'Accept: application/json, text/plain, */*',
        'Accept-Language: en-US,en;q=0.9',
        'Content-Type: application/json',
        "Cookie: $cookie",
        'Origin: chrome-extension://igimfdmnnijclcfdgimooedbealfpndj',
        'Priority: u=1, i',
        'Sec-CH-UA: "Google Chrome";v="125", "Chromium";v="125", "Not.A/Brand";v="24"',
        'Sec-CH-UA-Mobile: ?0',
        'Sec-CH-UA-Platform: "Windows"',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: none',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36'
    ];

    // Inisialisasi sesi cURL
    $ch = curl_init($url);

    // Set opsi cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    // Eksekusi permintaan cURL
    $response = curl_exec($ch);

    // Periksa kesalahan cURL
    if (curl_errno($ch)) {
        $error = 'Kesalahan cURL: ' . curl_error($ch);
        echo $error;
        kirimKeTelegram($error);
    } else {
        // Tampilkan respon dari server
        echo 'Respon: ' . $response;

        // Decode JSON response
        $data = json_decode($response, true);

         // Cek jika data header -> spar dan header -> integral tersedia dan kirim ke Telegram
         if (isset($data['data']['header']['spar']) && isset($data['data']['header']['integral']) && isset($data['data']['user']['invite_code'])) {
            $sparValue = $data['data']['header']['spar'];
            $integralValue = $data['data']['header']['integral'];
            $codeInvitelValue = $data['data']['user']['invite_code'];
            // Contoh tambahan nilai honor
            $pesan = "Diamond Value : " . $sparValue . "\nHonor Value : " . $integralValue . "\nCode invite : " . $codeInvitelValue . "\nAkun : ($alias)";
            echo $pesan;
            kirimKeTelegram($pesan);
        } else {
            // Jika data header -> spar atau header -> integral tidak tersedia
            $pesan = "Data header -> spar atau integral tidak ditemukan ($alias).";
            echo $pesan;
            kirimKeTelegram($pesan);
        }
    }

    // Tutup sesi cURL
    curl_close($ch);

    // Jeda 1 menit sebelum mengirim permintaan berikutnya
    sleep(10);
}
?>
