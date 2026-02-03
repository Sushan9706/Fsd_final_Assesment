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
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            mobileOverlay.classList.toggle('active');
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });

        mobileOverlay.addEventListener('click', () => {
            navMenu.classList.remove('active');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }



    // Multiple files preview for additional images
    const imagesInput = document.getElementById('images');
    const imagesPreview = document.getElementById('images-preview');
    if (imagesInput && imagesPreview) {
        imagesInput.addEventListener('change', (e) => {
            imagesPreview.innerHTML = '';
            const files = Array.from(e.target.files).slice(0, 3);
            files.forEach(file => {
                const reader = new FileReader();
                const wrap = document.createElement('div');
                wrap.className = 'thumb-item';
                const img = document.createElement('img');
                img.width = 120;
                img.alt = 'preview';
                reader.onload = function(ev) {
                    img.src = ev.target.result;
                }
                reader.readAsDataURL(file);
                wrap.appendChild(img);
                imagesPreview.appendChild(wrap);
            });
        });
    }
});
