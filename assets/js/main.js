document.addEventListener('DOMContentLoaded', () => {
    console.log('Real Estate Platform Loaded');

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.property-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });
    // Mobile Menu Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const mobileOverlay = document.getElementById('mobile-overlay');

    if (menuToggle && navMenu && mobileOverlay) {
        const toggleIcon = menuToggle.querySelector('i');

        menuToggle.addEventListener('click', () => {
            const isActive = navMenu.classList.toggle('active');
            mobileOverlay.classList.toggle('active');

            if (isActive) {
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-times');
                document.body.style.overflow = 'hidden';
            } else {
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
                document.body.style.overflow = '';
            }
        });

        mobileOverlay.addEventListener('click', () => {
            navMenu.classList.remove('active');
            mobileOverlay.classList.remove('active');
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-bars');
            document.body.style.overflow = '';
        });
    }
});
