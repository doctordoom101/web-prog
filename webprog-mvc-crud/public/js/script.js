// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                const value = field.value.trim();
                const fieldGroup = field.closest('.form-group');
                
                // Remove existing error messages
                const existingError = fieldGroup.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                // Validate field
                if (!value) {
                    isValid = false;
                    showFieldError(field, 'Field ini wajib diisi');
                } else if (field.type === 'email' && !isValidEmail(value)) {
                    isValid = false;
                    showFieldError(field, 'Format email tidak valid');
                } else if (field.type === 'tel' && !isValidPhone(value)) {
                    isValid = false;
                    showFieldError(field, 'Format nomor telepon tidak valid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Real-time validation
    const inputFields = document.querySelectorAll('.form-control');
    inputFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            const fieldGroup = this.closest('.form-group');
            const errorMessage = fieldGroup.querySelector('.error-message');
            if (errorMessage) {
                errorMessage.remove();
            }
            this.style.borderColor = '#e9ecef';
        });
    });

    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
    });

    // Table row hover effects
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Button loading state
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        if (button.type === 'submit') {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                if (form && form.checkValidity()) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    this.disabled = true;
                }
            });
        }
    });

    // Confirmation dialogs
    const deleteLinks = document.querySelectorAll('[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const modal = createConfirmModal(
                'Konfirmasi Hapus',
                'Apakah Anda yakin ingin menghapus user ini?',
                () => {
                    window.location.href = this.href;
                }
            );
            
            document.body.appendChild(modal);
        });
    });
});

// Helper functions
function showFieldError(field, message) {
    const fieldGroup = field.closest('.form-group');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    fieldGroup.appendChild(errorDiv);
    field.style.borderColor = '#dc3545';
}

function validateField(field) {
    const value = field.value.trim();
    const fieldGroup = field.closest('.form-group');
    
    // Remove existing error messages
    const existingError = fieldGroup.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Validate based on field type
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'Field ini wajib diisi');
        return false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Format email tidak valid');
        return false;
    }
    
    if (field.type === 'tel' && value && !isValidPhone(value)) {
        showFieldError(field, 'Format nomor telepon tidak valid');
        return false;
    }
    
    field.style.borderColor = '#28a745';
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]{10,}$/;
    return phoneRegex.test(phone);
}

function createConfirmModal(title, message, onConfirm) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    `;
    
    modal.innerHTML = `
        <div style="
            background: white;
            padding: 2rem;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        ">
            <h3 style="margin-bottom: 1rem; color: #333;">${title}</h3>
            <p style="margin-bottom: 2rem; color: #666;">${message}</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button class="btn btn-danger" onclick="confirmAction()">
                    <i class="fas fa-check"></i> Ya, Hapus
                </button>
                <button class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </div>
    `;
    
    // Add event listeners
    window.confirmAction = function() {
        onConfirm();
        modal.remove();
    };
    
    window.closeModal = function() {
        modal.remove();
    };
    
    // Close on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
    
    return modal;
}

// Utility functions for better UX
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        max-width: 300px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animate out
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Theme toggle (optional enhancement)
function toggleTheme() {
    const body = document.body;
    const isDark = body.classList.toggle('dark-theme');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// Load saved theme
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    document.body.classList.add('dark-theme');
}