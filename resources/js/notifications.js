class NotificationHandler {
    constructor() {
        this.pollingInterval = 30000;
        this.pollingTimer = null;
        this.notificationBell = null;
        this.notificationCount = null;
        this.notificationPanel = null;

        this.init();
    }

    init() {
        this.setupElements();
        this.setupEventListeners();
        this.startPolling();
        this.loadNotifications();
    }

    setupElements() {
        this.notificationBell = document.getElementById('notification-bell');
        this.notificationCount = document.getElementById('notification-count');
        this.notificationPanel = document.getElementById('notification-panel');
    }

    setupEventListeners() {
        if (this.notificationBell) {
            this.notificationBell.addEventListener('click', (e) => {
                e.stopPropagation();
                this.togglePanel();
            });
        }

        document.addEventListener('click', (e) => {
            if (this.notificationPanel && !this.notificationPanel.contains(e.target)) {
                this.closePanel();
            }
        });

        document.addEventListener('click', (e) => {
            if (e.target.closest('.mark-read-btn')) {
                const notificationId = e.target.closest('.mark-read-btn').dataset.id;
                this.markAsRead(notificationId);
            }
        });

        const markAllBtn = document.getElementById('mark-all-read');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.markAllAsRead());
        }
    }

    togglePanel() {
        if (this.notificationPanel) {
            this.notificationPanel.classList.toggle('hidden');
        }
    }

    closePanel() {
        if (this.notificationPanel) {
            this.notificationPanel.classList.add('hidden');
        }
    }

    async loadNotifications() {
        try {
            const response = await fetch('/admin/notifications', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to load notifications');

            const data = await response.json();
            this.updateNotifications(data);

        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    updateNotifications(data) {
        if (this.notificationCount) {
            const unreadCount = data.unread_count || 0;

            if (unreadCount > 0) {
                this.notificationCount.textContent = unreadCount > 99 ? '99+' : unreadCount;
                this.notificationCount.classList.remove('hidden');
            } else {
                this.notificationCount.classList.add('hidden');
            }
        }

        this.renderNotificationList(data.notifications || []);
    }

    renderNotificationList(notifications) {
        const listContainer = document.getElementById('notification-list');
        if (!listContainer) return;

        if (notifications.length === 0) {
            listContainer.innerHTML = `
                <div class="p-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm font-medium">Tidak ada notifikasi</p>
                </div>
            `;
            return;
        }

        const notificationHTML = notifications.map(notif => this.renderNotificationItem(notif)).join('');
        listContainer.innerHTML = notificationHTML;
    }

    renderNotificationItem(notification) {
        const icon = this.getNotificationIcon(notification.type);
        const bgClass = notification.is_read ? 'bg-white' : 'bg-blue-50';
        const timeAgo = this.formatTimeAgo(notification.created_at);

        return `
            <div class="${bgClass} border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        ${icon}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 mb-1">
                            ${notification.title}
                        </p>
                        <p class="text-sm text-gray-600 mb-2">
                            ${notification.message}
                        </p>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                ${timeAgo}
                            </span>
                            ${notification.document_code ? `
                                <span class="font-mono font-medium text-blue-600">
                                    ${notification.document_code}
                                </span>
                            ` : ''}
                        </div>
                    </div>
                    ${!notification.is_read ? `
                        <button class="mark-read-btn flex-shrink-0 text-blue-600 hover:text-blue-800"
                                data-id="${notification.id}"
                                title="Tandai sudah dibaca">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }

    getNotificationIcon(type) {
        const icons = {
            'document_submitted': `
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            `,
            'document_approved': `
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            `,
            'document_rejected': `
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            `,
            'verification_requested': `
                <div class="p-2 bg-amber-100 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            `,
            'verification_approved': `
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            `,
            'verification_rejected': `
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            `,
            'signature_requested': `
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
            `,
            'signature_uploaded': `
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
            `,
            'signature_verified': `
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            `,
            'signature_rejected': `
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            `,
            'document_ready': `
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            `
        };

        return icons[type] || icons['document_submitted'];
    }

    formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        const intervals = {
            tahun: 31536000,
            bulan: 2592000,
            minggu: 604800,
            hari: 86400,
            jam: 3600,
            menit: 60
        };

        for (const [name, secondsInInterval] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInInterval);
            if (interval >= 1) {
                return `${interval} ${name} lalu`;
            }
        }

        return 'Baru saja';
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to mark as read');

            await this.loadNotifications();

        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to mark all as read');

            await this.loadNotifications();

        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }

    startPolling() {
        this.pollingTimer = setInterval(() => {
            this.loadNotifications();
        }, this.pollingInterval);
    }

    stopPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
            this.pollingTimer = null;
        }
    }

    showToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm animate-slide-in z-50';

        const icon = this.getNotificationIcon(notification.type);

        toast.innerHTML = `
            <div class="flex items-start gap-3">
                ${icon}
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900 mb-1">${notification.title}</h4>
                    <p class="text-sm text-gray-600">${notification.message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('animate-slide-out');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('notification-bell')) {
        window.notificationHandler = new NotificationHandler();
    }
});

window.addEventListener('beforeunload', function() {
    if (window.notificationHandler) {
        window.notificationHandler.stopPolling();
    }
});
