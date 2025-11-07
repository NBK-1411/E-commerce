<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Essence Luxury Fragrances</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="auth-styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-brand">
                <a href="index.php" class="brand-link">
                    <h1>ESSENCE</h1>
                    <p class="tagline">Luxury Fragrances</p>
                </a>
            </div>
            <div class="auth-image">
                <img src="public/luxury-perfume-bottles-on-elegant-marble-surface-w.jpg" alt="Luxury Perfumes">
            </div>
        </div>
        
        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h2>Create Account</h2>
                    <p>Join our exclusive fragrance community</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST" action="authenticate.php">
                    <input type="hidden" name="action" value="signup">
                    
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            placeholder="John Doe" 
                            required
                            autocomplete="name"
                            maxlength="100"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="your@email.com" 
                            required
                            autocomplete="email"
                            maxlength="50"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Create a strong password" 
                            required
                            autocomplete="new-password"
                            minlength="8"
                        >
                        <small class="form-hint">Must be at least 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Confirm your password" 
                            required
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <input 
                            type="text" 
                            id="country" 
                            name="country" 
                            placeholder="Your country" 
                            required
                            autocomplete="country-name"
                            maxlength="30"
                        >
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input 
                            type="text" 
                            id="city" 
                            name="city" 
                            placeholder="Your city" 
                            required
                            autocomplete="address-level2"
                            maxlength="30"
                        >
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input 
                            type="tel" 
                            id="contact" 
                            name="contact" 
                            placeholder="Your phone number" 
                            required
                            autocomplete="tel"
                            maxlength="15"
                        >
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" value="1" required>
                            <span>I agree to the <a href="#terms" class="link-secondary">Terms & Conditions</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Create Account</button>
                </form>

                <div class="auth-divider">
                    <span>or continue with</span>
                </div>

                <div class="social-auth">
                    <button class="btn-social">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </button>
                    <button class="btn-social">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php" class="link-primary">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
