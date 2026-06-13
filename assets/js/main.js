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

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.tab;
                // إيجاد الـ Wrapper الأب اللي بيحتوي على الـ Tabs والـ Content
                const parentCard = btn.closest('.card-body'); 
                
                // تنظيف الـ Active Classes
                tabContainer.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                parentCard.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                // تفعيل المختار
                btn.classList.add('active');
                const targetContent = parentCard.querySelector(`#tab-${target}`);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            });
        });
    });
}

// اتأكد إنك بتنادي الدالة دي بعد ما الصفحة تحمل
document.addEventListener('DOMContentLoaded', initTabs);

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
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('submissionFile');

    if (uploadZone && fileInput) {
        // إزالة أي listeners قديمة إذا لزم الأمر
        uploadZone.onclick = null; 
        
        uploadZone.addEventListener('click', function(e) {
            // نتحقق أن الضغط لم يكن على الـ input نفسه لتجنب التكرار
            if (e.target !== fileInput) {
                fileInput.click();
            }
        });
    }
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
document.addEventListener('DOMContentLoaded', initFileUpload);

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
/**
 * المطور: محمد وحيد
 * دالة موحدة لعرض شاشة حظر الهجمات الأمنية بشكل متناسق في كل الموقع
 */
function renderWafBlockScreen(wafData) {
    document.body.innerHTML = `
        <div style="background:#0d1117; color:#00d26a; padding:30px; font-family:monospace; font-size:16px; min-height:100vh; box-sizing:border-box; overflow:auto; display:flex; flex-direction:column; justify-content:center; align-items:center;">
            <div style="max-width: 800px; width: 100%; background: #161b22; padding: 30px; border-radius: 8px; border: 1px solid #30363d; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                <h2 style="color:#ff4a4a; border-bottom:1px solid #30363d; padding-bottom:15px; margin-top:0; font-size: 24px; display:flex; align-items:center; gap:10px;">
                    🚨 [AI SHIELD] SECURITY VIOLATION DETECTED
                </h2>
                <pre style="background:#0d1117; padding:20px; border-radius:6px; border:1px solid #21262d; color:#00d26a; line-height:1.6; font-size:15px; overflow-x:auto;">${JSON.stringify(wafData, null, 4)}</pre>
                <p style="color:#8b949e; font-size:14px; margin-top:20px; text-align:center; border-top: 1px solid #30363d; padding-top: 15px;">
                    Your IP address has been logged and reported to the system administrator.
                </p>
            </div>
        </div>`;
}
/**
 * Real-Time Notification System with Dropdown and View All

 */
document.addEventListener("DOMContentLoaded", function () {
    const notifBtn = document.getElementById('notificationBtn');
    const dropdown = document.getElementById('notificationDropdown');
    
    if (!notifBtn || !dropdown) return;

    // تشغيل وإغلاق القائمة المنسدلة عند الضغط على الجرس
    notifBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });

    // إغلاق القائمة لو اليوزر ضغط في أي حتة بره
    window.addEventListener('click', function (e) {
        if (!notifBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // جلب الإشعارات لايف وعرض أحدث 5 في الـ Dropdown
    async function fetchLiveNotifications() {
        try {
            const response = await fetch('/api/get_notifications.php');
            if (!response.ok) return;
            const data = await response.json();
            
            const countBadge = document.getElementById('notificationCount');
            const listContainer = document.getElementById('notificationList');
            
            if (data.count > 0) {
                countBadge.innerText = data.count;
                countBadge.style.display = 'inline-block';
                
                let htmlContent = data.list.map(notif => {
                    let badgeColor = notif.type === 'security' ? '#ff4a4a' : '#2563eb';
                    return `
                        <div style="padding: 12px; border-bottom: 1px solid #f1f5f9; cursor: pointer;">
                            <strong style="color: ${badgeColor}; display: block; margin-bottom: 2px;">${notif.title}</strong>
                            <span style="color: #475569; font-size: 13px; line-height:1.4; display:block;">${notif.message}</span>
                        </div>
                    `;
                }).join('');
                
                // إضافة زرار الـ View All أسفل القائمة
                htmlContent += `
                    <div style="padding: 10px; text-align: center; background: #f8f9fa; border-top: 1px solid #e9ecef; border-radius: 0 0 8px 8px;">
                        <a href="?section=notifications" style="color: #2563eb; text-decoration: none; font-weight: 600; font-size: 13px; display: block;">View All Notifications</a>
                    </div>
                `;
                listContainer.innerHTML = htmlContent;
            } else {
                countBadge.style.display = 'none';
                listContainer.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #94a3b8;">No new notifications</div>
                    <div style="padding: 10px; text-align: center; background: #f8f9fa; border-top: 1px solid #e9ecef; border-radius: 0 0 8px 8px;">
                        <a href="?section=notifications" style="color: #2563eb; text-decoration: none; font-weight: 600; font-size: 13px; display: block;">View Archive</a>
                    </div>
                `;
            }
        } catch (e) {
            console.error("Live notification polling failed.");
        }
    }

    fetchLiveNotifications();
    setInterval(fetchLiveNotifications, 10000); // لف كل 10 ثواني
});
function openNewsModal(id, title, content, image, category, date) {
    document.getElementById('modalImage').src = image;
    document.getElementById('modalImage').alt = category;
    document.getElementById('modalCategory').textContent = category;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDate').textContent = date;
    document.getElementById('modalContent').textContent = content;
    document.getElementById('newsModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeNewsModal() {
    document.getElementById('newsModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('newsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNewsModal();
    }
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNewsModal();
    }
});