/**
 * Polling Utility - Auto-refresh orders without page reload
 */

export const OrderPoller = {
    intervalId: null,
    pollInterval: 5000, // 5 seconds

    /**
     * Start polling for order updates
     * @param {string} userId - The user ID (cook or client)
     * @param {string} endpoint - The API endpoint to poll
     * @param {function} onUpdate - Callback when data updates
     */
    start(endpoint, onUpdate) {
        // Poll immediately on start
        this.poll(endpoint, onUpdate);

        // Then poll every 5 seconds
        this.intervalId = setInterval(() => {
            this.poll(endpoint, onUpdate);
        }, this.pollInterval);

        console.log(`Polling started for ${endpoint}`);
    },

    /**
     * Stop polling
     */
    stop() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
            console.log('Polling stopped');
        }
    },

    /**
     * Make a single poll request
     */
    async poll(endpoint, onUpdate) {
        try {
            const response = await fetch(endpoint, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) {
                console.error(`Polling failed: ${response.statusText}`);
                return;
            }

            const data = await response.json();
            onUpdate(data);
        } catch (error) {
            console.error('Polling error:', error);
        }
    }
};

/**
 * Toast Notification Utility
 */
export const Toaster = {
    /**
     * Show a toast notification
     */
    show(message, type = 'info', duration = 5000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;

        // Create style if not already present
        if (!document.querySelector('#toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.innerHTML = `
                .toast {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 6px;
                    color: white;
                    font-weight: 500;
                    animation: slideIn 0.3s ease-out;
                    z-index: 9999;
                }
                .toast-success { background-color: #4caf50; }
                .toast-error { background-color: #f44336; }
                .toast-warning { background-color: #ff9800; }
                .toast-info { background-color: #2196f3; }
                @keyframes slideIn {
                    from {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(toast);

        // Remove after duration
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out forwards';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
};
