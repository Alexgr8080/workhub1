<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyWorkHub - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7fafc;
            color: #4a5568;
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
        }
        .error-message {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-tasks text-2xl mr-3"></i>
                    <h1 class="text-2xl font-bold">MyWorkHub</h1>
                </div>
                <nav>
                    <a href="#" class="text-white hover:text-blue-200 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-600 text-white py-4 px-6">
                <h2 class="text-xl font-semibold">Create an Account</h2>
                <p class="text-blue-100 text-sm mt-1">Join MyWorkHub to manage your tasks efficiently</p>
            </div>
            
            <form id="registrationForm" class="py-6 px-8">
                <!-- Full Name -->
                <div class="mb-4">
                    <label for="fullName" class="block text-gray-700 text-sm font-medium mb-1">Full Name</label>
                    <input type="text" id="fullName" name="fullName" class="form-input w-full px-4 py-2 border rounded-md focus:outline-none" required>
                    <div class="error-message hidden" id="fullNameError"></div>
                </div>
                
                <!-- Username -->
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-medium mb-1">Username</label>
                    <input type="text" id="username" name="username" class="form-input w-full px-4 py-2 border rounded-md focus:outline-none" required>
                    <div class="error-message hidden" id="usernameError"></div>
                </div>
                
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-1">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input w-full px-4 py-2 border rounded-md focus:outline-none" required>
                    <div class="error-message hidden" id="emailError"></div>
                </div>
                
                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="form-input w-full px-4 py-2 border rounded-md focus:outline-none pr-10" required>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 flex items-center">
                            <i class="fas fa-eye-slash text-gray-500"></i>
                        </button>
                    </div>
                    <div class="error-message hidden" id="passwordError"></div>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="confirmPassword" class="block text-gray-700 text-sm font-medium mb-1">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-input w-full px-4 py-2 border rounded-md focus:outline-none" required>
                    <div class="error-message hidden" id="confirmPasswordError"></div>
                </div>
                
                <!-- Department -->
                <div class="mb-4">
                    <label for="department" class="block text-gray-700 text-sm font-medium mb-1">Department</label>
                    <select id="department" name="department" class="form-input w-full px-4 py-2 border rounded-md focus:outline-none">
                        <option value="">Select Department</option>
                        <option value="it">IT Department</option>
                        <option value="hr">Human Resources</option>
                        <option value="finance">Finance</option>
                        <option value="marketing">Marketing</option>
                        <option value="research">Research</option>
                        <option value="operations">Operations</option>
                    </select>
                </div>
                
                <!-- Role -->
                <div class="mb-6">
                    <label for="role" class="block text-gray-700 text-sm font-medium mb-1">Role</label>
                    <select id="role" name="role" class="form-input w-full px-4 py-2 border rounded-md focus:outline-none">
                        <option value="">Select Role</option>
                        <option value="user">Standard User</option>
                        <option value="manager">Team Manager</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" id="terms" name="terms" class="form-checkbox h-4 w-4 text-blue-600">
                        <span class="ml-2 text-sm text-gray-600">I agree to the 
                            <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and 
                            <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                        </span>
                    </label>
                    <div class="error-message hidden" id="termsError"></div>
                </div>
                
                <!-- Register Button -->
                <div class="flex items-center justify-between">
                    <button type="submit" id="registerButton" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-300 w-full">
                        Create Account
                    </button>
                </div>
                
                <div class="text-center mt-4 text-sm text-gray-600">
                    Already have an account? 
                    <a href="#" class="text-blue-600 hover:underline">Sign in</a>
                </div>
            </form>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t mt-8 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600 text-sm">
                        &copy; 2025 MyWorkHub. Created by Dr. Ahmed AL-sadi
                    </p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition duration-300">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition duration-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition duration-300">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const registerButton = document.getElementById('registerButton'); // Get the button

            // Toggle password visibility
            if (togglePasswordBtn && passwordField) {
                togglePasswordBtn.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    
                    // Toggle icon
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            }
            
            // Form validation and submission
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    let isValid = true;
                    
                    // Reset all error messages
                    document.querySelectorAll('.error-message').forEach(el => {
                        el.classList.add('hidden');
                        el.textContent = '';
                    });
                    
                    // Validate full name
                    const fullNameInput = document.getElementById('fullName');
                    const fullName = fullNameInput ? fullNameInput.value.trim() : '';
                    if (fullName === '') {
                        showError('fullNameError', 'Full name is required');
                        isValid = false;
                    }
                    
                    // Validate username
                    const usernameInput = document.getElementById('username');
                    const username = usernameInput ? usernameInput.value.trim() : '';
                    if (username === '') {
                        showError('usernameError', 'Username is required');
                        isValid = false;
                    } else if (username.length < 3) {
                        showError('usernameError', 'Username must be at least 3 characters');
                        isValid = false;
                    }
                    
                    // Validate email
                    const emailInput = document.getElementById('email');
                    const email = emailInput ? emailInput.value.trim() : '';
                    if (email === '') {
                        showError('emailError', 'Email is required');
                        isValid = false;
                    } else if (!isValidEmail(email)) {
                        showError('emailError', 'Please enter a valid email address');
                        isValid = false;
                    }
                    
                    // Validate password
                    const passwordValue = passwordField ? passwordField.value : '';
                    if (passwordValue === '') {
                        showError('passwordError', 'Password is required');
                        isValid = false;
                    } else if (passwordValue.length < 8) {
                        showError('passwordError', 'Password must be at least 8 characters');
                        isValid = false;
                    }
                    
                    // Validate confirm password
                    const confirmPasswordInput = document.getElementById('confirmPassword');
                    const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : '';
                    if (confirmPassword === '') {
                        showError('confirmPasswordError', 'Please confirm your password');
                        isValid = false;
                    } else if (confirmPassword !== passwordValue) {
                        showError('confirmPasswordError', 'Passwords do not match');
                        isValid = false;
                    }
                    
                    // Validate terms checkbox
                    const termsCheckbox = document.getElementById('terms');
                    const termsChecked = termsCheckbox ? termsCheckbox.checked : false;
                    if (!termsChecked) {
                        showError('termsError', 'You must agree to the terms and conditions');
                        isValid = false;
                    }
                    
                    // If form is valid, submit it via fetch
                    if (isValid) {
                        const formData = new FormData(form);
                        formData.append('action', 'register_public_user'); // Action for your api/user.php

                        const originalButtonText = registerButton ? registerButton.innerHTML : 'Create Account';
                        if (registerButton) {
                            registerButton.innerHTML = '<span class="spinner-button animate-spin rounded-full h-4 w-4 border-t-2 border-r-2 border-white inline-block mr-2" role="status" aria-hidden="true"></span> Processing...';
                            registerButton.disabled = true;
                        }

                        fetch('api/user.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                // Try to get error from backend if it's JSON, otherwise use status text
                                return response.json().then(errData => {
                                    throw new Error(errData.message || response.statusText);
                                }).catch(() => {
                                    throw new Error(response.statusText || `Server error: ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert(data.message || 'Registration successful! You can now log in.');
                                form.reset();
                                // Optional: Redirect to login page after a short delay or directly
                                // setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                            } else {
                                let mainErrorMessage = data.message || 'Registration failed. Please check the errors.';
                                if (data.errors && data.errors.length > 0) {
                                    // Display specific errors
                                    data.errors.forEach(error => {
                                        if (error.toLowerCase().includes('username')) {
                                            showError('usernameError', error);
                                        } else if (error.toLowerCase().includes('email')) {
                                            showError('emailError', error);
                                        } else if (error.toLowerCase().includes('password')) {
                                            showError('passwordError', error); // General password error
                                        } else if (error.toLowerCase().includes('full name')) {
                                            showError('fullNameError', error);
                                        } else {
                                            // If error is not field-specific, prepend to main error message
                                            // or display in a general error area if you have one.
                                        }
                                    });
                                    // If any field-specific error was shown, alert might be redundant
                                    // unless the main message has additional info.
                                    // For simplicity, we can alert the main message too.
                                    alert(mainErrorMessage + "\n\nDetails:\n- " + data.errors.join("\n- "));
                                } else {
                                    alert(mainErrorMessage);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Registration submission error:', error);
                            alert('An error occurred during registration: ' + error.message + '. Please check the console for more details and try again.');
                        })
                        .finally(() => {
                            if (registerButton) {
                                registerButton.innerHTML = originalButtonText;
                                registerButton.disabled = false;
                            }
                        });
                    }
                });
            }
            
            // Helper function to show error messages
            function showError(elementId, message) {
                const errorElement = document.getElementById(elementId);
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                } else {
                    console.warn(`Error element not found: ${elementId} for message: ${message}`);
                }
            }
            
            // Helper function to validate email format
            function isValidEmail(email) {
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            }

            // Add a simple CSS rule for the spinner if not already handled by Tailwind or other CSS
            // This is just a basic spinner, you might want a more elaborate one.
            const styleSheet = document.createElement("style");
            styleSheet.type = "text/css";
            styleSheet.innerText = ".spinner-button { border-top-color: transparent !important; }"; // Make top transparent for spin effect
            document.head.appendChild(styleSheet);
        });
    </script>
</body>
</html>
