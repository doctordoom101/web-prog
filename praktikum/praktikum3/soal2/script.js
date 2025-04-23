document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const resultsContainer = document.getElementById('resultsContainer');
    
    // Form validation and submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Reset previous errors
        hideAllErrors();
        
        // Get form values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const genderElements = document.getElementsByName('gender');
        const course = document.getElementById('course').value;
        
        let gender = '';
        for (let i = 0; i < genderElements.length; i++) {
            if (genderElements[i].checked) {
                gender = genderElements[i].value;
                break;
            }
        }
        
        // Validate form
        let isValid = true;
        
        // Name validation
        if (name === '') {
            showError('nameError');
            isValid = false;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError('emailError');
            isValid = false;
        }
        
        // Phone validation
        const phoneRegex = /^\d+$/;
        if (!phoneRegex.test(phone)) {
            showError('phoneError');
            isValid = false;
        }
        
        // Gender validation
        if (gender === '') {
            showError('genderError');
            isValid = false;
        }
        
        // Course validation
        if (course === '') {
            showError('courseError');
            isValid = false;
        }
        
        // If form is valid, display results
        if (isValid) {
            // Display results
            document.getElementById('resultName').textContent = name;
            document.getElementById('resultEmail').textContent = email;
            document.getElementById('resultPhone').textContent = phone;
            document.getElementById('resultGender').textContent = gender;
            document.getElementById('resultCourse').textContent = course;
            
            // Show results container
            resultsContainer.style.display = 'block';
            
            // Scroll to results
            resultsContainer.scrollIntoView({ behavior: 'smooth' });
        }
    });
    
    // Helper functions for error handling
    function showError(errorId) {
        const errorElement = document.getElementById(errorId);
        errorElement.style.display = 'block';
    }
    
    function hideAllErrors() {
        const errorElements = document.querySelectorAll('.error');
        errorElements.forEach(function(element) {
            element.style.display = 'none';
        });
    }
    
    // Add input event listeners for real-time validation
    document.getElementById('name').addEventListener('input', function() {
        if (this.value.trim() !== '') {
            document.getElementById('nameError').style.display = 'none';
        }
    });
    
    document.getElementById('email').addEventListener('input', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailRegex.test(this.value.trim())) {
            document.getElementById('emailError').style.display = 'none';
        }
    });
    
    document.getElementById('phone').addEventListener('input', function() {
        const phoneRegex = /^\d+$/;
        if (phoneRegex.test(this.value.trim())) {
            document.getElementById('phoneError').style.display = 'none';
        }
    });
    
    const genderElements = document.getElementsByName('gender');
    genderElements.forEach(function(element) {
        element.addEventListener('change', function() {
            document.getElementById('genderError').style.display = 'none';
        });
    });
    
    document.getElementById('course').addEventListener('change', function() {
        if (this.value !== '') {
            document.getElementById('courseError').style.display = 'none';
        }
    });
});