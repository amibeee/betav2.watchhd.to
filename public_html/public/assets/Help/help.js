function closeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    notification.style.display = 'none';
}

// Optionally, you can make the notifications appear only if they are not already closed
window.onload = function() {
    // If you want notifications to be shown every time, no need to add any condition here
    // If you want to check for some condition, you can do it here
};


$(document).ready(function() {
    $('.help-item').click(function() {
        $(this).toggleClass('active');
        $(this).find('.help-answer').css('max-height', $(this).find('.help-answer').css('max-height') === '0px' ? $(this).find('.help-answer')[0].scrollHeight + 'px' : '0px');
        $(this).find('.expand-icon').text($(this).hasClass('active') ? '+' : '+');
    });

    $('nav a').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 70
        }, 500);
    });
});

    //back to top button
    document.addEventListener('DOMContentLoaded', function () {
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    
        window.addEventListener('scroll', function () {
            if (window.scrollY > 100) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
    
        scrollToTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });