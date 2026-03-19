import './bootstrap'; 
import './booking-engine'; 

/**
 * Dashboard & Global UI Logic
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('Donezo System: Operational');

    // 1. Initialize Tooltips
    // Using optional chaining to prevent errors if Bootstrap isn't loaded
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (window.bootstrap) {
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    }

    // 2. Flash Message Management
    // Improved with a smoother fade-out transition
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 600);
        }, 4000);
    });

    // 3. Global Delete Confirmation (Event Delegation)
    // High performance: one listener for the entire page
    document.addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.btn-delete-confirm');
        if (deleteBtn) {
            const message = deleteBtn.dataset.confirm || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        }
    });

    // 4. Mobile Sidebar Toggle
    const sidebarToggle = document.querySelector('.mobile-nav-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }
});

/**
 * Global Slot Selection UI Helper
 * Re-architected to use data-attributes for cleaner HTML
 */
window.selectTimeSlot = (element) => {
    const slotValue = element.getAttribute('data-time');
    
    // 1. Visual State Update
    document.querySelectorAll('.slot-item').forEach(slot => {
        slot.classList.remove('selected');
        slot.setAttribute('aria-selected', 'false');
    });

    element.classList.add('selected');
    element.setAttribute('aria-selected', 'true');

    // 2. Data Persistence
    const timeInput = document.getElementById('selected_time');
    if (timeInput) {
        timeInput.value = slotValue;
        // Trigger a change event so other scripts (like price calculators) can react
        timeInput.dispatchEvent(new Event('change'));
    }
};