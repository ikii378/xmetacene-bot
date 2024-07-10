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
    'user' => [
        'wakePet' => new stdClass() // Objek kosong untuk permintaan petLvUp
    ]
]);

// Load cookies and aliases from config.php
$cookies = require 'config.php';

foreach ($cookies as $item) {
    $alias = $item['alias'];
    $cookie = $item['cookie'];

    // Header permintaan dengan cookie yang berbeda
    $headers = [
        'Accept: application/json, text/plain, */*',
        'Accept-Language: en-US,en;q=0.9',
        'Content-Type: application/json',
        "Cookie: $cookie",
        'Priority: u=1, i',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: none',
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1'
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

        // Cek jika data pet -> lv tersedia dan kirim ke Telegram
        if (isset($data['data']['pet']['lv'])) {
            $rolemanLv = $data['data']['pet']['lv'];
            $pesan = "Bangunin Pet Sukses ($alias): " . $rolemanLv;
            echo $pesan;
            kirimKeTelegram($pesan);
        } else {
            // Jika data pet -> lv tidak tersedia
            $pesan = "Bangunin pet Gagal ($alias).";
            echo $pesan;
            kirimKeTelegram($pesan);
        }
    }

    // Tutup sesi cURL
    curl_close($ch);

    // Jeda 1 menit sebelum mengirim permintaan berikutnya
    sleep(2);
}
?>
