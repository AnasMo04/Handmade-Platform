document.addEventListener('DOMContentLoaded', function() {
    // Client-side validation for the Craft Form (Add/Edit)
    const craftForm = document.getElementById('craftForm');
    if (craftForm) {
        craftForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const price = document.getElementById('price').value.trim();

            if (!title || !description || !price) {
                e.preventDefault();
                alert('Please fill in all required fields (Title, Description, and Price).');
                return;
            }

            if (isNaN(price) || parseFloat(price) <= 0) {
                e.preventDefault();
                alert('Please enter a valid positive number for the price.');
                return;
            }
        });
    }

    // Client-side validation for Registration Form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !email || !password) {
                e.preventDefault();
                alert('All fields are required.');
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return;
            }
        });
    }

    // JavaScript Confirmation Pop-up for Delete Actions
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmed = confirm('Are you sure you want to permanently delete this record? This action cannot be undone.');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
});
