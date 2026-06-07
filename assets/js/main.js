/**
 * Faculty of Engineering at Shubra
 * Benha University - Main JavaScript
 */

// ========== SIDEBAR TOGGLE ==========
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('open');
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(e) {
    const sidebar = document.querySelector('.sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// ========== TAB SWITCHING ==========
function initTabs() {
    document.querySelectorAll('.tabs').forEach(tabContainer => {
        const buttons = tabContainer.querySelectorAll('.tab-btn');
        const contents = tabContainer.parentElement.querySelectorAll('.tab-content');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.tab;

                buttons.forEach(b => b.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                btn.classList.add('active');
                const targetContent = tabContainer.parentElement.querySelector(`#tab-${target}`);
                if (targetContent) targetContent.classList.add('active');
            });
        });
    });
}

// ========== MODAL ==========
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// ========== CONFIRM DELETE ==========
function confirmDelete(message, callback) {
    if (confirm(message || 'Are you sure you want to delete this item?')) {
        callback();
    }
}

// ========== AJAX HELPER ==========
async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {}
    };

    if (data) {
        if (data instanceof FormData) {
            options.body = data;
        } else {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }
    }

    const response = await fetch(url, options);
    return response.json();
}

// ========== NOTIFICATIONS ==========
function showNotification(message, type = 'info') {
    const container = document.getElementById('notificationContainer') || document.body;
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} fade-in`;
    alert.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;box-shadow:0 4px 12px rgba(0,0,0,0.15);';

    const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : type === 'warning' ? '⚠' : 'ℹ';
    alert.innerHTML = `<span class="alert-icon">${icon}</span> ${message}`;

    container.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 300);
    }, 4000);
}

// ========== QUIZ TIMER ==========
class QuizTimer {
    constructor(durationMinutes, displayElement, onExpire) {
        this.totalSeconds = durationMinutes * 60;
        this.remainingSeconds = this.totalSeconds;
        this.displayElement = displayElement;
        this.onExpire = onExpire;
        this.interval = null;
    }

    start() {
        this.updateDisplay();
        this.interval = setInterval(() => {
            this.remainingSeconds--;
            this.updateDisplay();

            if (this.remainingSeconds <= 300) {
                this.displayElement.classList.add('warning');
            }
            if (this.remainingSeconds <= 60) {
                this.displayElement.classList.remove('warning');
                this.displayElement.classList.add('danger');
            }

            if (this.remainingSeconds <= 0) {
                this.stop();
                if (this.onExpire) this.onExpire();
            }
        }, 1000);
    }

    stop() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    }

    updateDisplay() {
        const mins = Math.floor(this.remainingSeconds / 60);
        const secs = this.remainingSeconds % 60;
        this.displayElement.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    getRemainingSeconds() {
        return this.remainingSeconds;
    }
}

// ========== FILE UPLOAD ==========
function initFileUpload() {
    document.querySelectorAll('.upload-zone').forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        const fileList = zone.closest('form')?.querySelector('.file-list');

        zone.addEventListener('click', () => input?.click());

        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('dragover');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('dragover');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('dragover');
            if (input && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                updateFileList(e.dataTransfer.files, fileList);
            }
        });

        input?.addEventListener('change', () => {
            if (input.files.length) {
                updateFileList(input.files, fileList);
            }
        });
    });
}

function updateFileList(files, container) {
    if (!container) return;
    container.innerHTML = '';
    Array.from(files).forEach(file => {
        const div = document.createElement('div');
        div.className = 'file-item';
        div.innerHTML = `
            <span>&#128196; ${file.name}</span>
            <span style="color:var(--gray);font-size:0.85rem;">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
        `;
        container.appendChild(div);
    });
}

// ========== SEARCH TABLE ==========
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    input.addEventListener('input', () => {
        const filter = input.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

// ========== SORT TABLE ==========
function sortTable(tableId, columnIndex) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const isNumeric = rows.every(row => !isNaN(parseFloat(row.cells[columnIndex].textContent)));

    rows.sort((a, b) => {
        const aVal = a.cells[columnIndex].textContent.trim();
        const bVal = b.cells[columnIndex].textContent.trim();

        if (isNumeric) {
            return parseFloat(aVal) - parseFloat(bVal);
        }
        return aVal.localeCompare(bVal);
    });

    rows.forEach(row => tbody.appendChild(row));
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initFileUpload();
});

// ========== LOGOUT ==========
async function logout() {
    try {
        const formData = new FormData();
        formData.append('action', 'logout');

        await fetch('/api/auth.php', {
            method: 'POST',
            body: formData
        });
    } catch (e) {
        console.error('Logout error:', e);
    }
    window.location.href = '/login.php';
}
