<?php
// Fungsi untuk mengirim pesan ke Telegram
function kirimKeTelegram($pesan) {
    $tokenBotTelegram = '1738476070:AAH4VWw4zr_4cUxHMQ9qvoTRK-iGMxKI2oc'; // Ganti dengan token bot Anda
    $chatId = '-524934115'; // Ganti dengan ID chat Anda
    $urlTelegram = "https://api.telegram.org/bot$tokenBotTelegram/sendMessage";

    $dataPost = [
        'chat_id' => $chatId,
        'text' => $pesan
    ];

    // Kirim permintaan cURL
    $ch = curl_init($urlTelegram);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
    $response = curl_exec($ch);

    // Tampilkan atau log respon
    if (curl_errno($ch)) {
        echo 'Kesalahan cURL Telegram: ' . curl_error($ch);
    } else {
        echo 'Respon Telegram: ' . $response;
    }
    curl_close($ch);
}

// URL dari endpoint API
$url = "https://x.metacene.io/cmd.php";

// Role IDs untuk digunakan dalam permintaan
$roleIds = [1, 2]; // Tambahkan role ID lainnya jika diperlukan
//$roleIds = [2];

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

    foreach ($roleIds as $roleId) {
        // Body permintaan JSON
        $body = json_encode([
            'user' => [
                'roleLvUp' => [
                    'role_id' => $roleId
                ]
            ]
        ]);

        // Kirim permintaan cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($ch);

        // Periksa kesalahan cURL dan tangani respons
        if (curl_errno($ch)) {
            $error = 'Kesalahan cURL: ' . curl_error($ch);
            echo $error;
            kirimKeTelegram($error);
        } else {
            echo 'Respon: ' . $response;
            $data = json_decode($response, true);

            if (isset($data['data']['roleMan']['lv']) && isset($data['data']['roleWoman']['lv'])) {
                $sparValue = $data['data']['roleMan']['lv'];
                $roleWomanX = $data['data']['roleWoman']['lv'];
                $pesan = "Raven Lv: " . $sparValue . "\nEva Lv: " . $roleWomanX . "\nAkun: ($alias)\nRole ID: $roleId";
            } else {
                $pesan = "Gagal !!! Diamond Kurang ($alias) untuk Role ID: $roleId.";
            }
            echo $pesan;
            kirimKeTelegram($pesan);
        }
        curl_close($ch);

        // Jeda 20 detik sebelum mengirim permintaan berikutnya
        sleep(5);
    }
}
?>
