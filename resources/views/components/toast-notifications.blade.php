{{-- Toast Notification Styles --}}
<style>
    /* Toast Notification Container */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
    }

    /* Toast Notification */
    .toast {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 16px 24px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        min-width: 300px;
        max-width: 400px;
        animation: slideIn 0.3s ease-out;
        opacity: 1;
    }

    /* Toast Types */
    .toast-success {
        border-left: 4px solid #28a745;
    }

    .toast-error {
        border-left: 4px solid #dc3545;
    }

    .toast-warning {
        border-left: 4px solid #ffc107;
    }

    .toast-info {
        border-left: 4px solid #17a2b8;
    }

    /* Toast Icon */
    .toast-icon {
        margin-right: 12px;
        font-size: 20px;
    }

    .toast-success .toast-icon {
        color: #28a745;
    }

    .toast-error .toast-icon {
        color: #dc3545;
    }

    .toast-warning .toast-icon {
        color: #ffc107;
    }

    .toast-info .toast-icon {
        color: #17a2b8;
    }

    /* Toast Content */
    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .toast-message {
        color: #666;
        font-size: 14px;
    }

    /* Toast Animations */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Custom Sweet Alert Styles */
    .custom-sweet-alert-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease-out;
    }

    .custom-sweet-alert {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        max-width: 800px;
        width: 95%;
        max-height: 85vh;
        overflow-y: auto;
        animation: slideInUp 0.3s ease-out;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .custom-sweet-alert-header {
        color: #374151;
        padding: 25px 30px;
        border-radius: 16px 16px 0 0;
        display: flex;
        align-items: center;
        gap: 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .custom-sweet-alert-icon {
        font-size: 32px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .custom-sweet-alert-title {
        font-size: 28px;
        font-weight: 600;
        margin: 0;
        flex: 1;
        letter-spacing: -0.5px;
        color: #1f2937;
    }

    .custom-sweet-alert-body {
        padding: 30px;
        text-align: left;
    }

    .custom-sweet-alert-message {
        font-size: 18px;
        color: #374151;
        margin-bottom: 25px;
        line-height: 1.6;
        font-weight: 500;
    }

    .custom-sweet-alert-stats {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .custom-sweet-alert-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
    }

    .custom-sweet-alert-stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 15px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.1);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .custom-sweet-alert-stat-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        background: rgba(255, 255, 255, 0.95);
    }

    .custom-sweet-alert-stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .custom-sweet-alert-stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .custom-sweet-alert-footer {
        padding: 25px 30px;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
        display: flex;
        justify-content: center;
        gap: 20px;
        background: rgba(248, 250, 252, 0.5);
    }

    .custom-sweet-alert-btn {
        padding: 14px 35px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 140px;
    }

    .custom-sweet-alert-btn-primary {
        color: white;
    }

    .custom-sweet-alert-btn-primary:hover {
        transform: translateY(-2px);
    }

    .custom-sweet-alert-btn-secondary {
        background: #6c757d;
        color: white;
    }

    .custom-sweet-alert-btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }

    .custom-sweet-alert-close {
        position: absolute;
        top: 15px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }

    .custom-sweet-alert-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideInUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideOutDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(50px);
            opacity: 0;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .custom-sweet-alert {
            width: 98%;
            margin: 10px;
            max-width: none;
        }
        
        .custom-sweet-alert-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .custom-sweet-alert-title {
            font-size: 24px;
        }
        
        .custom-sweet-alert-body {
            padding: 25px 20px;
        }
        
        .custom-sweet-alert-header {
            padding: 20px 25px;
        }
        
        .custom-sweet-alert-icon {
            width: 50px;
            height: 50px;
            font-size: 28px;
        }
        
        .custom-sweet-alert-stat-value {
            font-size: 24px;
        }
        
        .custom-sweet-alert-stat-label {
            font-size: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .custom-sweet-alert-stats-grid {
            grid-template-columns: 1fr;
        }
        
        .custom-sweet-alert-title {
            font-size: 22px;
        }
        
        .custom-sweet-alert-message {
            font-size: 16px;
        }
    }
</style>

{{-- Toast Notification JavaScript --}}
<script type="text/javascript">
    /**
     * Shows a toast notification
     * @param {string} type - The type of toast (success, error, warning, info)
     * @param {string} message - The message to display
     * @param {string} title - Optional title for the toast
     * @param {number} duration - Time in milliseconds to show the toast (default: 5000ms)
     */
    function showToast(type, message, title = '', duration = 5000) {
        // Create container if it doesn't exist
        const container = document.querySelector('.toast-container') || createToastContainer();
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Set default titles based on type if not provided
        if (!title) {
            title = type === 'success' ? 'Success!' : 
                   type === 'error' ? 'Error!' : 
                   type === 'warning' ? 'Warning!' : 
                   type === 'info' ? 'Information' : '';
        }

        // Set icon based on type
        const icon = type === 'success' ? '✓' : 
                    type === 'error' ? '✕' : 
                    type === 'warning' ? '⚠' : 
                    type === 'info' ? 'ℹ' : '';

        // Build toast HTML
        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
        `;

        // Add to container
        container.appendChild(toast);

        // Set duration based on type - longer duration for errors
        let finalDuration = duration;
        if (type === 'error') {
            finalDuration = 10000; // 10 seconds for errors
        } else if (type === 'warning') {
            finalDuration = 8000;  // 8 seconds for warnings
        } else if (type === 'success') {
            finalDuration = 5000;  // 5 seconds for success
        } else {
            finalDuration = 6000;  // 6 seconds for info
        }

        // Auto remove after duration
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, finalDuration);
        
        // Return the toast element in case it needs to be manipulated
        return toast;
    }

    /**
     * Creates the toast container
     * @returns {HTMLElement} The created container
     */
    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Get color scheme based on alert type
     * @param {string} type - The type of alert
     * @returns {Object} Color scheme object
     */
    function getColorScheme(type) {
        const schemes = {
            // Professional white header with colored icons
            info: {
                headerGradient: 'linear-gradient(135deg, #ffffff, #f8f9fa)',
                iconBg: 'rgba(13, 110, 253, 0.1)',
                iconColor: '#0d6efd',
                icon: 'fa-info-circle',
                buttonGradient: 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
                buttonShadow: '0 4px 15px rgba(13, 110, 253, 0.2)'
            },
            // Warning colors - white header
            warning: {
                headerGradient: 'linear-gradient(135deg, #ffffff, #f8f9fa)',
                iconBg: 'rgba(255, 193, 7, 0.1)',
                iconColor: '#ffc107',
                icon: 'fa-exclamation-triangle',
                buttonGradient: 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
                buttonShadow: '0 4px 15px rgba(13, 110, 253, 0.2)'
            },
            // Error colors - white header
            error: {
                headerGradient: 'linear-gradient(135deg, #ffffff, #f8f9fa)',
                iconBg: 'rgba(220, 53, 69, 0.1)',
                iconColor: '#dc3545',
                icon: 'fa-times-circle',
                buttonGradient: 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
                buttonShadow: '0 4px 15px rgba(13, 110, 253, 0.2)'
            },
            // Success colors - white header
            success: {
                headerGradient: 'linear-gradient(135deg, #ffffff, #f8f9fa)',
                iconBg: 'rgba(25, 135, 84, 0.1)',
                iconColor: '#198754',
                icon: 'fa-check-circle',
                buttonGradient: 'linear-gradient(135deg, #0d6efd, #0b5ed7)',
                buttonShadow: '0 4px 15px rgba(13, 110, 253, 0.2)'
            }
        };
        
        return schemes[type] || schemes.warning; // Default to warning if type not found
    }

    /**
     * Shows a custom Sweet Alert for hostel validation errors
     * @param {Object} response - The response object from the server
     * @param {string} type - The type of alert (warning, info, success, error)
     */
    function showHostelValidationError(response, type = 'warning') {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'custom-sweet-alert-overlay';
        
        // Get color scheme based on type
        const colorScheme = getColorScheme(type);
        
        // Create alert HTML
        overlay.innerHTML = `
            <div class="custom-sweet-alert">
                <button class="custom-sweet-alert-close" onclick="closeHostelValidationError()">&times;</button>
                
                <div class="custom-sweet-alert-header" style="background: ${colorScheme.headerGradient};">
                    <div class="custom-sweet-alert-icon" style="background: ${colorScheme.iconBg};">
                        <i class="fa ${colorScheme.icon}" style="color: ${colorScheme.iconColor};"></i>
                    </div>
                    <h3 class="custom-sweet-alert-title">Hostel Seats Full!</h3>
                </div>
                
                <div class="custom-sweet-alert-body">
                    <div class="custom-sweet-alert-message">
                        <strong>${response.message}</strong>
                    </div>
                    
                    <div class="custom-sweet-alert-stats">
                        <div class="custom-sweet-alert-stats-grid">
                            <div class="custom-sweet-alert-stat-item">
                                <div class="custom-sweet-alert-stat-label">Available Seats</div>
                                <div class="custom-sweet-alert-stat-value">${response.availableSeats}</div>
                            </div>
                            <div class="custom-sweet-alert-stat-item">
                                <div class="custom-sweet-alert-stat-label">Total Allowed</div>
                                <div class="custom-sweet-alert-stat-value">${response.totalAllowed}</div>
                            </div>
                            <div class="custom-sweet-alert-stat-item">
                                <div class="custom-sweet-alert-stat-label">Applicants Allowed</div>
                                <div class="custom-sweet-alert-stat-value">${response.newAllowed}</div>
                            </div>
                            <div class="custom-sweet-alert-stat-item">
                                <div class="custom-sweet-alert-stat-label">Students Allowed</div>
                                <div class="custom-sweet-alert-stat-value">${response.oldAllowed}</div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="custom-sweet-alert-footer">
                    <button class="custom-sweet-alert-btn custom-sweet-alert-btn-primary" onclick="closeHostelValidationError()" style="background: ${colorScheme.buttonGradient}; box-shadow: ${colorScheme.buttonShadow};">
                        <i class="fa fa-check mr-2"></i>OK
                    </button>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(overlay);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Close on overlay click
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeHostelValidationError();
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeHostelValidationError();
            }
        });
    }

    /**
     * Closes the hostel validation error alert
     */
    function closeHostelValidationError() {
        const overlay = document.querySelector('.custom-sweet-alert-overlay');
        if (overlay) {
            overlay.style.animation = 'slideOutDown 0.3s ease-out';
            setTimeout(() => {
                overlay.remove();
                document.body.style.overflow = '';
            }, 300);
        }
    }
</script> 