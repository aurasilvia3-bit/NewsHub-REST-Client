<?php
declare(strict_types=1);

function news_api_request(string $endpoint, array $params = []): array
{
    if (NEWS_API_KEY === '') {
        return ['error' => 'API Key belum diatur. Buat file .env dari .env.example lalu isi NEWS_API_KEY.'];
    }

    if (!function_exists('curl_init')) {
        return ['error' => 'Ekstensi PHP cURL belum aktif. Aktifkan extension=curl pada php.ini lalu restart Apache.'];
    }

    $allowedEndpoints = ['top-headlines', 'everything'];
    if (!in_array($endpoint, $allowedEndpoints, true)) {
        return ['error' => 'Endpoint API tidak didukung.'];
    }

    $url = NEWS_API_BASE_URL . '/' . $endpoint . '?' . http_build_query($params);
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-Api-Key: ' . NEWS_API_KEY,
            'Accept: application/json',
            'User-Agent: NewsHub-REST-Client/1.0'
        ],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return ['error' => 'Gagal menghubungi NewsAPI: ' . ($curlError ?: 'kesalahan koneksi')];
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return ['error' => 'Respons dari server API bukan JSON yang valid.'];
    }

    if ($statusCode < 200 || $statusCode >= 300 || ($data['status'] ?? '') !== 'ok') {
        $message = (string)($data['message'] ?? 'Permintaan API gagal.');
        if ($statusCode === 401 || $statusCode === 403) {
            $message = 'API Key tidak valid atau akses API tidak diizinkan. Periksa akun NewsAPI.';
        } elseif ($statusCode === 429) {
            $message = 'Batas permintaan API tercapai. Coba lagi nanti.';
        }
        return ['error' => $message, 'status_code' => $statusCode];
    }

    return $data;
}

function get_top_headlines(): array
{
    return news_api_request('top-headlines', [
        'country' => NEWS_API_COUNTRY,
        'pageSize' => 12
    ]);
}

function search_news(string $keyword): array
{
    if ($keyword === '') {
        return ['error' => 'Masukkan kata kunci pencarian.'];
    }

    return news_api_request('everything', [
        'q' => $keyword,
        'language' => 'en',
        'sortBy' => 'publishedAt',
        'pageSize' => 12
    ]);
}
