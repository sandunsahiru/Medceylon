document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    initializeEventListeners();
    setupDashboardInteractions();
});

function initializeEventListeners() {
    // Navigation items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', handleNavigation);
    });

    // Search functionality
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }

    // Appointment actions
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', handleEditClick);
    });

    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', handleNextClick);
    });

    // Notification bell
    const notificationBtn = document.querySelector('.notifications-btn');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', handleNotifications);
    }
}

function setupDashboardInteractions() {
    // Add hover effects for buttons
    const interactiveElements = document.querySelectorAll('.nav-item, .edit-btn, .next-btn');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', () => {
            element.style.transform = 'translateY(-2px)';
            element.style.transition = 'transform 0.2s ease';
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translateY(0)';
        });
    });
}

// Event Handlers
function handleNavigation(e) {
    e.preventDefault();
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => item.classList.remove('active'));
    this.classList.add('active');

    const section = this.getAttribute('href').substring(1);
    console.log(`Navigating to ${section}`);
    // Add your navigation logic here
}

function handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    const appointmentCards = document.querySelectorAll('.appointment-card');

    appointmentCards.forEach(card => {
        const patientName = card.querySelector('.details h3').textContent.toLowerCase();
        const appointmentDate = card.querySelector('.details p').textContent.toLowerCase();
        const shouldShow = patientName.includes(searchTerm) || appointmentDate.includes(searchTerm);
        
        card.style.display = shouldShow ? 'flex' : 'none';
    });
}

function handleEditClick(e) {
    e.stopPropagation();
    const card = this.closest('.appointment-card');
    const patientName = card.querySelector('.details h3').textContent;
    const appointmentDate = card.querySelector('.details p').textContent;
    
    console.log(`Editing appointment for ${patientName} on ${appointmentDate}`);
    // Add your edit appointment logic here
}

function handleNextClick(e) {
    e.stopPropagation();
    const card = this.closest('.appointment-card');
    const patientName = card.querySelector('.details h3').textContent;
    
    console.log(`Next appointment for ${patientName}`);
    // Add your next appointment logic here
}

function handleNotifications() {
    console.log('Opening notifications panel');
    // Add your notifications logic here
}

// Utility Functions
function showToast(message, type = 'info') {
    // Simple toast notification implementation
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatTime(time) {
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit'
    });
}