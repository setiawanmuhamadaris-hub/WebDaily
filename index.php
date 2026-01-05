<?php
include "koneksi.php"; 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daily Journal</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />

    <link rel="icon" href="img/logo.png" type="image/png" />
  </head>
  <body>
    <nav
      class="navbar navbar-expand-lg bg-body-tertiary sticky-top"
      data-bs-theme="light"
    >
      <div class="container">
        <a class="navbar-brand" href="#">Daily Journal</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-dark">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="#hero">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#article">Article</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#gallery">Gallery</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#schedule">Schedule</a>
              </li>
            <li class="nav-item">
              <a class="nav-link" href="#profile">Profile</a>
              </li>
              <li class="nav-item">
              <a class="nav-link fw-bold" href="login.php">Login</a>
            </li>
            </ul>

          <div class="ms-3 d-flex">
            <button class="btn btn-dark me-2" id="btn-dark">
              <i class="bi bi-moon-stars-fill"></i>
            </button>
            <button class="btn btn-light" id="btn-light">
              <i class="bi bi-sun-fill"></i>
            </button>
          </div>
        </div>
      </div>
    </nav>
    <section
      id="hero"
      class="text-center p-5 bg-success-subtle text-sm-start"
    >
      <div class="container">
        <div class="d-sm-flex flex-sm-row-reverse align-items-center">
          <img
            src="img/banner1.jpg"
            width="300"
            class="img-fluid"
            alt="Banner"
          />
          <div>
            <h1 class="fw-bold display-4">
              Explore nature, Capture the moment
            </h1>
            <h4 class="lead display-6">
              Menjelajahi semua keajaiban alam dan mengabadikan setiap momennya
            </h4>
            <h6>
              <span id="tanggal"></span>
              <span id="jam"></span>
            </h6>
          </div>
        </div>
      </div>
    </section>
    <!-- article begin -->
    <section id="article" class="text-center p-5">
      <div class="container">
        <h1 class="fw-bold display-4 pb-3">article</h1>
        <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
          <?php
          $sql = "SELECT * FROM article ORDER BY tanggal DESC";
          $hasil = $conn->query($sql); 

          while($row = $hasil->fetch_assoc()){
          ?>
            <div class="col">
              <div class="card h-100">
                <img src="img/<?= $row["gambar"]?>" class="card-img-top" alt="..." />
                <div class="card-body">
                  <h5 class="card-title"><?= $row["judul"]?></h5>
                  <p class="card-text">
                    <?= $row["isi"]?>
                  </p>
                </div>
                <div class="card-footer">
                  <small class="text-body-secondary">
                    <?= $row["tanggal"]?>
                  </small>
                </div>
              </div>
            </div>
            <?php
          }
          ?> 
        </div>
      </div>
    </section>
    <!-- article end -->
    <section id="gallery" class="text-center p-5 bg-success-subtle">
      <div class="container">
        <h1 class="fw-bold display-4 pb-3">Gallery</h1>
        <div
          id="carouselExampleSlidesOnly"
          class="carousel slide"
          data-bs-ride="carousel"
        >
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img
                src="img/gallery1.jpg"
                class="d-block w-100"
                alt="Galeri 1"
              />
            </div>
            <div class="carousel-item">
              <img
                src="img/gallery2.jpg"
                class="d-block w-100"
                alt="Galeri 2"
              />
            </div>
            <div class="carousel-item">
              <img
                src="img/gallery3.jpg"
                class="d-block w-100"
                alt="Galeri 3"
              />
            </div>
            <div class="carousel-item">
              <img
                src="img/gallery4.jpg"
                class="d-block w-100"
                alt="Galeri 4"
              />
            </div>
            <div class="carousel-item">
              <img
                src="img/gallery5.jpg"
                class="d-block w-100"
                alt="Galeri 5"
              />
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="profile" class="p-5 ">
      <div class="container">
        <h1 class="fw-bold display-4 pb-3 text-center">Profile</h1>
        <div class="row justify-content-center align-items-center">
          <div class="col-md-4 text-center">
            <img
              src="profile.jpg"
              class="img-fluid rounded-circle w-75"
              alt="Foto Profil"
            />
          </div>
          <div class="col-md-8">
            <h3 class="text-center d-md-none mt-3">Muhamad Aris Setiawan</h3>
            <h3 class="text-md-start d-none d-md-block">
              Muhamad Aris Setiawan
            </h3>
            <table class="table table-borderless">
              <tbody>
                <tr>
                  <th style="width: 30%">NIM</th>
                  <td>: A11.2024.15984</td>
                </tr>
                <tr>
                  <th>Program Studi</th>
                  <td>: Teknik Informatika</td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td>: 111202415984@mhs.dinus.ac.id</td>
                </tr>
                <tr>
                  <th>Telepon</th>
                  <td>: +62 897 3498 424</td>
                </tr>
                <tr>
                  <th>Alamat</th>
                  <td>: Jl. Kaliandra No. 13, Kab.Semarang, Kec.Bawen</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <section id="schedule" class="text-center p-5 bg-success-subtle">
      <div class="container">
        <h1 class="fw-bold display-4 pb-3">Schedule</h1>
        <p class="lead">Jadwal Kuliah & Kegiatan Mahasiswa</p>
        <div class="row row-cols-1 row-cols-md-4 g-4 justify-content-center">
          <div class="col">
            <div class="card text-bg-primary mb-3 mx-auto" style="max-width: 18rem">
              <div class="card-header">Senin</div>
              <div class="card-body bg-body text-body">
                <h5 class="card-title">09.30-12.00</h5>
                <p class="card-text">
                  Probabilitas dan Statistik <br />H.5.11
                </p>
                <h5 class="card-title">15.30-18.00</h5>
                <p class="card-text">Logika Informatika<br />H.3.9</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card text-bg-success mb-3 mx-auto" style="max-width: 18rem">
              <div class="card-header">Selasa</div>
              <div class="card-body bg-body text-body">
                <h5 class="card-title">10.20-12.00</h5>
                <p class="card-text">Basis Data<br />D.2.K</p>
                <h5 class="card-title">12.30-14.10</h5>
                <p class="card-text">Pemrograman Berbasis Web<br />D.2.J</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card text-bg-warning mb-3 mx-auto" style="max-width: 18rem">
              <div class="card-header text-white">Rabu</div>
              <div class="card-body bg-body text-body">
                <h5 class="card-title">09.30-12.00</h5>
                <p class="card-text">Rekayasa Perangkat Lunak<br />H.3.10</p>
                <h5 class="card-title">12.30-15.00</h5>
                <p class="card-text">Kriptografi<br />H.5.9</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card text-bg-danger mb-3 mx-auto" style="max-width: 18rem">
              <div class="card-header">Kamis</div>
              <div class="card-body bg-body text-body">
                <h5 class="card-title">10.20-12.00</h5>
                <p class="card-text">Basis Data<br />H.5.6</p>
                <h5 class="card-title">12.30-15.00</h5>
                <p class="card-text">Sistem Operasi<br />H.3.10</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card text-bg-info mb-3 mx-auto" style="max-width: 18rem">
              <div class="card-header text-white">Jumat</div>
              <div class="card-body bg-body text-body">
                <h5 class="card-title">12.30-15.00</h5>
                <p class="card-text">Penambangan Data<br />H.4.3</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card text-bg-dark mb-3 mx-auto" style="max-width: 18rem">
              <div class="card-header">Sabtu</div>
              <div class="card-body bg-body text-body">
                <h5 class="card-title">9.30-12.00</h5>
                <p class="card-text">BTNG<br />H.4.3</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card text-bg-secondary mb-3 mx-auto" style="max-width: 18rem">
              <div class="card-header ">Minggu</div>
              <div class="card-body bg-body text-body ">
                <h5 class="card-title">Libur</h5>
                <p class="card-text">Tidak ada jadwal kuliah.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <footer class="text-center p-5 --bs-dark">
      <div>
        <a href="#" class="h2 p-2 text-decoration-none">
          <i class="bi bi-instagram"></i>
        </a>
        <a href="#" class="h2 p-2 text-decoration-none">
          <i class="bi bi-twitter"></i>
        </a>
        <a href="#" class="h2 p-2 text-decoration-none">
          <i class="bi bi-whatsapp"></i>
        </a>
      </div>
      <div>
        <p>
          Created by MAZETI with
          <i class="bi bi-fire text-danger"></i>
        </p>
      </div>
    </footer>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script type="text/javascript">
      window.setTimeout("tampilanWaktu()", 1000);

      function tampilanWaktu() {
        var waktu = new Date();
        var bulan = waktu.getMonth() + 1;

        setTimeout("tampilanWaktu()", 1000);
        document.getElementById("tanggal").innerHTML =
          waktu.getDate() + "/" + bulan + "/" + waktu.getFullYear();
        document.getElementById("jam").innerHTML =
          waktu.getHours() +
          ":" +
          waktu.getMinutes() +
          ":" +
          waktu.getSeconds();
      }

      const btnDark = document.getElementById("btn-dark");
      const btnLight = document.getElementById("btn-light");

      const htmlElement = document.documentElement;

      btnDark.onclick = function () {
        htmlElement.setAttribute("data-bs-theme", "dark");
      };

      btnLight.onclick = function () {
        htmlElement.setAttribute("data-bs-theme", "light");
      };
    </script>
  </body>
</html>