document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.querySelector('.slider-container');
    const wrapper = document.querySelector('.trending-wrapper');
    const slides = document.querySelectorAll('.trending-card');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const playPauseBtn = document.querySelector('.play-pause-btn');
    
    const slideCount = slides.length;
    let currentSlide = 0;
    let isPlaying = true;
    let slideInterval;
    let isTransitioning = false;

    function updateSlidePosition(instant = false) {
        const slideWidth = slides[0].offsetWidth + 16; // Width + gap
        if (instant) {
            wrapper.style.transition = 'none';
        }
        wrapper.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
        if (instant) {
            wrapper.offsetHeight; // Force reflow
            wrapper.style.transition = 'transform 0.6s ease';
        }
    }

    function updateDots() {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide % 3);
        });
    }

    function nextSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        currentSlide++;
        
        if (currentSlide >= slideCount - 1) {
            currentSlide = 0;
        }
        
        updateSlidePosition();
        updateDots();
        
        setTimeout(() => {
            isTransitioning = false;
        }, 600);
    }

    function prevSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        currentSlide--;
        
        if (currentSlide < 0) {
            currentSlide = slideCount - 2;
        }
        
        updateSlidePosition();
        updateDots();
        
        setTimeout(() => {
            isTransitioning = false;
        }, 600);
    }

    function startAutoplay() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 4000); // 4 seconds interval
    }

    function stopAutoplay() {
        if (slideInterval) {
            clearInterval(slideInterval);
            slideInterval = null;
        }
    }

    // Event Listeners
    prevBtn.addEventListener('click', () => {
        prevSlide();
        if (isPlaying) {
            stopAutoplay();
            startAutoplay();
        }
    });

    nextBtn.addEventListener('click', () => {
        nextSlide();
        if (isPlaying) {
            stopAutoplay();
            startAutoplay();
        }
    });

    playPauseBtn.addEventListener('click', () => {
        isPlaying = !isPlaying;
        const icon = playPauseBtn.querySelector('i');
        icon.className = isPlaying ? 'mdi mdi-pause' : 'mdi mdi-play';
        if (isPlaying) {
            startAutoplay();
        } else {
            stopAutoplay();
        }
    });

    // Touch events
    let touchStartX = 0;
    let touchEndX = 0;

    wrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        stopAutoplay();
    });

    wrapper.addEventListener('touchmove', (e) => {
        if (isTransitioning) return;
        touchEndX = e.touches[0].clientX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 5) {
            e.preventDefault();
            const slideWidth = slides[0].offsetWidth + 16;
            wrapper.style.transform = `translateX(-${(currentSlide * slideWidth) + diff}px)`;
        }
    });

    wrapper.addEventListener('touchend', () => {
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else {
            updateSlidePosition();
        }
        if (isPlaying) startAutoplay();
    });

    // Mouse hover events
    wrapper.addEventListener('mouseenter', () => {
        if (isPlaying) stopAutoplay();
    });

    wrapper.addEventListener('mouseleave', () => {
        if (isPlaying) startAutoplay();
    });

    // Initialize
    updateSlidePosition();
    if (isPlaying) startAutoplay();
});