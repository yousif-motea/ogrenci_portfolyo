document.addEventListener("DOMContentLoaded", () => {
  console.log("Öğrenci Portfolyo Sistemi: Core JS Başlatıldı.");

  /* -------------------------------------------------------------------------- */
  /* 1. DASHBOARD SAYAÇ ANİMASYONU (Counter Up)                                 */
  /* -------------------------------------------------------------------------- */
  const counters = document.querySelectorAll(".dash-value");

  if (counters.length > 0) {
    counters.forEach((counter) => {
      const target = +counter.innerText; // Mevcut sayıyı al
      const speed = 200; // Hız katsayısı (daha yüksek = daha yavaş)

      // Başlangıçta 0 yap
      counter.innerText = "0";

      const updateCount = () => {
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
          counter.innerText = Math.ceil(count + inc);
          setTimeout(updateCount, 20); // 20ms'de bir güncelle
        } else {
          counter.innerText = target; // Tam sayıya eşitle
        }
      };

      updateCount();
    });
  }

  /* -------------------------------------------------------------------------- */
  /* 2. ANLIK TABLO ARAMA (Canlı Filtreleme)                                    */
  /* -------------------------------------------------------------------------- */
  // Kullanımı: <input type="text" id="tableSearch" placeholder="Ara...">
  const searchInput = document.getElementById("tableSearch");

  if (searchInput) {
    searchInput.addEventListener("keyup", function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll("table tbody tr");

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        // Eğer metin eşleşiyorsa göster, yoksa gizle
        if (text.includes(filter)) {
          row.style.display = "";
          row.style.animation = "fadeIn 0.3s";
        } else {
          row.style.display = "none";
        }
      });
    });
  }

  /* -------------------------------------------------------------------------- */
  /* 3. FORM GÖNDERİM GÜVENLİĞİ (Çift Tıklamayı Önle)                           */
  /* -------------------------------------------------------------------------- */
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const btn = form.querySelector('button[type="submit"]');
      if (btn) {
        // Eğer buton zaten 'loading' durumundaysa gönderimi engelle
        if (btn.dataset.loading === "true") {
          e.preventDefault();
          return;
        }

        // İlk tıklama: Butonu pasif yap ve metni değiştir
        const originalText = btn.innerHTML;
        btn.dataset.loading = "true";
        btn.style.opacity = "0.7";
        btn.style.cursor = "wait";
        btn.innerHTML =
          '<i class="fa-solid fa-spinner fa-spin"></i> İşleniyor...';

        // Not: İşlem çok kısa sürerse veya hata dönerse sayfa yenilenmediği için
        // butonu 5 saniye sonra eski haline getir (güvenlik için)
        setTimeout(() => {
          btn.dataset.loading = "false";
          btn.style.opacity = "1";
          btn.style.cursor = "pointer";
          btn.innerHTML = originalText;
        }, 8000);
      }
    });
  });

  /* -------------------------------------------------------------------------- */
  /* 4. BİLDİRİM KUTULARI (Yumuşak Kapanış)                                     */
  /* -------------------------------------------------------------------------- */
  const alerts = document.querySelectorAll(".alert-error, .alert-success");
  if (alerts.length > 0) {
    // Kullanıcı manuel kapatabilsin diye tıklama ekle
    alerts.forEach((alert) => {
      alert.style.cursor = "pointer";
      alert.title = "Kapatmak için tıklayın";

      alert.addEventListener("click", () => {
        removeAlert(alert);
      });
    });

    // 4 saniye sonra otomatik kapat
    setTimeout(() => {
      alerts.forEach((alert) => {
        removeAlert(alert);
      });
    }, 4000);
  }

  function removeAlert(element) {
    element.style.transition =
      "opacity 0.5s ease, transform 0.5s ease, margin 0.5s ease";
    element.style.opacity = "0";
    element.style.transform = "translateY(-10px)";
    element.style.marginTop = "-10px"; // Yukarı kayarak yok olsun
    setTimeout(() => {
      if (element.parentNode) {
        element.parentNode.removeChild(element);
      }
    }, 500);
  }

  /* -------------------------------------------------------------------------- */
  /* 5. TABLO SATIRLARINA TIKLAMA (Veri Varsa)                                  */
  /* -------------------------------------------------------------------------- */
  const clickableRows = document.querySelectorAll("tr[data-href]");
  clickableRows.forEach((row) => {
    row.style.cursor = "pointer";
    row.addEventListener("click", (e) => {
      // Eğer tıklanan şey bir buton veya link değilse yönlendir
      if (!e.target.closest("a") && !e.target.closest("button")) {
        window.location.href = row.getAttribute("data-href");
      }
    });
  });
});

/* CSS Keyframes Eklemesi (JS ile dinamik stil) */
const styleSheet = document.createElement("style");
styleSheet.innerText = `
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
`;
document.head.appendChild(styleSheet);
