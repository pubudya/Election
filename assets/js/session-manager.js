/**
 * Client-side Session Manager
 * Handles session timeout warnings, auto-logout, and session extension
 */

class SessionManager {
    constructor() {
        this.warningTime = 5 * 60 * 1000; // 5 minutes before expiry
        this.checkInterval = 30 * 1000; // Check every 30 seconds
        this.warningShown = false;
        this.sessionTimeout = null;
        this.warningTimeout = null;
        this.checkIntervalId = null;
        
        this.init();
    }
    
    init() {
        // Start monitoring
        this.startMonitoring();
        
        // Set up activity listeners
        this.setupActivityListeners();
        
        // Check if session is about to expire
        this.checkSessionExpiry();
        
        // Set up periodic checks
        this.setupPeriodicChecks();
    }
    
    startMonitoring() {
        // Check session status every 30 seconds
        this.checkIntervalId = setInterval(() => {
            this.checkSessionStatus();
        }, this.checkInterval);
    }
    
    setupActivityListeners() {
        // Extend session on user activity
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.extendSession();
            }, { passive: true });
        });
        
        // Extend session on page visibility change
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.extendSession();
            }
        });
    }
    
    setupPeriodicChecks() {
        // Check session status every minute
        setInterval(() => {
            this.checkSessionExpiry();
        }, 60000);
    }
    
    async checkSessionStatus() {
        try {
            const response = await fetch('/api/session/status', {
                method: 'GET',
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (!data.authenticated) {
                    this.handleSessionExpired();
                    return;
                }
                
                // Update session info
                this.updateSessionInfo(data);
                
                // Check if session is expiring soon
                if (data.time_left <= this.warningTime && !this.warningShown) {
                    this.showExpiryWarning(data.time_left);
                }
            }
        } catch (error) {
            console.error('Error checking session status:', error);
        }
    }
    
    async extendSession() {
        try {
            const response = await fetch('/api/session/extend', {
                method: 'POST',
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateSessionInfo(data.session_info);
                }
            }
        } catch (error) {
            console.error('Error extending session:', error);
        }
    }
    
    checkSessionExpiry() {
        // Check if there's a session expiry warning to show
        if (window.showExpiryWarning) {
            this.showExpiryWarning();
        }
    }
    
    showExpiryWarning(timeLeft = null) {
        if (this.warningShown) return;
        
        this.warningShown = true;
        
        // Create warning modal
        const modal = this.createWarningModal(timeLeft);
        document.body.appendChild(modal);
        
        // Show modal
        setTimeout(() => {
            modal.classList.add('show');
        }, 100);
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            this.hideWarningModal(modal);
        }, 10000);
    }
    
    createWarningModal(timeLeft) {
        const modal = document.createElement('div');
        modal.className = 'session-warning-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="warning-icon">⚠️</div>
                <h3>Session Expiring Soon</h3>
                <p>Your session will expire in ${timeLeft ? Math.ceil(timeLeft / 60000) : 'a few'} minutes.</p>
                <p>Click anywhere to extend your session.</p>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="sessionManager.extendSessionAndHide()">
                        Extend Session
                    </button>
                    <button class="btn btn-secondary" onclick="sessionManager.logout()">
                        Logout Now
                    </button>
                </div>
            </div>
        `;
        
        // Close on click outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.hideWarningModal(modal);
            }
        });
        
        return modal;
    }
    
    hideWarningModal(modal) {
        if (modal && modal.parentNode) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.parentNode.removeChild(modal);
            }, 300);
        }
    }
    
    async extendSessionAndHide() {
        await this.extendSession();
        this.warningShown = false;
        
        // Hide all warning modals
        const modals = document.querySelectorAll('.session-warning-modal');
        modals.forEach(modal => this.hideWarningModal(modal));
    }
    
    updateSessionInfo(sessionInfo) {
        // Update any session info displays on the page
        const sessionElements = document.querySelectorAll('[data-session-info]');
        sessionElements.forEach(element => {
            const infoType = element.dataset.sessionInfo;
            if (sessionInfo[infoType] !== undefined) {
                element.textContent = sessionInfo[infoType];
            }
        });
        
        // Update time remaining displays
        const timeElements = document.querySelectorAll('[data-time-remaining]');
        timeElements.forEach(element => {
            if (sessionInfo.time_left) {
                const minutes = Math.ceil(sessionInfo.time_left / 60);
                element.textContent = `${minutes} minute${minutes !== 1 ? 's' : ''}`;
            }
        });
    }
    
    handleSessionExpired() {
        // Clear any existing warnings
        this.warningShown = false;
        
        // Show session expired message
        this.showSessionExpiredMessage();
        
        // Redirect to login after 3 seconds
        setTimeout(() => {
            window.location.href = '/login.php?expired=1';
        }, 3000);
    }
    
    showSessionExpiredMessage() {
        const message = document.createElement('div');
        message.className = 'session-expired-message';
        message.innerHTML = `
            <div class="message-content">
                <h3>Session Expired</h3>
                <p>Your session has expired. You will be redirected to the login page.</p>
            </div>
        `;
        
        document.body.appendChild(message);
        
        // Show message
        setTimeout(() => {
            message.classList.add('show');
        }, 100);
    }
    
    async logout() {
        try {
            const response = await fetch('/logout.php', {
                method: 'POST',
                credentials: 'same-origin'
            });
            
            // Redirect to logout page
            window.location.href = '/logout.php';
        } catch (error) {
            console.error('Error during logout:', error);
            // Fallback redirect
            window.location.href = '/logout.php';
        }
    }
    
    destroy() {
        // Clean up intervals
        if (this.checkIntervalId) {
            clearInterval(this.checkIntervalId);
        }
        
        if (this.warningTimeout) {
            clearTimeout(this.warningTimeout);
        }
        
        if (this.sessionTimeout) {
            clearTimeout(this.sessionTimeout);
        }
    }
}

// Initialize session manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.sessionManager = new SessionManager();
});

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (window.sessionManager) {
        window.sessionManager.destroy();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SessionManager;
}
