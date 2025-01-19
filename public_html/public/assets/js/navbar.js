  document.addEventListener('DOMContentLoaded', function() {
    const languageSwitcher = document.getElementById('language-switcher');
    const flagIcon = document.getElementById('flag-icon');
    const currentLanguage = document.getElementById('current-language');
    const dropdownMenu = document.getElementById('dropdown-menu');
    const hamburgerButton = document.getElementById('hamburger-button');
    const drawer = document.getElementById('drawer');
    const drawerItems = document.querySelectorAll('.drawer-item');

    // Translation data
    const translations = {
      EN: {
          navbarTitle: "WATCHHD",
          home: "Home",
          news: "News",
          forum: "Forum",
          help: "Help",
          buyBitcoin: "Buy Bitcoin (Tutorial)",
          loginButton: "Login",
          signupButton: "Sign Up",
          pageTitle: "Login",
          welcomeMessage: "Welcome Back!",
          subtitleMessage: "Sign in to continue enjoying high-definition entertainment with WatchHD",
          usernamePlaceholder: "Username",
          passwordPlaceholder: "Password",
          captchaLabel: "I’m not a robot",
          forgotPasswordLink: "Forgot Password?",
          loadingMessage: "Please wait, processing your request...",
          currentLanguage: "EN",
          english: "English",
          german: "Deutsch",
          joinTitle: "Join WatchHD Today!",
          joinDescription: "Create your account to start enjoying high-definition entertainment and stay updated with our latest features and offers."
      },
      DE: {
          navbarTitle: "WATCHHD",
          home: "Startseite",
          news: "Nachrichten",
          forum: "Forum",
          help: "Hilfe",
          buyBitcoin: "Bitcoin kaufen (Tutorial)",
          loginButton: "Einloggen",
          signupButton: "Anmelden",
          pageTitle: "Anmeldung",
          welcomeMessage: "Willkommen zurück!",
          subtitleMessage: "Melden Sie sich an, um weiterhin hochauflösende Unterhaltung mit WatchHD zu genießen",
          usernamePlaceholder: "Benutzername",
          passwordPlaceholder: "Passwort",
          captchaLabel: "Ich bin kein Roboter",
          forgotPasswordLink: "Passwort vergessen?",
          loadingMessage: "Bitte warten, Ihre Anfrage wird bearbeitet...",
          currentLanguage: "DE",
          english: "Englisch",
          german: "Deutsch",
          joinTitle: "Schließen Sie sich WatchHD noch heute an!",
          joinDescription: "Erstellen Sie Ihr Konto, um hochauflösende Unterhaltung zu genießen und über unsere neuesten Funktionen und Angebote informiert zu bleiben."
      }
  };

    // Load the previously selected language or default to English
    let selectedLanguage = localStorage.getItem('language') || 'EN';
    updateTextContent(selectedLanguage);

    function closeDropdownMenu() {
      dropdownMenu.style.display = 'none';
    }

    function closeDrawer() {
      drawer.style.display = 'none';
    }

    function updateTextContent(language) {
      document.querySelectorAll('[data-translate-key]').forEach(element => {
        const key = element.getAttribute('data-translate-key');
        if (translations[language] && translations[language][key]) {
          element.textContent = translations[language][key];
        }
      });

      // Update elements with data-translate attributes
      document.querySelectorAll('[data-translate]').forEach(element => {
        const key = element.getAttribute('data-translate');
        if (translations[language] && translations[language][key]) {
          element.textContent = translations[language][key];
        }
      });

      // Update the flag icon and current language display
      if (language === 'EN') {
        flagIcon.src = 'assets/navbar/assets/icons/Englishflag.svg';
        currentLanguage.textContent = 'EN';
      } else if (language === 'DE') {
        flagIcon.src = 'assets/navbar/assets/icons/Germanflag.svg';
        currentLanguage.textContent = 'DE';
      }

      localStorage.setItem('language', language); // Store selected language
    }

    languageSwitcher.addEventListener('click', function(event) {
      event.stopPropagation();
      if (dropdownMenu.style.display === 'block') {
        closeDropdownMenu();
      } else {
        dropdownMenu.style.display = 'block';
      }
    });

    document.addEventListener('click', function(event) {
      if (!languageSwitcher.contains(event.target)) {
        closeDropdownMenu();
      }
      if (!drawer.contains(event.target) && drawer.style.display === 'flex') {
        closeDrawer();
      }
    });

    dropdownMenu.addEventListener('click', function(event) {
      if (event.target.classList.contains('language-option')) {
        const selectedLanguage = event.target.dataset.language;
        updateTextContent(selectedLanguage); // Update page content
        closeDropdownMenu();
      }
    });

    hamburgerButton.addEventListener('click', function(event) {
      event.stopPropagation();
      if (drawer.style.display === 'flex') {
        closeDrawer();
      } else {
        drawer.style.display = 'flex';
      }
    });

    drawerItems.forEach(item => {
      item.addEventListener('click', function() {
        closeDrawer();
      });
    });
  }); 
  