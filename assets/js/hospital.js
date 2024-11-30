// Main initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    setupDashboardInteractions();
    initializeToastContainer();
});

// Initialize all event listeners
function initializeEventListeners() {
    // Navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', handleNavigation);
    });

    // Search
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearch, 300));
    }

    // Request actions
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', handleViewRequest);
    });

    document.querySelectorAll('.respond-btn').forEach(btn => {
        btn.addEventListener('click', handleRespond);
    });

    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', handleApprove);
    });

    // Form submissions
    const responseForm = document.getElementById('responseForm');
    if (responseForm) {
        responseForm.addEventListener('submit', handleResponseSubmit);
    }

    // Modal close buttons
    document.querySelectorAll('.close-modal, .cancel-btn').forEach(btn => {
        btn.addEventListener('click', () => closeModal());
    });

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            closeModal();
        }
    });

    // Notifications
    const notificationBtn = document.querySelector('.notifications');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', handleNotifications);
    }
}

// Setup dashboard interactions and animations
function setupDashboardInteractions() {
    // Stats cards hover effects
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.transition = 'transform 0.3s ease';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });

    // Request cards hover effects
    const requestCards = document.querySelectorAll('.request-card');
    requestCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateX(5px)';
            card.style.transition = 'transform 0.3s ease';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateX(0)';
        });
    });
}

// Event Handlers
function handleNavigation(e) {
    e.preventDefault();
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => item.classList.remove('active'));
    this.classList.add('active');

    const href = this.getAttribute('href');
    if (href !== '#') {
        window.location.href = href;
    }
}

function handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    const requestCards = document.querySelectorAll('.request-card');

    requestCards.forEach(card => {
        const patientName = card.querySelector('.request-info h3').textContent.toLowerCase();
        const treatmentType = card.querySelector('.treatment-type').textContent.toLowerCase();
        const doctorName = card.querySelector('.doctor-preference').textContent.toLowerCase();
        
        const shouldShow = patientName.includes(searchTerm) || 
                          treatmentType.includes(searchTerm) || 
                          doctorName.includes(searchTerm);
        
        card.style.display = shouldShow ? 'grid' : 'none';
    });
}

function handleViewRequest(e) {
    const requestId = e.currentTarget.dataset.id;
    showLoadingSpinner();
    
    fetch(`api/requests/${requestId}`)
        .then(handleResponse)
        .then(data => {
            hideLoadingSpinner();
            displayRequestDetails(data);
        })
        .catch(error => {
            hideLoadingSpinner();
            showToast('Error loading request details', 'error');
            console.error('Error:', error);
        });
}

function handleRespond(e) {
    const requestId = e.currentTarget.dataset.id;
    const requestCard = e.currentTarget.closest('.request-card');
    
    // Pre-fill form with existing data if available
    document.getElementById('request_id').value = requestId;
    if (requestCard) {
        const estimatedCost = requestCard.querySelector('.cost')?.textContent.match(/\d+(\.\d+)?/)?.[0];
        if (estimatedCost) {
            document.getElementById('estimated_cost').value = estimatedCost;
        }
    }
    
    showModal('responseModal');
}

function handleApprove(e) {
    const requestId = e.currentTarget.dataset.id;
    
    if (confirm('Are you sure you want to approve this treatment request?')) {
        showLoadingSpinner();
        
        fetch(`api/requests/${requestId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        })
        .then(handleResponse)
        .then(data => {
            hideLoadingSpinner();
            showToast('Request approved successfully', 'success');
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            hideLoadingSpinner();
            showToast('Error approving request', 'error');
            console.error('Error:', error);
        });
    }
}

function handleResponseSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const requestId = formData.get('request_id');

    if (!validateResponseForm(form)) {
        return;
    }

    showLoadingSpinner();
    
    fetch(`api/requests/${requestId}/respond`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
    })
    .then(handleResponse)
    .then(data => {
        hideLoadingSpinner();
        showToast('Response sent successfully', 'success');
        closeModal();
        setTimeout(() => location.reload(), 1500);
    })
    .catch(error => {
        hideLoadingSpinner();
        showToast('Error sending response', 'error');
        console.error('Error:', error);
    });
}

function handleNotifications() {
    // Implement notifications panel logic
    console.log('Notifications clicked');
}

// Utility Functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        const firstInput = modal.querySelector('input:not([type="hidden"]), textarea');
        if (firstInput) {
            firstInput.focus();
        }
    }
}

function closeModal() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    const toastContainer = document.querySelector('.toast-container');
    toastContainer.appendChild(toast);
    
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function initializeToastContainer() {
    if (!document.querySelector('.toast-container')) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
}

function showLoadingSpinner() {
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    document.body.appendChild(spinner);
}

function hideLoadingSpinner() {
    const spinner = document.querySelector('.loading-spinner');
    if (spinner) {
        spinner.remove();
    }
}

function validateResponseForm(form) {
    const estimatedCost = form.querySelector('#estimated_cost').value;
    const responseMessage = form.querySelector('#response_message').value;
    
    if (!estimatedCost || estimatedCost <= 0) {
        showToast('Please enter a valid estimated cost', 'error');
        return false;
    }
    
    if (!responseMessage.trim()) {
        showToast('Please enter a response message', 'error');
        return false;
    }
    
    return true;
}

function handleResponse(response) {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    return response.json();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function displayRequestDetails(data) {
    // Implement request details display logic
    console.log('Displaying request details:', data);
}

// Date formatting utility
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Error handling
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    showToast('An unexpected error occurred', 'error');
});

// Initialize tooltips if using them
function initializeTooltips() {
    const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
    tooltipTriggers.forEach(trigger => {
        trigger.addEventListener('mouseenter', showTooltip);
        trigger.addEventListener('mouseleave', hideTooltip);
    });
}

// Export any functions that might be needed elsewhere
export {
    showToast,
    showModal,
    closeModal,
    formatDate
};