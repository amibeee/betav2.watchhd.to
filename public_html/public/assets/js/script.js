// Utility function for DOM content loaded
function onDOMContentLoaded(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
}

// Back to top button
onDOMContentLoaded(() => {
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');

    window.addEventListener('scroll', () => {
        scrollToTopBtn.classList.toggle('show', window.scrollY > 200);
    });

    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// Hero background image carousel
onDOMContentLoaded(() => {
    const images = [
        '/assets/assets/img/heroimg.jpg',
        '/assets/assets/img/heroimg2.jpg',
        '/assets/assets/img/heroimg3.jpg',
        '/assets/assets/img/heroimg4.jpg'
    ];

    let currentIndex = 0;
    const heroElement = document.querySelector('.hero');
    const backgroundLayers = [];

    images.forEach((image, index) => {
        const layer = document.createElement('div');
        layer.classList.add('hero__background');
        layer.style.backgroundImage = `url(${image})`;
        layer.classList.add(index === 0 ? 'fade-in' : 'fade-out');
        heroElement.appendChild(layer);
        backgroundLayers.push(layer);
    });

    function changeBackgroundImage() {
        const currentLayer = backgroundLayers[currentIndex];
        const nextIndex = (currentIndex + 1) % images.length;
        const nextLayer = backgroundLayers[nextIndex];

        currentLayer.classList.remove('fade-in');
        currentLayer.classList.add('fade-out');

        nextLayer.classList.remove('fade-out');
        nextLayer.classList.add('fade-in');

        currentIndex = nextIndex;
    }

    setInterval(changeBackgroundImage, 3000);
});

// Pricing section animation
onDOMContentLoaded(() => {
    const pricingSection = document.querySelector('.pricing');
    const elements = document.querySelectorAll('.pricing__content > *');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                elements.forEach((element, index) => {
                    setTimeout(() => {
                        element.style.animation = `slideIn 0.8s ease-out forwards, fadeIn 0.8s ease-out forwards`;
                    }, index * 200);
                });
            } else {
                elements.forEach((element) => {
                    element.style.animation = 'none';
                });
            }
        });
    }, { threshold: 0.1 });

    observer.observe(pricingSection);
});

// Benefits section animation
onDOMContentLoaded(() => {
    const leftItems = document.querySelectorAll(".benefits__column:first-child .benefit-item");
    const rightItems = document.querySelectorAll(".benefits__column:last-child .benefit-item");

    const observerOptions = { threshold: 0.1 };

    const createObserver = (className) => {
        return new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(className);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
    };

    const leftObserver = createObserver('slide-in-left');
    const rightObserver = createObserver('slide-in-right');

    leftItems.forEach(item => leftObserver.observe(item));
    rightItems.forEach(item => rightObserver.observe(item));
});

// Carousel
onDOMContentLoaded(() => {
    const track = document.querySelector('.carousel-track');
    if (track) {
        const cloneFirst = track.firstElementChild.cloneNode(true);
        const cloneLast = track.lastElementChild.cloneNode(true);
        track.appendChild(cloneFirst);
        track.insertBefore(cloneLast, track.firstElementChild);
    }
});

// Notification functions
function closeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.style.display = 'none';
    }
}




