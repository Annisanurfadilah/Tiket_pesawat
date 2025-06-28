<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SemestaAirLine - Pemesanan Tiket Pesawat</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary:rgb(132, 188, 233);
      --secondary:rgb(46, 132, 167);
      --text: #333;
      --background: #f9fafe;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right top,rgb(149, 168, 216), #3f4d7f,rgb(102, 153, 230),rgb(96, 142, 196),rgb(164, 162, 190));
      color: var(--text);
      overflow-x: hidden;
    }

    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      padding: 1rem 2rem;
      background-color: rgba(255, 255, 255, 0.95);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 999;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--text);
      font-weight: 500;
      transition: 0.3s ease;
    }

    .nav-links a:hover {
      color: var(--primary);
    }

    .hero {
      padding: 160px 2rem 100px;
      text-align: center;
      color: white;
      animation: fadeIn 1s ease-out;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 1rem;
      font-weight: 700;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      opacity: 0.9;
    }

    .cta-button {
      background: linear-gradient(45deg, var(--primary), var(--secondary));
      color: white;
      padding: 14px 40px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1.1rem;
      text-decoration: none;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(47, 141, 218, 0.4);
    }

    .cta-button:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(106, 166, 206, 0.6);
    }

    .features {
      background: var(--background);
      padding: 80px 2rem;
      text-align: center;
    }

    .features h2 {
      font-size: 2.5rem;
      color: var(--text);
      margin-bottom: 2rem;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 2rem;
    }

    .feature-card {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feature-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    }

    .feature-icon {
      font-size: 2.8rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .feature-card h3 {
      font-size: 1.4rem;
      margin-bottom: 0.5rem;
    }

    .feature-card p {
      font-size: 1rem;
      color: #555;
    }

    .stats {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      padding: 80px 2rem;
      text-align: center;
    }

    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 2rem;
      max-width: 1000px;
      margin: auto;
    }

    .stat-item h3 {
      font-size: 2.2rem;
    }

    .stat-item p {
      font-size: 1rem;
      opacity: 0.9;
    }

    .footer {
      background: #1f2a44;
      color: white;
      text-align: center;
      padding: 40px 2rem;
    }

    .social-links a {
      color: white;
      margin: 0 1rem;
      font-size: 1.4rem;
      transition: 0.3s ease;
    }

    .social-links a:hover {
      color: var(--primary);
    }

    @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.2rem;
      }

      .nav-links {
        flex-direction: column;
        gap: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">SemestaAirLine</div>
    <div class="nav-links">
      <a href="#hero">Beranda</a>
      <a href="#tentang">Tentang</a>
      <a href="{{ route('login') }}">Login</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" id="hero">
    <h1>Terbang Lebih Mudah Bersama Kami</h1>
    <p>Pemesanan tiket pesawat cepat, aman, dan terpercaya</p>
    <a href="{{ route('login') }}" class="cta-button"><i class="fas fa-plane-departure"></i> Pesan Sekarang</a>
  </section>

  <!-- Features Section -->
  <section class="features">
    <h2>Mengapa SemestaAirLine?</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-bolt"></i></div>
        <h3>Proses Cepat</h3>
        <p>Pesan tiket hanya dalam hitungan menit tanpa ribet.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-lock"></i></div>
        <h3>Keamanan Terjamin</h3>
        <p>Transaksi aman dan data pribadi Anda terlindungi.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-headset"></i></div>
        <h3>Dukungan 24/7</h3>
        <p>Kami selalu siap membantu Anda kapan saja.</p>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="stats" id="tentang">
    <div class="stats-container">
      <div class="stat-item">
        <h3>50K+</h3>
        <p>Pelanggan Puas</p>
      </div>
      <div class="stat-item">
        <h3>1.200+</h3>
        <p>Penerbangan Tersedia</p>
      </div>
      <div class="stat-item">
        <h3>99%</h3>
        <p>Tingkat Kepuasan</p>
      </div>
      <div class="stat-item">
        <h3>24/7</h3>
        <p>Layanan Nonstop</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="social-links">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-whatsapp"></i></a>
    </div>
    <p>&copy; 2025 SemestaAirLine. Semua hak dilindungi.</p>
  </footer>

</body>
</html>
