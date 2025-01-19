// Universal Navigation Functions
function navigateToHome() {
  window.location.href = `home.html`;
}

function navigateToLogin(){
  window.location.href = 'login.html'
}
function navigateToSignUp (){
  window.location.href = 'signup.html'
}

// Assigning navigation to buttons
document.addEventListener('DOMContentLoaded', function () {
  // Replace static calls with dynamic navigation
  document.getElementById('joinNowButton')?.addEventListener('click', function () {
    window.location.href = 'signup.html';
  });

  document.getElementById('getStartedButton')?.addEventListener('click', function () {
    window.location.href = 'signup.html';
  });

  document.getElementById('NavButtonHome')?.addEventListener('click', function () {
    window.location.href = 'home.html'
  })

  document.getElementById('NavButtonNews')?.addEventListener('click', function () {
    window.location.href = 'News.html'
  })

  document.getElementById('NavButtonHelp')?.addEventListener('click', function () {
    window.location.href = 'help.html'
  })

  document.getElementById('NavButtonForum')?.addEventListener('click', function() {
    window.open('https://forum.watchhd.to/', '_blank', 'noopener,noreferrer');
  });
  
  document.getElementById('NavButtonBtc')?.addEventListener('click', function() {
    window.open('https://www.xup.in/dl,19741857/watchhd_Krypto_TUT.pdf/', '_blank', 'noopener,noreferrer');
  });
  

  // Navbar Scroll Background Adjuster
  window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');

    // Ensure navbar exists on the page before manipulating
    if (navbar) {
      if (window.scrollY > 150) {
        navbar.style.background = 'rgba(15, 14, 200, 0.95)';
      } else {
        navbar.style.background = 'rgba(15, 14, 20, 0.8)';
      }
    }
  });

  // Language Switcher and Drawer functionality
  const languageSwitcher = document.getElementById('language-switcher');
  const flagIcon = document.getElementById('flag-icon');
  const currentLanguage = document.getElementById('current-language');
  const dropdownMenu = document.getElementById('dropdown-menu2');
  const hamburgerButton = document.getElementById('hamburger-button');
  const drawer = document.getElementById('drawer');
  const drawerItems = document.querySelectorAll('.drawer-item');

  function closeDropdownMenu() {
    dropdownMenu.style.display = 'none';
  }

  function closeDrawer() {
    drawer.style.display = 'none';
  }

  if (languageSwitcher) {
    languageSwitcher.addEventListener('click', function (event) {
      event.stopPropagation();
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });
  }

  document.addEventListener('click', function (event) {
    // Close dropdown menu if clicking outside
    if (dropdownMenu && !languageSwitcher.contains(event.target)) {
      closeDropdownMenu();
    }

    // Close drawer if clicking outside
    if (drawer && !drawer.contains(event.target) && drawer.style.display === 'flex') {
      closeDrawer();
    }
  });

  if (dropdownMenu) {
    dropdownMenu.addEventListener('click', function (event) {
      if (event.target.classList.contains('language-option')) {
        const selectedLanguage = event.target.dataset.language;
        if (selectedLanguage === 'EN') {
          flagIcon.src = '../assets/assets/icons/Englishflag.svg';
          currentLanguage.textContent = 'EN';
        } else if (selectedLanguage === 'DE') {
          flagIcon.src = '../assets/assets/icons/Germanflag.svg';
          currentLanguage.textContent = 'DE';
        }
        closeDropdownMenu();
      }
    });
  }

  if (hamburgerButton) {
    hamburgerButton.addEventListener('click', function (event) {
      event.stopPropagation();
      drawer.style.display = drawer.style.display === 'flex' ? 'none' : 'flex';
    });
  }

  drawerItems.forEach(item => {
    item.addEventListener('click', closeDrawer);
  });
});
