// Initialize AOS (Animate On Scroll)
AOS.init({
    duration: 1000,
    once: true
});

// Navbar scroll behavior
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Contact form AJAX submission
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!contactForm.checkValidity()) {
            event.stopPropagation();
            contactForm.classList.add('was-validated');
            return;
        }

        const formData = new FormData(contactForm);
        const submitButton = contactForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

        fetch('ajax/contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert(data.message, 'success');
                contactForm.reset();
                contactForm.classList.remove('was-validated');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again later.', 'danger');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Send Message';
        });
    });
}

// Alert function
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Number counter animation
const counters = document.querySelectorAll('.count');
const speed = 200;

counters.forEach(counter => {
    const animate = () => {
        const value = +counter.innerText.replace('+', '');
        const data = +counter.getAttribute('data-target');
        const time = data / speed;
        
        if (value < data) {
            counter.innerText = Math.ceil(value + time) + '+';
            setTimeout(animate, 1);
        }
    };
    
    animate();
}); 