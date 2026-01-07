<?php
/**
 * Kompres gambar ke ukuran target (default 100KB)
 * Untuk menghemat token saat pengecekan AI
 * 
 * @param string $sourcePath Path gambar asli
 * @param int $targetSizeKB Target ukuran dalam KB (default 100)
 * @param int $maxWidth Max lebar gambar (default 800px)
 * @return array ['status' => bool, 'path' => string, 'message' => string]
 */
if (!function_exists('kompres_gambar')) {
function kompres_gambar($sourcePath, $targetSizeKB = 100, $maxWidth = 800) {
    $result = ['status' => false, 'path' => '', 'message' => ''];
    
    // Cek apakah file ada
    if (!file_exists($sourcePath)) {
        $result['message'] = 'File tidak ditemukan: ' . $sourcePath;
        return $result;
    }
    
    // Dapatkan info gambar
    $imageInfo = getimagesize($sourcePath);
    if ($imageInfo === false) {
        $result['message'] = 'Bukan file gambar yang valid';
        return $result;
    }
    
    $mimeType = $imageInfo['mime'];
    $origWidth = $imageInfo[0];
    $origHeight = $imageInfo[1];
    
    // Buat image resource berdasarkan tipe
    switch ($mimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            $result['message'] = 'Format gambar tidak didukung: ' . $mimeType;
            return $result;
    }
    
    if (!$image) {
        $result['message'] = 'Gagal membaca gambar';
        return $result;
    }
    
    // Hitung dimensi baru (resize jika lebih besar dari maxWidth)
    if ($origWidth > $maxWidth) {
        $ratio = $maxWidth / $origWidth;
        $newWidth = $maxWidth;
        $newHeight = (int)($origHeight * $ratio);
    } else {
        $newWidth = $origWidth;
        $newHeight = $origHeight;
    }
    
    // Buat gambar baru dengan ukuran kecil
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency untuk PNG
    if ($mimeType === 'image/png') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize gambar
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    
    // Buat path untuk file terkompresi (sementara)
    $pathInfo = pathinfo($sourcePath);
    $compressedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_compressed.jpg';
    
    // Simpan dengan kompresi progresif
    // Mulai dari quality 85, turunkan sampai mencapai target size
    $quality = 85;
    $minQuality = 20;
    $targetBytes = $targetSizeKB * 1024;
    
    do {
        // Simpan ke file
        imagejpeg($newImage, $compressedPath, $quality);
        $currentSize = filesize($compressedPath);
        
        // Jika sudah di bawah target, selesai
        if ($currentSize <= $targetBytes) {
            break;
        }
        
        // Turunkan quality
        $quality -= 10;
    } while ($quality >= $minQuality);
    
    // Bersihkan memory
    imagedestroy($image);
    imagedestroy($newImage);
    
    $result['status'] = true;
    $result['path'] = $compressedPath;
    $result['message'] = 'Berhasil dikompres ke ' . round($currentSize / 1024, 1) . 'KB (quality: ' . $quality . ')';
    
    return $result;
}
} // end function_exists kompres_gambar

/**
 * Hapus file hasil kompresi sementara
 */
if (!function_exists('hapus_file_compressed')) {
function hapus_file_compressed($path) {
    if (file_exists($path) && strpos($path, '_compressed') !== false) {
        unlink($path);
    }
}
} // end function_exists hapus_file_compressed
?>
