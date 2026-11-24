document.addEventListener('DOMContentLoaded', function () {
  // Sidebar Toggle Desktop & Mobile
  const toggleBtn = document.getElementById('sidebarToggleBtn');
  const sidebar = document.querySelector('.app-sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');

  // Pastikan default sidebar TERBUKA PENUH (260px) sesuai tampilan asli KCLC
  document.body.classList.remove('sidebar-collapsed');
  try {
    localStorage.removeItem('sidebarCollapsed');
    sessionStorage.removeItem('sidebarCollapsed');
  } catch (e) {}

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      if (window.innerWidth < 992) {
        sidebar.classList.toggle('show-mobile');
        if (backdrop) backdrop.classList.toggle('show');
      } else {
        // Toggle slide-out / slide-in sidebar di laptop/desktop
        document.body.classList.toggle('sidebar-closed');
      }
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', function () {
      sidebar.classList.remove('show-mobile');
      backdrop.classList.remove('show');
    });
  }

  // Intercept any form with data-confirm
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!form.dataset.confirmed) {
        e.preventDefault();
        const msg = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin memproses data ini?';
        SirAlert.confirm({
          title: 'Konfirmasi !',
          text: msg,
          confirmButtonText: 'Ya, Lanjutkan !',
          cancelButtonText: 'Batal',
          isDanger: false,
          onConfirm: function () {
            form.dataset.confirmed = 'true';
            form.submit();
          }
        });
      }
    });
  });

  // Animasi Angka Berjalan (Count-Up Animation untuk Statistik Dashboard)
  initAnimatedCounters();
});

/**
 * Animated Number Counter (Random Hacker / Slot-Machine Shuffling Effect)
 * Angka teracak secara acak (scramble/shuffle) lalu mengunci (lock) ke angka target yang asli.
 */
function initAnimatedCounters() {
  const elements = document.querySelectorAll('.counter-animate');
  if (!elements.length) return;

  // 1. Inisialisasi awal pada saat halaman dimuat (efek bergelombang cepat)
  elements.forEach((el, index) => {
    setTimeout(() => {
      animateRandomShuffle(el);
    }, index * 40);
  });

  // 2. IntersectionObserver: memicu ulang animasi saat kartu di-scroll masuk ke viewport
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          // Animasikan kartu ketika masuk layar (jika belum sedang mengacak)
          if (!el._isShuffling) {
            animateRandomShuffle(el);
          }
          obs.unobserve(el);
        }
      });
    }, { threshold: 0.15 });

    elements.forEach(el => observer.observe(el));
  }

  // 3. Efek Interaktif: Saat mouse mengarahkan kursor (hover) ke kartu statistik, angka teracak kembali
  document.querySelectorAll('.card, .stat-card').forEach(card => {
    const counter = card.querySelector('.counter-animate');
    if (counter && !card._hasCounterHover) {
      card._hasCounterHover = true;
      card.addEventListener('mouseenter', () => {
        if (!counter._isShuffling) {
          animateRandomShuffle(counter, true);
        }
      });
    }
  });

  function animateRandomShuffle(el, isHover = false) {
    if (el._isShuffling) return;
    el._isShuffling = true;

    const raw = (el.getAttribute('data-target') || el.innerText || '').trim();
    const cleanNumber = raw.replace(/,/g, '');
    const target = parseFloat(cleanNumber);
    if (isNaN(target)) {
      el._isShuffling = false;
      return;
    }

    const isThousand = raw.includes(',') || target >= 1000;
    const finalFormatted = isThousand ? Math.round(target).toLocaleString('en-US') : Math.round(target).toString();
    const targetDigits = finalFormatted.split('');
    const len = targetDigits.length;

    const duration = isHover ? 900 : 1400; // Durasi acak (hover lebih cepat 0.9s, initial 1.4s)
    const startTime = performance.now();

    function getRandomChar(expectedChar) {
      if (expectedChar === ',' || expectedChar === '.' || expectedChar === '/' || expectedChar === ' ') {
        return expectedChar;
      }
      return Math.floor(Math.random() * 10).toString();
    }

    function frame(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);

      // Angka acak berputar cepat: dari progress 0 sampai 0.35 semua digit acak
      // Setelah progress > 0.35 digit mulai terkunci satu per satu dari kiri ke kanan
      let lockedCount = 0;
      if (progress > 0.35) {
        lockedCount = Math.floor(((progress - 0.35) / 0.65) * len);
      }

      let output = '';
      for (let i = 0; i < len; i++) {
        if (i < lockedCount || progress >= 1) {
          output += targetDigits[i];
        } else {
          output += getRandomChar(targetDigits[i]);
        }
      }

      el.innerText = output;

      if (progress < 1) {
        requestAnimationFrame(frame);
      } else {
        el.innerText = finalFormatted;
        el._isShuffling = false;
      }
    }

    requestAnimationFrame(frame);
  }
}

// Ekspos ke global window agar dapat dipanggil kembali kapan saja
window.initAnimatedCounters = initAnimatedCounters;

/**
 * SirAlert - Enterprise Popup Notification & Confirmation System
 * Matches official Government SIR-JP visual specification
 */
window.SirAlert = {
  // Alert Informasi (Presisi seperti di gambar)
  success: function (message, title = 'Informasi !', callback = null) {
    if (typeof Swal !== 'undefined') {
      return Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        showCloseButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#183B65',
      }).then(res => {
        if (callback && typeof callback === 'function') callback(res);
      });
    } else {
      alert(message);
      if (callback && typeof callback === 'function') callback();
    }
  },

  // Alert Gagal / Error
  error: function (message, title = 'Pemberitahuan !') {
    if (typeof Swal !== 'undefined') {
      return Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        showCloseButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#183B65',
      });
    } else {
      alert(message);
    }
  },

  // Alert Peringatan
  warning: function (message, title = 'Peringatan !') {
    if (typeof Swal !== 'undefined') {
      return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCloseButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#183B65',
      });
    } else {
      alert(message);
    }
  },

  // Alert Informasi Biasa
  info: function (message, title = 'Informasi !') {
    return this.success(message, title);
  },

  // Konfirmasi Dialog Iya / Tidak (Seperti yang diminta user)
  confirm: function (options) {
    const title = options.title || 'Konfirmasi !';
    const text = options.text || 'Apakah Anda yakin ingin melakukan tindakan ini?';
    const confirmText = options.confirmButtonText || 'Ya, Lanjutkan !';
    const cancelText = options.cancelButtonText || 'Batal';
    const isDanger = options.isDanger !== false;

    if (typeof Swal !== 'undefined') {
      return Swal.fire({
        title: title,
        text: text,
        icon: options.icon || (isDanger ? 'warning' : 'question'),
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: isDanger ? '#DC2626' : '#183B65',
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        focusCancel: true,
      }).then((result) => {
        if (result.isConfirmed) {
          if (typeof options.onConfirm === 'function') {
            options.onConfirm();
          }
        } else if (result.isDismissed) {
          if (typeof options.onCancel === 'function') {
            options.onCancel();
          }
        }
      });
    } else {
      if (window.confirm(text)) {
        if (typeof options.onConfirm === 'function') options.onConfirm();
      } else {
        if (typeof options.onCancel === 'function') options.onCancel();
      }
    }
  }
};

/**
 * Universal confirmation helper for delete buttons across all tables
 * Called via: onclick="confirmDelete('form-id', 'Hapus data...?')"
 */
function confirmDelete(formId, message) {
  SirAlert.confirm({
    title: 'Konfirmasi !',
    text: message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    confirmButtonText: 'Ya, Hapus !',
    cancelButtonText: 'Batal',
    isDanger: true,
    onConfirm: function () {
      const form = document.getElementById(formId);
      if (form) form.submit();
    }
  });
}
