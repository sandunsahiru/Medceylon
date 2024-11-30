// Main initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeModal();
    initializeEventListeners();
    setupDashboardInteractions();
});

// Initialize Modal
function initializeModal() {
    const modal = document.querySelector('.modal');
    const responseForm = document.getElementById('responseForm');
    const closeButtons = document.querySelectorAll('.cancel-response, .close-modal');

    // Add close button handlers
    closeButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    });

    // Close on outside click
    modal?.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Handle form submission
    responseForm?.addEventListener('submit', handleResponseSubmit);
}

// Initialize Event Listeners
function initializeEventListeners() {
    // Navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', handleNavigation);
    });

    // Search functionality
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearch, 300));
    }

    // Request action buttons
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', handleViewRequest);
    });

    document.querySelectorAll('.respond-btn').forEach(btn => {
        btn.addEventListener('click', handleRespond);
    });

    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', handleApprove);
    });

    // Notifications
    const notificationBtn = document.querySelector('.notifications');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', handleNotifications);
    }
}

// Dashboard Interactions
function setupDashboardInteractions() {
    // Stats cards hover effects
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });

    // Request cards hover effects
    document.querySelectorAll('.request-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateX(5px)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateX(0)';
        });
    });
}

// Event Handlers
function handleNavigation(e) {
    const href = this.getAttribute('href');
    if (href && href !== '#') {
        e.preventDefault();
        window.location.href = href;
    }
}

function handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    const requestCards = document.querySelectorAll('.request-card');

    requestCards.forEach(card => {
        const patientName = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const treatmentType = card.querySelector('.treatment-type')?.textContent.toLowerCase() || '';
        const doctorName = card.querySelector('.doctor-preference')?.textContent.toLowerCase() || '';
        
        const shouldShow = patientName.includes(searchTerm) || 
                          treatmentType.includes(searchTerm) || 
                          doctorName.includes(searchTerm);
        
        card.style.display = shouldShow ? 'flex' : 'none';
    });
}

function handleViewRequest(e) {
    const requestId = e.currentTarget.dataset.id;
    showLoadingSpinner();

    // Replace with actual API call
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
    e.preventDefault();
    const requestId = e.currentTarget.dataset.id;
    const modal = document.querySelector('.modal');
    
    // Set request ID and show modal
    document.getElementById('request_id').value = requestId;
    modal.classList.add('show');
    
    // Focus first input
    const firstInput = modal.querySelector('input:not([type="hidden"])');
    if (firstInput) {
        firstInput.focus();
    }
}

function handleApprove(e) {
    const requestId = e.currentTarget.dataset.id;
    
    if (confirm('Are you sure you want to approve this request?')) {
        showLoadingSpinner();
        
        // Replace with actual API call
        fetch(`api/requests/${requestId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        })
        .then(handleResponse)
        .then(() => {
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

    if (!validateResponseForm(formData)) return;

    const submitButton = form.querySelector('.send-response');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="ri-loader-4-line"></i> Sending...';
    submitButton.disabled = true;

    // Replace with actual API call
    fetch(`api/requests/${formData.get('request_id')}/respond`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
    })
    .then(handleResponse)
    .then(() => {
        showToast('Response sent successfully', 'success');
        closeModal();
        setTimeout(() => location.reload(), 1500);
    })
    .catch(error => {
        showToast('Error sending response', 'error');
        console.error('Error:', error);
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

// Utility Functions
function closeModal() {
    const modal = document.querySelector('.modal');
    const form = document.getElementById('responseForm');
    
    modal.classList.remove('show');
    setTimeout(() => {
        if (form) form.reset();
    }, 300);
}

function validateResponseForm(formData) {
    const cost = formData.get('estimated_cost');
    const message = formData.get('response_message');
    
    if (!cost || cost <= 0) {
        showToast('Please enter a valid cost', 'error');
        return false;
    }
    
    if (!message?.trim()) {
        showToast('Please enter a response message', 'error');
        return false;
    }
    
    return true;
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    const container = document.querySelector('.toast-container') || createToastContainer();
    container.appendChild(toast);
    
    requestAnimationFrame(() => toast.classList.add('show'));
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

function showLoadingSpinner() {
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    document.body.appendChild(spinner);
}

function hideLoadingSpinner() {
    const spinner = document.querySelector('.loading-spinner');
    if (spinner) spinner.remove();
}

function handleResponse(response) {
    if (!response.ok) throw new Error('Network response was not ok');
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

function handleNotifications() {
    // Implement notifications panel logic
    console.log('Notifications clicked');
}

function displayRequestDetails(data) {
    // Implement request details display logic
    console.log('Request details:', data);
}

// Error handling
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    showToast('An unexpected error occurred', 'error');
});