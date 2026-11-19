document.addEventListener('DOMContentLoaded', function () {
  // Sidebar Toggle Desktop & Mobile
  const toggleBtn = document.getElementById('sidebarToggleBtn');
  const sidebar = document.querySelector('.app-sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      if (window.innerWidth < 992) {
        sidebar.classList.toggle('show-mobile');
        if (backdrop) backdrop.classList.toggle('show');
      } else {
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(
          'sidebarCollapsed',
          document.body.classList.contains('sidebar-collapsed') ? 'true' : 'false'
        );
      }
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', function () {
      sidebar.classList.remove('show-mobile');
      backdrop.classList.remove('show');
    });
  }

  // Restore sidebar preference
  if (window.innerWidth >= 992 && localStorage.getItem('sidebarCollapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
  }

  // Auto-hide alert messages after 5 seconds
  const autoAlerts = document.querySelectorAll('.alert-dismissible');
  autoAlerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      if (bsAlert) {
        bsAlert.close();
      }
    }, 5000);
  });
});

// Confirmation helper for delete buttons
function confirmDelete(formId, message) {
  if (confirm(message || 'Apakah Anda yakin ingin menghapus data ini?')) {
    document.getElementById(formId).submit();
  }
}
