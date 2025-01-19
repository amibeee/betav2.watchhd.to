// Define our translations
const translations = {
  EN: {
    'Startseite': 'Home',
    'News': 'News',
    'Hilfe': 'Help',
    'Forum': 'Forum',
    'Bitcoins kaufen (Tutorial)': 'Buy Bitcoins (Tutorial)',
    'Login': 'Login',
    'Sign Up': 'Sign Up',
    'Account': 'Account',
    'Settings': 'Settings',
    'Friend': 'Friend',
    'Verify': 'Verify',
    'Mein Benutzerkonto': 'My Account',
    'Abmelden': 'Logout',
    'Verify 2FA': 'Verify 2FA',
    'Benutzerkonto erstellen': 'Create Account',
    'TV_Guide': 'TV Guide',
    'Support': 'Support'
  },
  DE: {
    'Home': 'Startseite',
    'News': 'News',
    'Help': 'Hilfe',
    'Forum': 'Forum',
    'Buy Bitcoins (Tutorial)': 'Bitcoins kaufen (Tutorial)',
    'Login': 'Anmelden',
    'Sign Up': 'Registrieren',
    'Account': 'Konto',
    'Settings': 'Einstellungen',
    'Friend': 'Freund',
    'Verify': 'Verifizieren',
    'My Account': 'Mein Benutzerkonto',
    'Logout': 'Abmelden',
    'Verify 2FA': '2FA verifizieren',
    'Create Account': 'Benutzerkonto erstellen',
    'TV Guide': 'TV-Programm',
    'Support': 'Unterstützung'
  }
};
  
  let currentLanguage = 'EN';
  
  function changeLanguage(lang) {
    currentLanguage = lang;
    document.querySelectorAll('[data-translate]').forEach(element => {
      const key = element.getAttribute('data-translate');
      element.textContent = translations[lang][key] || key;
    });
    
    // Update language display
    document.getElementById('current-language').textContent = lang;
    document.getElementById('flag-icon').src = `${base_url}assets/assets/icons/${lang === 'EN' ? 'English' : 'German'}flag.svg`;
  }
  
  document.addEventListener('DOMContentLoaded', () => {
    // Set up language switcher
    const languageSwitcher = document.getElementById('language-switcher');
    const dropdownMenu = document.getElementById('dropdown-menu');
  
    languageSwitcher.addEventListener('click', () => {
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });
  
    document.querySelectorAll('.language-option').forEach(option => {
      option.addEventListener('click', (e) => {
        const lang = e.currentTarget.getAttribute('data-language');
        changeLanguage(lang);
        dropdownMenu.style.display = 'none';
      });
    });
  
    // Initial translation
    changeLanguage(currentLanguage);
  });