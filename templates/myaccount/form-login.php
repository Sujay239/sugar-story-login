<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$company_name = get_bloginfo( 'name' );
$primary_color = get_option('sugar_story_primary_color');
if ( empty( $primary_color ) ) $primary_color = '#c3a669';

$bg_color = get_option('sugar_story_bg_color');
if ( empty( $bg_color ) ) $bg_color = '#FEFBF6';

$image_1 = get_option('sugar_story_image_1');
if ( empty( $image_1 ) ) $image_1 = 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&q=80';

$image_2 = get_option('sugar_story_image_2');
if ( empty( $image_2 ) ) $image_2 = 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=400&q=80';

$image_3 = get_option('sugar_story_image_3');
if ( empty( $image_3 ) ) $image_3 = 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400&q=80';

$image_4 = get_option('sugar_story_image_4');
if ( empty( $image_4 ) ) $image_4 = 'https://images.unsplash.com/photo-1576618148400-f54bed99fcfd?w=400&q=80';

$google_client_id = get_option('sugar_story_google_client_id', '');
?>
<div class="sugar-story-login-wrapper light">
<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "background": "<?php echo esc_js($bg_color); ?>",
                        "surface": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "<?php echo esc_js($bg_color); ?>",
                        "primary": "<?php echo esc_js($primary_color); ?>",
                        "primary-container": "<?php echo esc_js($primary_color); ?>",
                        "on-primary-container": "#ffffff",
                        "secondary": "#3a506b",
                        "secondary-container": "#e0e7ff",
                        "on-secondary-container": "#1e3a8a",
                        "on-surface": "#2c3e50",
                        "on-surface-variant": "#4a5568",
                        "outline-variant": "#e2e8f0"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "display-lg": ["Playfair Display", "serif"],
                        "headline-md": ["Playfair Display", "serif"],
                        "body-md": ["Plus Jakarta Sans", "sans-serif"],
                        "body-lg": ["Plus Jakarta Sans", "sans-serif"],
                        "label-md": ["Plus Jakarta Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>
<style>
        .sugar-story-login-wrapper .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .sugar-story-login-wrapper .cocoa-shadow {
            box-shadow: 0px 10px 30px rgba(74, 63, 63, 0.05);
        }
        
        /* Floating animations for premium imagery */
        @keyframes float-slow {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        @keyframes float-slower {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(15px) rotate(-2deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .animate-float-slow { animation: float-slow 7s ease-in-out infinite; }
        .animate-float-slower { animation: float-slower 9s ease-in-out infinite; }
        
        .sugar-story-main-bg {
            background-color: <?php echo esc_attr($bg_color); ?>;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 2147483647; /* Max z-index to cover everything including theme headers */
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        
        /* Hide WooCommerce Privacy Policy Text on this custom form */
        .woocommerce-privacy-policy-text {
            display: none !important;
        }
    </style>

<div class="sugar-story-main-bg text-on-surface font-body-md text-body-md flex flex-col overflow-x-hidden">
<!-- Main Content Area -->
<main class="flex-grow flex items-center justify-center px-4 py-12 relative overflow-hidden">
<!-- Decorative Scattered Premium Images -->
<div class="hidden md:block absolute top-12 left-[10%] w-36 h-36 animate-float-slow z-50 group cursor-pointer">
    <div class="w-full h-full rounded-[50%] border-4 border-white shadow-xl overflow-hidden transition-all duration-1000 ease-in-out group-hover:rounded-[15%] group-hover:shadow-2xl">
        <img src="<?php echo esc_url($image_1); ?>" alt="Premium item 1" class="transition-transform duration-1000 ease-in-out group-hover:scale-125" style="width: 100% !important; height: 100% !important; object-fit: cover !important; max-width: none !important; border-radius: 0 !important; margin: 0 !important;">
    </div>
</div>
<div class="hidden lg:block absolute bottom-24 left-[15%] w-44 h-44 animate-float-slower z-50 group cursor-pointer" style="animation-delay: 2s;">
    <div class="w-full h-full rounded-[50%] border-4 border-white shadow-xl overflow-hidden transition-all duration-1000 ease-in-out group-hover:rounded-[15%] group-hover:shadow-2xl">
        <img src="<?php echo esc_url($image_2); ?>" alt="Premium item 2" class="transition-transform duration-1000 ease-in-out group-hover:scale-125" style="width: 100% !important; height: 100% !important; object-fit: cover !important; max-width: none !important; border-radius: 0 !important; margin: 0 !important;">
    </div>
</div>
<div class="hidden lg:block absolute top-20 right-[15%] w-40 h-40 animate-float-slower z-50 group cursor-pointer" style="animation-delay: 1s;">
    <div class="w-full h-full rounded-[50%] border-4 border-white shadow-xl overflow-hidden transition-all duration-1000 ease-in-out group-hover:rounded-[15%] group-hover:shadow-2xl">
        <img src="<?php echo esc_url($image_3); ?>" alt="Premium item 3" class="transition-transform duration-1000 ease-in-out group-hover:scale-125" style="width: 100% !important; height: 100% !important; object-fit: cover !important; max-width: none !important; border-radius: 0 !important; margin: 0 !important;">
    </div>
</div>
<div class="hidden md:block absolute bottom-12 right-[10%] w-32 h-32 animate-float-slow z-50 group cursor-pointer" style="animation-delay: 3s;">
    <div class="w-full h-full rounded-[50%] border-4 border-white shadow-xl overflow-hidden transition-all duration-1000 ease-in-out group-hover:rounded-[15%] group-hover:shadow-2xl">
        <img src="<?php echo esc_url($image_4); ?>" alt="Premium item 4" class="transition-transform duration-1000 ease-in-out group-hover:scale-125" style="width: 100% !important; height: 100% !important; object-fit: cover !important; max-width: none !important; border-radius: 0 !important; margin: 0 !important;">
    </div>
</div>

<!-- Decorative Elements -->
<div class="absolute -top-20 -left-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl z-0"></div>
<div class="absolute -bottom-20 -right-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl z-0"></div>
<!-- Login/Register Card Container -->
<section class="w-full max-w-[420px] bg-surface-container-lowest cocoa-shadow rounded-xl p-8 sm:p-10 relative z-10 border border-outline-variant/30 backdrop-blur-sm bg-white/95 overflow-hidden transition-all duration-500">
<div id="loginView" class="transition-opacity duration-300">
<div class="text-center mb-10">
<h1 class="font-display-lg text-4xl text-primary italic mb-2"><?php echo esc_html($company_name); ?></h1>
<p class="text-on-surface-variant font-label-md text-label-md tracking-widest uppercase"><?php esc_html_e( 'Welcome Back', 'sugar-story-login' ); ?></p>
</div>
<form class="space-y-6" id="loginForm" method="post">
<!-- Username/Email Field -->
<div class="space-y-2">
<label class="block font-label-md text-label-md text-on-surface-variant ml-1" for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
<div class="relative group">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors">person</span>
<input class="w-full !pl-12 pr-4 py-3.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="username" name="username" placeholder="Enter your credentials" type="text" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"/>
</div>
</div>
<!-- Password Field -->
<div class="space-y-2">
<div class="flex justify-between items-center px-1">
<label class="block font-label-md text-label-md text-on-surface-variant" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
<a class="text-secondary font-label-md text-label-md hover:underline" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
</div>
<div class="relative group">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors">lock</span>
<input class="w-full !pl-12 pr-12 py-3.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="password" name="password" placeholder="••••••••" type="password" required autocomplete="current-password" />
<button class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 hover:text-primary transition-colors bg-transparent hover:bg-transparent focus:bg-transparent active:bg-transparent focus:outline-none focus:ring-0 border-none shadow-none p-0 m-0" onclick="togglePassword('password', 'eyeIcon')" type="button">
<span class="material-symbols-outlined" id="eyeIcon">visibility</span>
</button>
</div>
</div>
<!-- Remember Me -->
<div class="flex items-center gap-3 px-1">
<input class="w-5 h-5 rounded border-on-surface-variant/30 text-primary focus:ring-primary bg-surface-container-low transition-colors" id="rememberme" name="rememberme" type="checkbox" value="forever"/>
<label class="font-body-md text-on-surface-variant cursor-pointer select-none" for="rememberme"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></label>
</div>
<!-- Submit Button -->
<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
<button class="w-full bg-primary-container text-on-primary-container font-label-md text-label-md py-4 rounded-full shadow-sm hover:shadow-md hover:brightness-105 active:scale-[0.98] transition-all duration-200 mt-2 uppercase tracking-widest font-bold" type="submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
                    <?php esc_html_e( 'Log in', 'woocommerce' ); ?>
                </button>
</form>

<?php if ( ! empty( $google_client_id ) ) : ?>
<div class="relative mt-6 mb-4">
    <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-surface-variant"></div>
    </div>
    <div class="relative flex justify-center text-sm">
        <span class="px-2 bg-surface text-on-surface-variant font-label-md tracking-wider uppercase" style="background-color: <?php echo esc_attr($bg_color); ?>;">Or continue with</span>
    </div>
</div>
<a href="<?php echo esc_url( site_url('?sugar_story_google_auth=login') ); ?>" class="w-full flex items-center justify-center gap-3 bg-white text-gray-700 font-label-md text-label-md py-3 rounded-full shadow-sm hover:shadow-md active:scale-[0.98] transition-all duration-200 border border-gray-200 tracking-widest font-bold cursor-pointer" style="text-decoration: none;">
    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo" class="w-5 h-5" />
    Google
</a>
<?php endif; ?>
<div class="mt-8 pt-8 border-t border-surface-variant text-center">
<p class="text-on-surface-variant font-body-md">New to our bakery? 
                    <a class="text-primary font-bold hover:underline cursor-pointer" onclick="toggleForms()">Create an account</a>
</p>
</div>
</div>

<!-- Register View -->
<div id="registerView" class="hidden opacity-0 transition-opacity duration-300">
    <div class="text-center mb-8">
        <h1 class="font-display-lg text-4xl text-primary italic mb-2">Join Us</h1>
        <p class="text-on-surface-variant font-label-md text-label-md tracking-widest uppercase">Create an Account</p>
    </div>
    <form class="space-y-4" id="registerForm" method="post" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
        <!-- Name Field -->
        <div class="space-y-1">
            <label class="block font-label-md text-sm text-on-surface-variant ml-1" for="reg_full_name"><?php esc_html_e( 'Full Name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors text-lg">person</span>
                <input class="w-full !pl-11 pr-4 py-2.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="reg_full_name" name="full_name" placeholder="John Doe" type="text" value="<?php echo ( ! empty( $_POST['full_name'] ) ) ? esc_attr( wp_unslash( $_POST['full_name'] ) ) : ''; ?>" required pattern="[a-zA-Z\s]+" minlength="2" maxlength="50" title="Only letters and spaces are allowed." />
            </div>
        </div>
        
        <!-- Email Field -->
        <div class="space-y-1">
            <label class="block font-label-md text-sm text-on-surface-variant ml-1" for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors text-lg">mail</span>
                <input class="w-full !pl-11 pr-4 py-2.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="reg_email" name="email" placeholder="john@example.com" type="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required autocomplete="email" />
            </div>
        </div>

        <!-- Mobile Field -->
        <div class="space-y-1">
            <label class="block font-label-md text-sm text-on-surface-variant ml-1" for="reg_billing_phone"><?php esc_html_e( 'Mobile Number', 'woocommerce' ); ?> <span class="font-normal text-xs">(Optional)</span></label>
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors text-lg">phone_iphone</span>
                <input class="w-full !pl-11 pr-4 py-2.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="reg_billing_phone" name="billing_phone" placeholder="+91 9876543210" type="tel" value="<?php echo ( ! empty( $_POST['billing_phone'] ) ) ? esc_attr( wp_unslash( $_POST['billing_phone'] ) ) : ''; ?>" pattern="^\+?[0-9\s\-\(\)]+$" minlength="10" maxlength="15" title="Enter a valid phone number (e.g. +1 123 456 7890)" />
            </div>
        </div>
        
        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
        <div class="space-y-1">
            <label class="block font-label-md text-sm text-on-surface-variant ml-1" for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors text-lg">person</span>
                <input class="w-full !pl-11 pr-4 py-2.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="reg_username" name="username" placeholder="Username" type="text" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required autocomplete="username" />
            </div>
        </div>
        <?php endif; ?>

        <!-- Passwords Fields (Stacked) -->
        <div class="space-y-4 mt-2">
            <div class="space-y-1">
                <label class="block font-label-md text-sm text-on-surface-variant ml-1" for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors text-lg">lock</span>
                    <input class="w-full !pl-11 pr-12 py-2.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="reg_password" name="password" placeholder="••••••••" type="password" required autocomplete="new-password" minlength="8" />
                    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 hover:text-primary transition-colors bg-transparent hover:bg-transparent focus:bg-transparent active:bg-transparent focus:outline-none focus:ring-0 border-none shadow-none p-0 m-0" onclick="togglePassword('reg_password', 'eyeIconReg')" type="button">
                    <span class="material-symbols-outlined" id="eyeIconReg">visibility</span>
                    </button>
                </div>
            </div>
            <div class="space-y-1">
                <label class="block font-label-md text-sm text-on-surface-variant ml-1" for="reg_password_confirm"><?php esc_html_e( 'Confirm Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 group-focus-within:text-primary transition-colors text-lg">lock</span>
                    <input class="w-full !pl-11 pr-12 py-2.5 bg-surface-container-low border-none rounded-lg ring-1 ring-on-surface-variant/30 focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50" id="reg_password_confirm" name="password_confirm" placeholder="••••••••" type="password" required minlength="8" />
                    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 hover:text-primary transition-colors bg-transparent hover:bg-transparent focus:bg-transparent active:bg-transparent focus:outline-none focus:ring-0 border-none shadow-none p-0 m-0" onclick="togglePassword('reg_password_confirm', 'eyeIconRegConf')" type="button">
                    <span class="material-symbols-outlined" id="eyeIconRegConf">visibility</span>
                    </button>
                </div>
            </div>
        </div>
        
        <?php do_action( 'woocommerce_register_form' ); ?>

        <!-- Submit Button -->
        <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
        <button class="w-full bg-primary-container text-on-primary-container font-label-md text-label-md py-3 rounded-full shadow-sm hover:shadow-md hover:brightness-105 active:scale-[0.98] transition-all duration-200 mt-4 uppercase tracking-widest font-bold" type="submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
            <?php esc_html_e( 'Register', 'woocommerce' ); ?>
        </button>
    </form>
    
    <?php if ( ! empty( $google_client_id ) ) : ?>
    <div class="relative mt-6 mb-4">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-surface-variant"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-surface text-on-surface-variant font-label-md tracking-wider uppercase" style="background-color: <?php echo esc_attr($bg_color); ?>;">Or continue with</span>
        </div>
    </div>
    <a href="<?php echo esc_url( site_url('?sugar_story_google_auth=login') ); ?>" class="w-full flex items-center justify-center gap-3 bg-white text-gray-700 font-label-md text-label-md py-3 rounded-full shadow-sm hover:shadow-md active:scale-[0.98] transition-all duration-200 border border-gray-200 tracking-widest font-bold cursor-pointer" style="text-decoration: none;">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo" class="w-5 h-5" />
        Google
    </a>
    <?php endif; ?>
    <div class="mt-6 pt-6 border-t border-surface-variant text-center">
        <p class="text-on-surface-variant font-body-md">Already have an account? 
            <a class="text-primary font-bold hover:underline cursor-pointer" onclick="toggleForms()">Log in</a>
        </p>
    </div>
</div>
</section>
</main>
<!-- Micro-interactions Script -->
<script>
        function toggleForms() {
            const loginView = document.getElementById('loginView');
            const registerView = document.getElementById('registerView');
            
            if (loginView.classList.contains('hidden')) {
                // Show Login
                registerView.classList.replace('opacity-100', 'opacity-0');
                setTimeout(() => {
                    registerView.classList.add('hidden');
                    loginView.classList.remove('hidden');
                    // slight delay to allow display block to apply before fading in
                    setTimeout(() => loginView.classList.replace('opacity-0', 'opacity-100'), 50);
                }, 300);
            } else {
                // Show Register
                loginView.classList.replace('opacity-100', 'opacity-0');
                setTimeout(() => {
                    loginView.classList.add('hidden');
                    registerView.classList.remove('hidden');
                    setTimeout(() => registerView.classList.replace('opacity-0', 'opacity-100'), 50);
                }, 300);
            }
        }

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerText = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerText = 'visibility';
            }
        }

        // Add loaders on form submit
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2" style="font-size: 18px; vertical-align: middle;">autorenew</span> <span style="vertical-align: middle;">PLEASE WAIT...</span>';
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                }
            });
        }

        const regForm = document.getElementById('registerForm');
        if (regForm) {
            regForm.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2" style="font-size: 18px; vertical-align: middle;">autorenew</span> <span style="vertical-align: middle;">PLEASE WAIT...</span>';
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                }
            });
        }
    </script>
</div>