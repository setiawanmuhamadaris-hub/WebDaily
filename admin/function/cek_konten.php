<?php
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

    // GANTI DENGAN API KEY ANDA
    $apiKey = "AIzaSyB6PrFT8FhUrvtK2wUP3Uk4YBy0c3pMKbE"; 
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

    // Baca file gambar dan ubah ke base64
    $imageData = base64_encode(file_get_contents($imagePath));
    $mimeType = mime_content_type($imagePath);

    // Prompt khusus untuk moderasi konten
    $promptText = "Kamu adalah sistem filter konten. Analisis gambar ini. 
    Apakah gambar ini mengandung: Kekerasan, Pornografi, Senjata, atau Kebencian? 
    Jawab HANYA dengan format JSON: {\"aman\": true/false, \"kategori\": \"alasannya\"}. 
    Jika aman, kategori boleh kosong.";

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
    
    // Cek jika ada error dari API
    if (isset($result['error'])) {
        // Fail-closed: tolak jika API error
        return ['aman' => false, 'kategori' => 'API Error: ' . $result['error']['message']];
    }
    
    // Ambil teks jawaban AI
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $rawText = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Bersihkan format markdown ```json jika ada
        $cleanJson = trim(str_replace(['```json', '```', "\n"], '', $rawText));
        
        // Decode JSON jawaban AI
        $analysis = json_decode($cleanJson, true);
        
        if ($analysis !== null && isset($analysis['aman'])) {
            return $analysis;
        }
    }

    // Fail-closed: tolak jika tidak bisa analisis
    return ['aman' => false, 'kategori' => 'Gagal analisis - gambar ditolak untuk keamanan'];
}
?>