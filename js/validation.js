document.addEventListener('DOMContentLoaded', function() {
    /**
     * Client-side validation for the Craft Form (Add/Edit)
     */
    const craftForm = document.getElementById('craftForm');
    if (craftForm) {
        craftForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const price = document.getElementById('price').value.trim();

            if (!title || !description || !price) {
                e.preventDefault();
                alert('All fields (Title, Description, and Price) are required.');
                return;
            }

            const priceNum = parseFloat(price);
            if (isNaN(priceNum) || priceNum <= 0) {
                e.preventDefault();
                alert('Please enter a valid positive number for the price.');
                return;
            }
        });
    }

    /**
     * Client-side validation for Registration Form
     */
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;

            if (!username || !email || !password || !role) {
                e.preventDefault();
                alert('All fields are required, including role selection.');
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Security requirement: Password must be at least 6 characters long.');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
        });
    }

    /**
     * JavaScript Confirmation Pop-up for Delete Actions
     * Uses event delegation to handle dynamically added elements or multiple buttons
     */
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('delete-btn')) {
            const confirmed = confirm('Are you sure you want to permanently delete this record? This action cannot be undone.');
            if (!confirmed) {
                e.preventDefault();
            }
        }
    });
});
