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
  // Animasi Angka Berjalan (Count-Up Animation untuk Statistik Dashboard)
  initAnimatedCounters();
});

/**
 * Animated Number Counter (Count-Up Odometer Engine)
 * Smooth easing cubic count-up effect for KPI numbers and stat cards
 */
function initAnimatedCounters() {
  const elements = document.querySelectorAll('.counter-animate');
  if (!elements.length) return;

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounterElement(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  elements.forEach(el => observer.observe(el));

  function animateCounterElement(el) {
    const raw = (el.getAttribute('data-target') || el.innerText || '').trim();
    const cleanNumber = raw.replace(/,/g, '');
    const target = parseFloat(cleanNumber);
    if (isNaN(target)) return;

    const isThousand = raw.includes(',') || target >= 1000;
    const duration = 1250; // Durasi animasi 1.25 detik (sangat halus)
    const startTime = performance.now();

    function step(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      // Easing cubic out: cepat di awal lalu melambat halus saat mendekati target
      const ease = 1 - Math.pow(1 - progress, 3);
      const current = Math.floor(target * ease);

      el.innerText = isThousand ? current.toLocaleString('en-US') : current;

      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.innerText = isThousand ? Math.round(target).toLocaleString('en-US') : Math.round(target);
      }
    }

    requestAnimationFrame(step);
  }
}

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
