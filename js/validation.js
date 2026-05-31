document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[id]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            // Clear previous errors
            clearErrors(form);

            requiredFields.forEach(field => {
                const value = field.value.trim();
                if (!value) {
                    isValid = false;
                    showError(field, getErrorMessage(field));
                } else if (field.type === 'email' && !isValidEmail(value)) {
                    isValid = false;
                    showError(field, 'Please enter a valid email address.');
                } else if (field.id === 'password' && value.length < 6 && form.id === 'registerForm') {
                    isValid = false;
                    showError(field, 'Password must be at least 6 characters long.');
                } else if (field.id === 'price' && (isNaN(parseFloat(value)) || parseFloat(value) <= 0)) {
                    isValid = false;
                    showError(field, 'Please enter a valid positive price.');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Clear error on input
        form.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('input', function() {
                removeError(this);
            });
        });
    });

    function showError(field, message) {
        field.classList.add('is-invalid');
        const errorDiv = document.createElement('span');
        errorDiv.className = 'error-text';
        errorDiv.innerText = message;
        field.closest('.form-group').appendChild(errorDiv);
    }

    function removeError(field) {
        field.classList.remove('is-invalid');
        const formGroup = field.closest('.form-group');
        const errorText = formGroup.querySelector('.error-text');
        if (errorText) {
            errorText.remove();
        }
    }

    function clearErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        form.querySelectorAll('.error-text').forEach(error => {
            error.remove();
        });
    }

    function getErrorMessage(field) {
        const label = field.closest('.form-group').querySelector('label');
        const fieldName = label ? label.innerText : 'This field';

        if (field.id === 'title') return 'Please enter a valid craft title';
        if (field.id === 'password') return 'Password cannot be empty';

        return `${fieldName} is required.`;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * JavaScript Confirmation Pop-up for Delete Actions
     */
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn'))) {
            const confirmed = confirm('Are you sure you want to permanently delete this record? This action cannot be undone.');
            if (!confirmed) {
                e.preventDefault();
            }
        }
    });
});
