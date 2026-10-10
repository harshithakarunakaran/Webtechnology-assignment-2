
// Get the registration form and success message
const form = document.getElementById("registrationForm");
const formMessage = document.getElementById("formMessage");

// Display validation errors
function showError(fieldId, message) {
    document.getElementById(fieldId + "Error").textContent = message;
}

// Clear previous errors and messages
function clearErrors() {
    document.querySelectorAll(".error").forEach(function (element) {
        element.textContent = "";
    });

    formMessage.textContent = "";
    formMessage.className = "message";
}

// Validate the form when Register Student is clicked
form.addEventListener("submit", function (event) {
    event.preventDefault();

    clearErrors();

    // Get values from the form
    const name = document.getElementById("name").value.trim();
    const rollNumber = document.getElementById("rollNumber").value.trim();
    const email = document.getElementById("email").value.trim();
    const course = document.getElementById("course").value;
    const password = document.getElementById("password").value;
    const fileInput = document.getElementById("document");

    let isValid = true;

    // 1. Validate full name
    if (name.length < 2) {
        showError("name", "Please enter a valid name.");
        isValid = false;
    }

    // 2. Validate roll number
    if (!/^[A-Za-z0-9/-]{3,20}$/.test(rollNumber)) {
        showError(
            "rollNumber",
            "Enter a valid roll number (3–20 characters)."
        );
        isValid = false;
    }

    // 3. Validate email address
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError("email", "Please enter a valid email address.");
        isValid = false;
    }

    // 4. Validate course selection
    if (course === "") {
        showError("course", "Please select a course.");
        isValid = false;
    }

    // 5. Validate password strength
    // Must contain uppercase, lowercase, number and special character
    const strongPassword =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

    if (!strongPassword.test(password)) {
        showError(
            "password",
            "Password must have 8+ characters, uppercase, lowercase, number and symbol."
        );
        isValid = false;
    }

    // 6. Validate uploaded file, if selected
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];

        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "application/pdf"
        ];

        const allowedExtensions = /\.(jpe?g|png|pdf)$/i;

        // Check file type and extension
        if (
            !allowedTypes.includes(file.type) ||
            !allowedExtensions.test(file.name)
        ) {
            showError(
                "document",
                "Only JPG, PNG or PDF files are allowed."
            );
            isValid = false;
        }

        // Maximum file size: 2 MB
        if (file.size > 2 * 1024 * 1024) {
            showError(
                "document",
                "File size must not exceed 2 MB."
            );
            isValid = false;
        }
    }

    // 7. Stop if any validation fails
    if (!isValid) {
        formMessage.textContent =
            "Please correct the errors and try again.";

        formMessage.classList.add("failure");
        return;
    }

    // 8. Display browser pop-up on successful validation
    alert(
        "Registration Successful!\n\n" +
        "Welcome, " + name + "!\n\n" +
        "Roll Number: " + rollNumber + "\n" +
        "Course: " + course
    );

    // 9. Reset the form after the user clicks OK
    form.reset();

    // Clear the success message area
    formMessage.textContent = "";
    formMessage.className = "message";
});

