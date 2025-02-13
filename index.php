<?php
require_once 'config/config.php';
init_session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-tint"></i> BloodDonate
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#why-donate">Why Donate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#blood-types">Blood Types</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-warning text-dark ms-2 px-4" href="admin/dashboard.php">
                                <i class="fas fa-cog"></i> Admin Panel
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white ms-2 px-4" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-warning text-dark ms-2 px-4" href="admin/login.php">
                                <i class="fas fa-user-shield"></i> Admin Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Video Background -->
    <section id="home" class="hero-section">
        <div class="video-background">
            <video autoplay muted loop id="myVideo">
                <source src="assets/videos/homevideo1.mp4" type="video/mp4">
            </video>
            <div class="overlay"></div>
        </div>
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-md-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold mb-4 text-white">Donate Blood, Save Lives</h1>
                    <p class="lead mb-4 text-white">Your donation can save up to three lives. Join our community of life-savers today.</p>
                    <div class="d-flex gap-3">
                        <a href="register.php" class="btn btn-danger btn-lg">Become a Donor</a>
                        <a href="#why-donate" class="btn btn-outline-light btn-lg">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Donate Section -->
    <section id="why-donate" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Why Donate Blood?</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-heart text-danger fa-3x mb-3"></i>
                            <h4>Save Lives</h4>
                            <p>One donation can save up to three lives and help various medical treatments.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clock text-danger fa-3x mb-3"></i>
                            <h4>Quick & Easy</h4>
                            <p>The donation process takes less than an hour and is completely safe.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle text-danger fa-3x mb-3"></i>
                            <h4>Regular Need</h4>
                            <p>Blood is needed every day for surgeries, treatments, and emergencies.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blood Types Section -->
    <section id="blood-types" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Blood Types & Compatibility</h2>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="blood-type-chart" data-aos="zoom-in">
                        <div class="row text-center">
                            <?php
                            $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                            foreach($bloodTypes as $type):
                            ?>
                            <div class="col-md-3 col-6 mb-4">
                                <div class="blood-type-box p-3 rounded">
                                    <h3 class="blood-type"><?php echo $type; ?></h3>
                                    <p class="mb-0">Can donate to:</p>
                                    <small class="compatibility">
                                        <?php
                                        // Add compatibility information based on blood type
                                        switch($type) {
                                            case 'O-':
                                                echo 'All blood types';
                                                break;
                                            case 'O+':
                                                echo 'O+, A+, B+, AB+';
                                                break;
                                            // Add other cases
                                        }
                                        ?>
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6" data-aos="fade-right">
                    <h2 class="mb-4">Contact Us</h2>
                    <form id="contactForm" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                            <div class="invalid-feedback">Please enter your name</div>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                            <div class="invalid-feedback">Please enter a valid email address</div>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                            <div class="invalid-feedback">Please enter your message</div>
                        </div>
                        <button type="submit" class="btn btn-danger">Send Message</button>
                    </form>
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <div class="contact-info p-4">
                        <h3>Emergency Contact</h3>
                        <p class="text-danger mb-4">For urgent blood requirements:</p>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-phone me-2"></i> Emergency: +91 83294 08037 </li>
                            <li class="mb-3"><i class="fas fa-envelope me-2"></i> ankitbodkhe2003@gmail.com</li>
                            <li class="mb-3"><i class="fas fa-clock me-2"></i> 24/7 Available </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-tint"></i> BloodDonate</h5>
                    <p>Connecting donors with those in need. Every drop counts.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-white">Home</a></li>
                        <li><a href="#why-donate" class="text-white">Why Donate</a></li>
                        <li><a href="#blood-types" class="text-white">Blood Types</a></li>
                        <li><a href="#contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 