document.addEventListener("DOMContentLoaded", () => {
  console.log("🎓 Öğrenci Portfolyo Sistemi: Core JS Başlatıldı.");

  /* -------------------------------------------------------------------------- */
  /* 0. DARK MODE TOGGLE                                                        */
  /* -------------------------------------------------------------------------- */
  // LocalStorage'dan tema tercihini al
  const currentTheme = localStorage.getItem("theme") || "light";
  document.documentElement.setAttribute("data-theme", currentTheme);

  // Theme toggle butonu oluştur
  const themeToggle = document.createElement("button");
  themeToggle.className = "theme-toggle";
  themeToggle.setAttribute("aria-label", "Tema Değiştir");
  themeToggle.innerHTML =
    currentTheme === "dark"
      ? '<i class="fa-solid fa-sun"></i>'
      : '<i class="fa-solid fa-moon"></i>';

  document.body.appendChild(themeToggle);

  // Tema değiştirme fonksiyonu
  function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute("data-theme");
    const newTheme = currentTheme === "dark" ? "light" : "dark";

    document.documentElement.setAttribute("data-theme", newTheme);
    localStorage.setItem("theme", newTheme);

    // İkon animasyonuyla değiştir
    themeToggle.style.transform = "rotate(360deg) scale(0.8)";

    setTimeout(() => {
      themeToggle.innerHTML =
        newTheme === "dark"
          ? '<i class="fa-solid fa-sun"></i>'
          : '<i class="fa-solid fa-moon"></i>';
      themeToggle.style.transform = "rotate(0deg) scale(1)";
    }, 200);
  }

  themeToggle.addEventListener("click", toggleTheme);

  // Klavye kısayolu: Ctrl/Cmd + Shift + D
  document.addEventListener("keydown", (e) => {
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === "D") {
      e.preventDefault();
      toggleTheme();
    }
  });

  /* -------------------------------------------------------------------------- */
  /* 1. DASHBOARD SAYAÇ ANİMASYONU (Counter Up)                                 */
  /* -------------------------------------------------------------------------- */
  const counters = document.querySelectorAll(".dash-value");

  if (counters.length > 0) {
    const observerOptions = {
      threshold: 0.5,
      rootMargin: "0px 0px -50px 0px",
    };

    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && !entry.target.dataset.counted) {
          entry.target.dataset.counted = "true";
          animateCounter(entry.target);
        }
      });
    }, observerOptions);

    counters.forEach((counter) => {
      counterObserver.observe(counter);
    });

    function animateCounter(counter) {
      const target = +counter.innerText;
      const speed = 200;
      counter.innerText = "0";

      const updateCount = () => {
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
          counter.innerText = Math.ceil(count + inc);
          setTimeout(updateCount, 20);
        } else {
          counter.innerText = target;
        }
      };

      updateCount();
    }
  }

  /* -------------------------------------------------------------------------- */
  /* 2. ANLIK TABLO ARAMA (Canlı Filtreleme)                                    */
  /* -------------------------------------------------------------------------- */
  const searchInput = document.getElementById("tableSearch");

  if (searchInput) {
    // Arama input'una icon ekle
    if (
      !searchInput.previousElementSibling?.classList.contains("search-wrapper")
    ) {
      const wrapper = document.createElement("div");
      wrapper.className = "search-wrapper";
      wrapper.style.cssText =
        "position: relative; max-width: 400px; margin-bottom: 20px;";
      searchInput.parentNode.insertBefore(wrapper, searchInput);
      wrapper.appendChild(searchInput);
    }

    // Debounce fonksiyonu - performans için
    let searchTimeout;
    searchInput.addEventListener("keyup", function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        const filter = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll("table tbody tr");
        let visibleCount = 0;

        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          if (text.includes(filter)) {
            row.style.display = "";
            row.style.animation = "fadeIn 0.3s ease";
            visibleCount++;
          } else {
            row.style.display = "none";
          }
        });

        // Sonuç sayısını göster
        updateSearchResults(visibleCount, rows.length);
      }, 300);
    });

    function updateSearchResults(visible, total) {
      let resultInfo = document.getElementById("search-result-info");
      if (!resultInfo) {
        resultInfo = document.createElement("div");
        resultInfo.id = "search-result-info";
        resultInfo.style.cssText =
          "font-size: 13px; color: var(--text-muted); margin-bottom: 12px;";
        searchInput.parentNode.insertBefore(
          resultInfo,
          searchInput.nextSibling
        );
      }

      if (searchInput.value.trim() === "") {
        resultInfo.innerHTML = `Toplam <strong>${total}</strong> kayıt`;
      } else {
        resultInfo.innerHTML = `<strong>${visible}</strong> / ${total} kayıt gösteriliyor`;
      }
    }

    // İlk yüklemede toplam sayıyı göster
    const totalRows = document.querySelectorAll("table tbody tr").length;
    if (totalRows > 0) {
      updateSearchResults(totalRows, totalRows);
    }
  }

  /* -------------------------------------------------------------------------- */
  /* 3. FORM GÖNDERİM GÜVENLİĞİ (Çift Tıklamayı Önle)                           */
  /* -------------------------------------------------------------------------- */
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const btn = form.querySelector('button[type="submit"]');
      if (btn) {
        if (btn.dataset.loading === "true") {
          e.preventDefault();
          return;
        }

        const originalText = btn.innerHTML;
        btn.dataset.loading = "true";
        btn.dataset.originalText = originalText;
        btn.style.opacity = "0.7";
        btn.style.cursor = "wait";
        btn.innerHTML =
          '<i class="fa-solid fa-spinner fa-spin"></i> İşleniyor...';

        // 8 saniye sonra butonu eski haline getir
        setTimeout(() => {
          if (btn.dataset.loading === "true") {
            btn.dataset.loading = "false";
            btn.style.opacity = "1";
            btn.style.cursor = "pointer";
            btn.innerHTML = btn.dataset.originalText || originalText;
          }
        }, 8000);
      }
    });
  });

  /* -------------------------------------------------------------------------- */
  /* 4. BİLDİRİM KUTULARI (Yumuşak Kapanış)                                     */
  /* -------------------------------------------------------------------------- */
  const alerts = document.querySelectorAll(".alert-error, .alert-success");
  if (alerts.length > 0) {
    alerts.forEach((alert) => {
      alert.style.cursor = "pointer";
      alert.title = "Kapatmak için tıklayın";

      // Hover efekti ekle
      alert.addEventListener("mouseenter", () => {
        alert.style.transform = "scale(1.02)";
      });

      alert.addEventListener("mouseleave", () => {
        alert.style.transform = "scale(1)";
      });

      alert.addEventListener("click", () => {
        removeAlert(alert);
      });
    });

    // 5 saniye sonra otomatik kapat
    setTimeout(() => {
      alerts.forEach((alert) => {
        removeAlert(alert);
      });
    }, 5000);
  }

  function removeAlert(element) {
    if (element.dataset.removing === "true") return;
    element.dataset.removing = "true";

    element.style.transition = "all 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
    element.style.opacity = "0";
    element.style.transform = "translateY(-20px) scale(0.95)";
    element.style.maxHeight = element.offsetHeight + "px";

    setTimeout(() => {
      element.style.maxHeight = "0";
      element.style.marginBottom = "0";
      element.style.paddingTop = "0";
      element.style.paddingBottom = "0";
    }, 100);

    setTimeout(() => {
      if (element.parentNode) {
        element.parentNode.removeChild(element);
      }
    }, 600);
  }

  /* -------------------------------------------------------------------------- */
  /* 5. TABLO SATIRLARINA TIKLAMA (Veri Varsa)                                  */
  /* -------------------------------------------------------------------------- */
  const clickableRows = document.querySelectorAll("tr[data-href]");
  clickableRows.forEach((row) => {
    row.style.cursor = "pointer";
    row.addEventListener("click", (e) => {
      if (!e.target.closest("a") && !e.target.closest("button")) {
        window.location.href = row.getAttribute("data-href");
      }
    });
  });

  /* -------------------------------------------------------------------------- */
  /* 6. SMOOTH SCROLL TO TOP BUTTON                                             */
  /* -------------------------------------------------------------------------- */
  const scrollToTopBtn = document.createElement("button");
  scrollToTopBtn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
  scrollToTopBtn.className = "scroll-to-top";
  scrollToTopBtn.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    transition: all 0.3s ease;
    z-index: 999;
    font-size: 18px;
  `;

  document.body.appendChild(scrollToTopBtn);

  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      scrollToTopBtn.style.display = "flex";
    } else {
      scrollToTopBtn.style.display = "none";
    }
  });

  scrollToTopBtn.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

  scrollToTopBtn.addEventListener("mouseenter", () => {
    scrollToTopBtn.style.transform = "translateY(-5px) scale(1.1)";
    scrollToTopBtn.style.boxShadow = "0 8px 20px rgba(99, 102, 241, 0.5)";
  });

  scrollToTopBtn.addEventListener("mouseleave", () => {
    scrollToTopBtn.style.transform = "translateY(0) scale(1)";
    scrollToTopBtn.style.boxShadow = "0 4px 12px rgba(99, 102, 241, 0.4)";
  });

  /* -------------------------------------------------------------------------- */
  /* 7. TOOLTIP İŞLEVSELLİĞİ                                                    */
  /* -------------------------------------------------------------------------- */
  const elementsWithTitle = document.querySelectorAll("[title]");
  elementsWithTitle.forEach((element) => {
    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") return;

    element.addEventListener("mouseenter", (e) => {
      const tooltip = document.createElement("div");
      tooltip.className = "custom-tooltip";
      tooltip.textContent = element.getAttribute("title");
      tooltip.style.cssText = `
        position: absolute;
        background: rgba(15, 23, 42, 0.95);
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        z-index: 9999;
        pointer-events: none;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.2s ease;
      `;

      document.body.appendChild(tooltip);

      const rect = element.getBoundingClientRect();
      tooltip.style.left =
        rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px";
      tooltip.style.top =
        rect.top - tooltip.offsetHeight - 10 + window.scrollY + "px";

      element.dataset.tooltipId = Date.now();
      tooltip.dataset.tooltipId = element.dataset.tooltipId;

      element.removeAttribute("title");
      element.dataset.originalTitle = tooltip.textContent;
    });

    element.addEventListener("mouseleave", (e) => {
      const tooltips = document.querySelectorAll(
        `.custom-tooltip[data-tooltip-id="${element.dataset.tooltipId}"]`
      );
      tooltips.forEach((t) => t.remove());

      if (element.dataset.originalTitle) {
        element.setAttribute("title", element.dataset.originalTitle);
      }
    });
  });

  /* -------------------------------------------------------------------------- */
  /* 8. RESPONSIVE NAVİGASYON (Mobil Menü)                                      */
  /* -------------------------------------------------------------------------- */
  const userInfo = document.querySelector(".user-info");
  if (userInfo && window.innerWidth <= 768) {
    const topbar = document.querySelector(".topbar");
    if (topbar) {
      topbar.style.flexDirection = "column";
      topbar.style.alignItems = "stretch";
      topbar.style.gap = "12px";
      topbar.style.padding = "16px 20px";
    }
  }

  /* -------------------------------------------------------------------------- */
  /* 9. CARD HOVER PARALLAX EFEKTİ                                              */
  /* -------------------------------------------------------------------------- */
  const dashCards = document.querySelectorAll(".dash-card");
  dashCards.forEach((card) => {
    card.addEventListener("mousemove", (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      const centerX = rect.width / 2;
      const centerY = rect.height / 2;

      const rotateX = (y - centerY) / 20;
      const rotateY = (centerX - x) / 20;

      card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
    });

    card.addEventListener("mouseleave", () => {
      card.style.transform =
        "perspective(1000px) rotateX(0) rotateY(0) translateY(0)";
    });
  });

  /* -------------------------------------------------------------------------- */
  /* 10. LAZY LOADING GÖRSELLERİ                                                */
  /* -------------------------------------------------------------------------- */
  const images = document.querySelectorAll("img[data-src]");
  const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.removeAttribute("data-src");
        imageObserver.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));

  console.log("✅ Tüm özellikler başarıyla yüklendi!");
});

/* -------------------------------------------------------------------------- */
/* CSS KEYFRAMES EKLE                                                          */
/* -------------------------------------------------------------------------- */
const styleSheet = document.createElement("style");
styleSheet.innerText = `
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  @keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
  }
  
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
  }
  
  @keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
  }
`;
document.head.appendChild(styleSheet);
