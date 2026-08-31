(function () {
  'use strict'
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
})()
function toggleSelectMode() {
    const toolbar = document.getElementById('bulkToolbar');
    const btn = document.getElementById('toggleSelectBtn');
    const isOn = !toolbar.classList.contains('select-mode');

    toolbar.classList.toggle('select-mode', isOn);
    btn.classList.toggle('active', isOn);
    btn.innerHTML = isOn
        ? '<i class="fa-solid fa-xmark"></i> Cancel'
        : '<i class="fa-solid fa-square-check"></i> Select';

    document.querySelectorAll('.col-checkbox').forEach(el => el.classList.toggle('show', isOn));

    if (!isOn) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAllCheckbox').checked = false;
        updateSelectedCount();
    }
}

function toggleSelectAll(source) {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = source.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.row-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count > 0 ? count + ' selected' : '';
}

function applyBulkStatus() {
    const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) {
        alert('Please select at least one lead first.');
        return;
    }
    const status = document.getElementById('bulkStatusSelect').value;

    fetch('update-lead-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ids: ids, status: status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            ids.forEach(id => {
                const badge = document.querySelector('.status-badge[data-id="' + id + '"]');
                if (badge) {
                    badge.className = 'status-badge status-' + status;
                    badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                }
            });
            toggleSelectMode();
        } else {
            alert(data.message || 'Status could not be updated.');
        }
    })
    .catch(err => alert('Request failed: ' + err.message));
}
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('sidebar-open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}

function closeSidebar() {
    document.getElementById('adminSidebar').classList.remove('sidebar-open');
    document.getElementById('sidebarOverlay').classList.remove('show');
}
function toggleSidebarGroup(btn) {
    const group = btn.closest('.sidebar-group');
    group.classList.toggle('open');
}