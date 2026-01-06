<?php
// === API USAGE TRACKER ===
if (!defined('API_USAGE_LOG')) {
    define('API_USAGE_LOG', __DIR__ . '/api_usage.json');
}
if (!defined('DAILY_LIMIT')) {
    define('DAILY_LIMIT', 1500);
}

if (!function_exists('log_api_usage')) {
function log_api_usage($tokenUsed = 0, $success = true) {
    $today = date('Y-m-d');
    $usage = [];
    
    // Baca file log jika ada
    if (file_exists(API_USAGE_LOG)) {
        $usage = json_decode(file_get_contents(API_USAGE_LOG), true) ?? [];
    }
    
    // Reset jika hari berbeda
    if (!isset($usage['date']) || $usage['date'] !== $today) {
        $usage = [
            'date' => $today,
            'requests' => 0,
            'tokens' => 0,
            'success' => 0,
            'failed' => 0
        ];
    }
    
    // Update counter
    $usage['requests']++;
    $usage['tokens'] += $tokenUsed;
    if ($success) {
        $usage['success']++;
    } else {
        $usage['failed']++;
    }
    $usage['remaining'] = DAILY_LIMIT - $usage['requests'];
    $usage['last_request'] = date('H:i:s');
    
    // Simpan ke file
    file_put_contents(API_USAGE_LOG, json_encode($usage, JSON_PRETTY_PRINT));
    
    return $usage;
}
}

if (!function_exists('get_api_usage')) {
function get_api_usage() {
    $today = date('Y-m-d');
    
    if (file_exists(API_USAGE_LOG)) {
        $usage = json_decode(file_get_contents(API_USAGE_LOG), true);
        if ($usage && $usage['date'] === $today) {
            return $usage;
        }
    }
    
    return [
        'date' => $today,
        'requests' => 0,
        'tokens' => 0,
        'remaining' => DAILY_LIMIT,
        'success' => 0,
        'failed' => 0
    ];
}
}

if (!function_exists('cek_konten_aman')) {
function cek_konten_aman($imagePath) {
    // === DEVELOPMENT MODE: Set ke false untuk mengaktifkan AI check ===
    $BYPASS_AI_CHECK = false; // SEMENTARA: quota API habis, aktifkan lagi nanti
    
    if ($BYPASS_AI_CHECK) {
        return ['aman' => true, 'kategori' => 'AI check disabled (dev mode)'];
    }
    
    // Cek apakah file ada
    if (!file_exists($imagePath)) {
        // Fail-closed: tolak jika file tidak ditemukan
        return ['aman' => false, 'kategori' => 'File tidak ditemukan: ' . $imagePath];
    }

    // API Key Gemini
    $apiKey = "AIzaSyDriAUjiNXPsX-VSF1mUmJ3SDtdSeSAq_0"; 
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

    // Baca file gambar dan ubah ke base64
    $imageData = base64_encode(file_get_contents($imagePath));
    $mimeType = mime_content_type($imagePath);

    // Prompt untuk moderasi konten - versi halus agar tidak di-block
    $promptText = "You are an image reviewer for a family-friendly news website. 
Analyze this image and determine if it is appropriate for all audiences.

Consider if the image contains anything that would NOT be suitable for:
- A general news publication
- Viewers of all ages
- Professional workplace viewing

Rate the image safety. 
Respond ONLY in this exact JSON format:
{\"aman\": true, \"kategori\": \"\", \"confidence\": 0.95}

If safe for all audiences: aman = true, kategori = empty string
If NOT safe: aman = false, kategori = brief reason in Indonesian";

    // Data yang dikirim ke API
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $promptText],
                    [
                        "inline_data" => [
                            "mime_type" => $mimeType,
                            "data" => $imageData
                        ]
                    ]
                ]
            ]
        ]
    ];

    // Konfigurasi cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        // Fail-closed: tolak jika koneksi error
        return ['aman' => false, 'kategori' => 'Error koneksi: ' . $error];
    }
    
    curl_close($ch);

    // Decode respon dari Gemini
    $result = json_decode($response, true);
    
    // DEBUG: Log full response (hapus setelah debug selesai)
    file_put_contents(__DIR__ . '/debug_ai_response.txt', date('Y-m-d H:i:s') . "\n" . "FULL RESPONSE:\n" . print_r($result, true) . "\n\n", FILE_APPEND);
    
    // Log penggunaan API
    $tokenUsed = $result['usageMetadata']['totalTokenCount'] ?? 0;
    $success = !isset($result['error']);
    log_api_usage($tokenUsed, $success);
    
    // Cek jika ada error dari API
    if (isset($result['error'])) {
        // Fail-closed: tolak jika API error
        return ['aman' => false, 'kategori' => 'API Error: ' . $result['error']['message']];
    }
    
    // Ambil teks jawaban AI
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $rawText = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Bersihkan format markdown ```json jika ada
        $cleanJson = $rawText;
        $cleanJson = preg_replace('/```json\s*/', '', $cleanJson);
        $cleanJson = preg_replace('/```\s*/', '', $cleanJson);
        $cleanJson = trim($cleanJson);
        
        // Decode JSON jawaban AI
        $analysis = json_decode($cleanJson, true);
        
        if ($analysis !== null && isset($analysis['aman'])) {
            return $analysis;
        }
        
        // DEBUG: Log jika parsing gagal
        file_put_contents(__DIR__ . '/debug_ai_response.txt', "PARSE FAILED: " . json_last_error_msg() . "\nClean JSON: " . $cleanJson . "\n\n", FILE_APPEND);
    }

    // Fail-closed: tolak jika tidak bisa analisis
    return ['aman' => false, 'kategori' => 'Gagal analisis - gambar ditolak untuk keamanan'];
}
} // end function_exists cek_konten_aman
?>