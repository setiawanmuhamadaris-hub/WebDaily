<?php
function cek_konten_aman($imagePath) {
    // GANTI DENGAN API KEY ANDA
    $apiKey = "AIzaSyC7YWG1AkqwssdOESsk0Mgy3rC9tpxloz4"; 
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Matikan verify SSL jika di localhost error

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return ['aman' => true, 'kategori' => 'Error koneksi AI']; // Default allow jika error koneksi (opsional)
    }
    
    curl_close($ch);

    // Decode respon dari Gemini
    $result = json_decode($response, true);
    
    // Ambil teks jawaban AI
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $rawText = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Bersihkan format markdown ```json jika ada
        $cleanJson = str_replace(['```json', '```'], '', $rawText);
        
        // Decode JSON jawaban AI
        $analysis = json_decode($cleanJson, true);
        
        return $analysis;
    }

    return ['aman' => false, 'kategori' => 'Gagal analisis AI'];
}
?>