
    //back to top button
    document.addEventListener('DOMContentLoaded', function () {
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    
        window.addEventListener('scroll', function () {
            if (window.scrollY > 200) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
    
        scrollToTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    
    
    
    // Array of image URLs
    const images = [
        'assets/Home/assets/img/heroimg.jpg',
        'assets/Home/assets/img/heroimg2.jpg',
        'assets/Home/assets/img/heroimg3.jpg',
        'assets/Home/assets/img/heroimg4.jpg'
    ];

    let currentIndex = 0;
    const heroElement = document.querySelector('.hero');
    const backgroundLayers = [];

    // Create and append background layers
    images.forEach((image, index) => {
        const layer = document.createElement('div');
        layer.classList.add('hero__background');
        layer.style.backgroundImage = `url(${image})`;
        if (index === 0) {
            layer.classList.add('fade-in');
        } else {
            layer.classList.add('fade-out');
        }
        heroElement.appendChild(layer);
        backgroundLayers.push(layer);
    });

    function changeBackgroundImage() {
        // Get current and next background layers
        const currentLayer = backgroundLayers[currentIndex];
        const nextIndex = (currentIndex + 1) % images.length;
        const nextLayer = backgroundLayers[nextIndex];

        // Apply fade-out to the current layer
        currentLayer.classList.remove('fade-in');
        currentLayer.classList.add('fade-out');

        // Apply fade-in to the next layer
        nextLayer.classList.remove('fade-out');
        nextLayer.classList.add('fade-in');

        // Update the current index
        currentIndex = nextIndex;
    }

    // Change the background image every 4 seconds
    setInterval(changeBackgroundImage, 3000);




    //animation pricing

    document.addEventListener('DOMContentLoaded', function () {
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
                        element.style.animation = 'none'; // Reset the animation
                    });
                }
            });
        }, {
            threshold: 0.1 // Trigger when 10% of the section is in view
        });
    
        observer.observe(pricingSection);
    });
     
    
    //animation benifits
    document.addEventListener("DOMContentLoaded", function () {
        const leftItems = document.querySelectorAll(".benefits__column:first-child .benefit-item");
        const rightItems = document.querySelectorAll(".benefits__column:last-child .benefit-item");
    
        const observerOptions = {
            threshold: 0.1
        };
    
        const leftObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('slide-in-left');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
    
        const rightObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('slide-in-right');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
    
        leftItems.forEach(item => {
            leftObserver.observe(item);
        });
    
        rightItems.forEach(item => {
            rightObserver.observe(item);
        });
    });
    

    //carrousell infint


    const track = document.querySelector('.carousel-track');
let cloneFirst = track.firstElementChild.cloneNode(true);
let cloneLast = track.lastElementChild.cloneNode(true);
track.appendChild(cloneFirst);
track.insertBefore(cloneLast, track.firstElementChild);
