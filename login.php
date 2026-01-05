<?php
// Memulai session atau melanjutkan session yang sudah ada
session_start();

// Menyertakan code dari file koneksi
include "koneksi.php";

// Check jika sudah ada user yang login arahkan ke halaman admin
if (isset($_SESSION['username'])) { 
	header("location:admin/admin.php"); 
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['user'];
  
  // Menggunakan fungsi enkripsi md5 supaya sama dengan password yang tersimpan di database
  $password = md5($_POST['pass']);

  // Prepared statement
  $stmt = $conn->prepare("SELECT username FROM user WHERE username=? AND password=?");

  // Parameter binding 
  $stmt->bind_param("ss", $username, $password); // username string dan password string
  
  // Database executes the statement
  $stmt->execute();
  
  // Menampung hasil eksekusi
  $hasil = $stmt->get_result();
  
  // Mengambil baris dari hasil sebagai array asosiatif
  $row = $hasil->fetch_array(MYSQLI_ASSOC);

  // Check apakah ada baris hasil data user yang cocok
  if (!empty($row)) {
    // Jika ada, simpan variable username pada session
    $_SESSION['username'] = $row['username'];

    // Mengalihkan ke halaman admin
    header("location:admin/admin.php");
  } else {
    // Jika tidak ada (gagal), alihkan kembali ke halaman login
    header("location:login.php");
  }

  // Menutup koneksi database
  $stmt->close();
  $conn->close();
} 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | My Daily Journal</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    />
    <link rel="icon" href="img/logo.png" />
  </head>
  <body class="bg-success-subtle"> 
    <div class="container mt-5 pt-5">
      <div class="row">
        <div class="col-12 col-sm-8 col-md-6 m-auto">
          <div class="card border-0 shadow rounded-5">
            <div class="card-body">
              <div class="text-center mb-3">
                <i class="bi bi-person-circle h1 display-4 text-success"></i>
                <p class="h5 mt-2">My Daily Journal</p>
                <hr />
              </div>
              
              <form action="" method="post">
                <input
                  type="text"
                  name="user"
                  class="form-control my-4 py-2 rounded-4"
                  placeholder="Username"
                  required
                />
                <input
                  type="password"
                  name="pass"
                  class="form-control my-4 py-2 rounded-4"
                  placeholder="Password"
                  required
                />
                <div class="text-center my-3 d-grid">
                  <button class="btn btn-success rounded-4">Login</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
      crossorigin="anonymous"
    ></script>
  </body>
</html>