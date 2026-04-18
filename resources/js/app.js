import './bootstrap';

// ===== Vanilla JS Utilities for VCMS =====

// Password visibility toggle
window.togglePasswordVisibility = function (inputId) {
    var input = document.getElementById(inputId);
    var eyeOpen = document.getElementById(inputId + '-eye-open');
    var eyeClosed = document.getElementById(inputId + '-eye-closed');
    if (input.type === 'password') {
        input.type = 'text';
        if (eyeOpen) eyeOpen.classList.add('hidden');
        if (eyeClosed) eyeClosed.classList.remove('hidden');
    } else {
        input.type = 'password';
        if (eyeOpen) eyeOpen.classList.remove('hidden');
        if (eyeClosed) eyeClosed.classList.add('hidden');
    }
};

// Dropdown toggle
document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-dropdown-trigger]');
    if (trigger) {
        e.stopPropagation();
        const dropdown = trigger.closest('[data-dropdown]');
        const content = dropdown.querySelector('[data-dropdown-content]');
        const isOpen = content.style.display !== 'none';

        // Close all other dropdowns
        document.querySelectorAll('[data-dropdown-content]').forEach(el => {
            el.style.display = 'none';
            el.style.opacity = '0';
            el.style.transform = 'scale(0.95)';
        });

        if (!isOpen) {
            content.style.display = 'block';
            requestAnimationFrame(() => {
                content.style.opacity = '1';
                content.style.transform = 'scale(1)';
            });
        }
        return;
    }

    // Close dropdowns when clicking outside
    if (!e.target.closest('[data-dropdown]')) {
        document.querySelectorAll('[data-dropdown-content]').forEach(el => {
            el.style.display = 'none';
            el.style.opacity = '0';
            el.style.transform = 'scale(0.95)';
        });
    }
});

// Mobile sidebar toggle
window.toggleSidebar = function () {
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) {
        const isOpen = sidebar.classList.contains('translate-x-0');
        if (isOpen) {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        } else {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        }
    }
};

// Flash message auto-dismiss
document.addEventListener('DOMContentLoaded', function () {
    const flashMessages = document.querySelectorAll('[data-flash]');
    flashMessages.forEach(function (msg) {
        setTimeout(function () {
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-8px)';
            setTimeout(function () { msg.remove(); }, 300);
        }, 4000);
    });
});

// Phone input initialization (No longer using intlTelInput)
window.initPhoneInput = function (element) {
    return null;
};

// Sync international numbers (No longer needed)
window.syncPhoneNumbers = function (formElement) {
    // Standard behavior
};

// Repeatable fields (phones/emails)
window.addRepeatableField = function (containerId, type) {
    const container = document.getElementById(containerId);
    const index = container.children.length;
    let labels, placeholder;

    if (type === 'phone') {
        labels = '<option value="mobile">Mobile</option><option value="office">Office</option><option value="other">Other</option>';
        placeholder = 'Phone number';
    } else {
        labels = '<option value="work">Work</option><option value="personal">Personal</option><option value="other">Other</option>';
        placeholder = 'Email address';
    }

    const fieldName = type === 'phone' ? 'phones' : 'emails';
    const inputType = type === 'phone' ? 'tel' : 'email';

    const html = `
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 animate-fade-in group" id="${fieldName}-row-${index}">
            <select name="${fieldName}[${index}][label]"
                class="w-full sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all order-1">
                ${labels}
            </select>
            <div class="flex-1 min-w-[200px] order-2">
                <input type="${inputType}" name="${fieldName}[${index}][${type === 'phone' ? 'phone' : 'email'}]"
                    placeholder="${placeholder}"
                    class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
            </div>
            <button type="button" onclick="this.parentElement.remove()"
                class="shrink-0 p-2 rounded-lg text-danger-400 hover:bg-danger-500/10 transition-colors order-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    // Initialize phone input if needed
    if (type === 'phone') {
        // Handled as standard text input
    }
};

// Image preview on file input
window.previewImage = function (input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            const placeholder = preview.parentElement.querySelector('.upload-placeholder');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
};

// Lightbox
window.openLightbox = function (src) {
    const overlay = document.createElement('div');
    overlay.id = 'lightbox-overlay';
    overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm';
    overlay.style.animation = 'fadeIn 0.2s ease-out';
    overlay.innerHTML = `
        <div class="relative max-w-4xl max-h-[90vh] p-4">
            <button onclick="document.getElementById('lightbox-overlay').remove()"
                class="absolute -top-2 -right-2 z-10 p-2 rounded-full bg-surface-800 text-white hover:bg-surface-700 transition-colors shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img src="${src}" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl object-contain" />
        </div>
    `;
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) overlay.remove();
    });
    document.body.appendChild(overlay);
};

// Confirm delete
window.confirmDelete = function (formId, itemName) {
    if (confirm('Are you sure you want to delete this ' + (itemName || 'item') + '? This action cannot be undone.')) {
        document.getElementById(formId).submit();
    }
};

// Theme management
window.initTheme = function () {
    const theme = localStorage.getItem('theme') || 'dark';
    if (theme === 'light') {
        document.documentElement.classList.add('light');
    } else {
        document.documentElement.classList.remove('light');
    }
};

window.toggleTheme = function () {
    const isLight = document.documentElement.classList.toggle('light');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
};

// Initialize theme on load
document.addEventListener('DOMContentLoaded', initTheme);

// Template preview (for send email page)
window.loadTemplatePreview = function (templateId, contactId, configId) {
    if (!templateId || !contactId) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/email-preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            template_id: templateId,
            contact_id: contactId,
            config_id: configId || null,
        }),
    })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            const subjectEl = document.getElementById('preview-subject');
            const bodyEl = document.getElementById('preview-body');
            if (subjectEl) subjectEl.textContent = data.subject;
            if (bodyEl) {
                if (bodyEl.tagName === 'IFRAME') {
                    bodyEl.srcdoc = data.body;
                } else {
                    bodyEl.innerHTML = data.body;
                }
            }
        })
        .catch(function (err) { console.error('Preview error:', err); });
};
