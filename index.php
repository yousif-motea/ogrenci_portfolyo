<?php
require_once 'functions.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Panel - Öğrenci Portfolyo Sistemi</title>
      <link rel="stylesheet" href="style.css">
      <script src="script.js" defer></script>
</head>

<body>
      <header class="topbar">
            <div class="logo">Öğrenci Portfolyo Sistemi</div>
            <div class="user-info">
                  <?php echo htmlspecialchars(currentUserName()); ?>
                  (<?php echo htmlspecialchars(currentUserRole()); ?>)
                  | <a href="logout.php">Çıkış</a>
            </div>
      </header>

      <main class="main-content">
            <h1>Hoş geldin, <?php echo htmlspecialchars(currentUserName()); ?></h1>
            <p>Burası ana panel. Daha sonra buralara şu modülleri ekleyeceğiz:</p>
            <ul>
                  <li>Öğrenci yönetimi</li>
                  <li>Aktivite &amp; katılım kayıtları</li>
                  <li>Disiplin ve PDR kayıtları</li>
                  <li>Rozet / motivasyon sistemi</li>
                  <li>PDF rapor oluşturma</li>
            </ul>
      </main>
</body>

</html>