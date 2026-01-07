<?php 
// Include fungsi pendukung
require_once __DIR__ . '/compress.php';
require_once __DIR__ . '/cek_konten.php';

function upload_foto($File){    
    $uploadOk = 1;
    $hasil = array();
    $message = '';
 
    //File properties:
    $FileName = $File['name'];
    $TmpLocation = $File['tmp_name'];
    $FileSize = $File['size'];

    //Figure out what kind of file this is:
    $FileExt = explode('.', $FileName);
    $FileExt = strtolower(end($FileExt));

    //Allowed files:
    $Allowed = array('jpg', 'png', 'gif', 'jpeg');  

    // Check file size
    if ($FileSize > 10000000) {
        $message .= "Sorry, your file is too large, max 10MB. ";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if(!in_array($FileExt, $Allowed)){
        $message .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed. ";
        $uploadOk = 0; 
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message .= "Sorry, your file was not uploaded. ";
        $hasil['status'] = false; 
    }else{
        // === STEP 1: KOMPRES GAMBAR ===
        $kompres = kompres_gambar($TmpLocation, 100, 800);
        
        if (!$kompres['status']) {
            // Jika kompres gagal, gunakan file asli
            $fileToCheck = $TmpLocation;
        } else {
            $fileToCheck = $kompres['path'];
        }
        
        // === STEP 2: CEK KONTEN DENGAN AI ===
        $cekKonten = cek_konten_aman($fileToCheck);
        
        // Hapus file compressed sementara
        if ($kompres['status']) {
            hapus_file_compressed($kompres['path']);
        }
        
        // Jika konten tidak aman, tolak upload
        if (!$cekKonten['aman']) {
            $message .= "Gambar ditolak: " . $cekKonten['kategori'];
            $hasil['status'] = false;
            $hasil['message'] = $message;
            return $hasil;
        }
        
        // === STEP 3: UPLOAD JIKA AMAN ===
        $NewName = date("YmdHis"). '.' . $FileExt;
        $UploadDestination = __DIR__ . "/../../img/". $NewName; 

        if (move_uploaded_file($TmpLocation, $UploadDestination)) {
            $message .= $NewName;
            $hasil['status'] = true; 
        }else{
            $message .= "Sorry, there was an error uploading your file. ";
            $hasil['status'] = false; 
        }
    }
    
    $hasil['message'] = $message; 
    return $hasil;
}
?>