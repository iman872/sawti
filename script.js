// ==========================================================
// Sawti.ma - Main Frontend Script
// Handles:
// 1) Language switching (AR / FR / AM)
// 2) Direction switching (RTL / LTR)
// 3) Audio autoplay + play/pause button
// 4) Mobile menu toggle
// ==========================================================

document.addEventListener('DOMContentLoaded', function () {
  console.log('✅ Script loaded successfully');

  const htmlEl = document.documentElement;
  const audioEl = document.getElementById('bg-audio');
  const videoEl = document.getElementById('bg-video');
  const audioBtn = document.getElementById('audio-btn');
  const langButtons = document.querySelectorAll('.lang-btn');
  const langDropdown = document.querySelector('.lang-dropdown');
  const langToggleBtn = document.getElementById('lang-toggle');

  console.log('✅ HTML Elements:', {
    audioBtn: !!audioBtn,
    audioEl: !!audioEl,
    langToggleBtn: !!langToggleBtn,
    langButtons: langButtons.length
  });

  const translations = {
    ar: {
      nav_home: 'الرئيسية',
      nav_about: 'حول البوابة',
      nav_services: 'الخدمات',
      nav_faq: 'أسئلة وأجوبة',
      nav_stats: 'إحصائيات',
      nav_numbers: 'أرقام الشكايات',
      nav_submit: 'تقديم شكاية',
      nav_track: 'تتبع شكاية',
      nav_note: 'تقديم ملاحظة',
      nav_suggest: 'تقديم اقتراح',
      nav_login: 'تسجيل الدخول',
      nav_action: 'إرسال شكاية',
      hero_title: 'منصة صوتي',
      hero_desc: 'بوابة إلكترونية لتقديم الشكايات وتتبعها بسهولة وشفافية',
      intro_text:
        'منصة صوتي هي خدمة رقمية تهدف إلى تمكين المواطنين من تقديم شكاياتهم وملاحظاتهم بسهولة، مع إمكانية تتبع حالة الطلب والتفاعل مع الإدارة بطريقة شفافة وسريعة.',
      quote_text:
        '« [...] ومن غير المقبول أن لا تجيب الإدارة على شكايات وتساؤلات الناس وكأن المواطن لا يساوي شيئا. فبدون المواطن لن تكون هناك إدارة. ومن حقه أن يتلقى جوابا عن رسائله وحلولا لمشاكله المعروضة عليها. [...] »',
      quote_source:
        'مقتطف من نص الخطاب الملكي السامي<br>بمناسبة افتتاح البرلمان بتاريخ 2016/10/14',
      steps_title: 'كيفية تقديم الشكاية',
      step1_title: 'إنشاء حساب',
      step1_desc: 'قم بالتسجيل في المنصة باستخدام معلوماتك الشخصية لإنشاء حساب خاص بك.',
      step2_title: 'تقديم الشكاية',
      step2_desc: 'املأ الاستمارة بمعلومات الشكاية وارفق الوثائق الضرورية الداعمة لطلبك.',
      step3_title: 'تتبع الشكاية',
      step3_desc: 'استخدم رقم التتبع الخاص بك لمعرفة مآل الشكاية ومراحل معالجتها.',
      step4_title: 'تلقي الرد',
      step4_desc: 'توصل برد الإدارة المعنية عبر حسابك في المنصة أو عبر البريد الإلكتروني.',
      footer_rights: '© 2024 منصة صوتي. جميع الحقوق محفوظة.',
      audio_label: 'الصوت',
      anthem_label: 'النشيد الوطني المغربي'
    },
    fr: {
      nav_home: 'Accueil',
      nav_about: 'À propos du portail',
      nav_services: 'Services',
      nav_faq: 'Questions & Réponses',
      nav_stats: 'Statistiques',
      nav_numbers: 'Numéros des réclamations',
      nav_submit: 'Déposer une réclamation',
      nav_track: 'Suivre une réclamation',
      nav_note: 'Déposer une observation',
      nav_suggest: 'Déposer une suggestion',
      nav_login: 'Connexion',
      nav_action: 'Envoyer une réclamation',
      hero_title: 'Plateforme Sawti',
      hero_desc: 'Portail électronique pour déposer et suivre les réclamations facilement et en toute transparence',
      intro_text:
        'La plateforme Sawti est un service numérique qui permet aux citoyens de soumettre facilement leurs réclamations et remarques, avec la possibilité de suivre le statut de leur demande et d’interagir avec l’administration de manière transparente et rapide.',
      quote_text:
        '« [...] Il est inacceptable que l’administration ne réponde pas aux plaintes des citoyens. Sans citoyen, il n’y aurait pas d’administration. Le citoyen a droit à une réponse et à des solutions. [...] »',
      quote_source:
        'Extrait du discours royal<br>à l’ouverture du Parlement le 14/10/2016',
      steps_title: 'Comment déposer une réclamation',
      step1_title: 'Créer un compte',
      step1_desc: 'Inscrivez-vous sur la plateforme avec vos informations personnelles.',
      step2_title: 'Déposer la réclamation',
      step2_desc: 'Remplissez le formulaire et joignez les documents nécessaires.',
      step3_title: 'Suivre la réclamation',
      step3_desc: 'Utilisez votre numéro de suivi pour connaître l’état du dossier.',
      step4_title: 'Recevoir la réponse',
      step4_desc: 'Recevez la réponse de l’administration via votre compte ou e-mail.',
      footer_rights: '© 2024 Plateforme Sawti. Tous droits réservés.',
      audio_label: 'Audio',
      anthem_label: 'Hymne National Marocain'
    },
    am: {
      nav_home: 'ⴰⵙⵏⵙ',
      nav_about: 'ⵅⴼ ⵜⴱⵓⵔⵜ',
      nav_services: 'ⵜⵉⵡⵓⵔⵉⵡⵉⵏ',
      nav_faq: 'ⵉⵙⵇⵙⵉⵜⵏ ⴷ ⵜⵔⵔⴰ',
      nav_stats: 'ⵜⵉⵙⵜⴰⵜⵉⵙⵜⵉⴽⵉⵏ',
      nav_numbers: 'ⵉⵎⴹⴰⵏ ⵏ ⵉⵙⵉⴽⴰⵢⵏ',
      nav_submit: 'ⴰⵙⵙⵉⵡⴹ ⵏ ⵓⵙⵉⴽⴰⵢ',
      nav_track: 'ⴰⵎⵓⵣⵣⵓ ⵏ ⵓⵙⵉⴽⴰⵢ',
      nav_note: 'ⴰⵙⵙⵉⵡⴹ ⵏ ⵜⵔⵎⵉⵜ',
      nav_suggest: 'ⴰⵙⵙⵉⵡⴹ ⵏ ⵓⵍⵛⵛⵉⵎ',
      nav_login: 'ⴰⴽⵛⵛⵓⵎ',
      nav_action: 'ⴰⵣⵏ ⴰⵙⵉⴽⴰⵢ',
      hero_title: 'ⵜⴰⵙⴳⴰ ⵏ Sawti',
      hero_desc: 'ⵜⴰⴱⵓⵔⵜ ⵜⴰⵏⵉⵍⴽⵜⵔⵓⵏⵉⵜ ⵉ ⵓⵙⵉⴽⴰⵢ ⴷ ⵓⵎⵓⵣⵣⵓ ⵏⵏⵙ ⵙ ⵜⵉⴼⴰⵡⵜ',
      intro_text:
        'ⵜⴰⵙⴳⴰ Sawti ⴷ ⴰⵎⴰⵣⵣⴰⵏ ⴰⵏⵓⵎⴰⵏ ⵉⵙⵙⴰⵔⴰ ⵉ ⵉⵎⵣⴷⴰⵖ ⴰⴷ ⴰⵙⵉⵡⴹⵏ ⵉⵙⵉⴽⴰⵢⵏ ⵏⵏⵙⵏ ⵙ ⵜⵉⵙⵀⵉⵍⵜ ⴷ ⵓⵎⵓⵣⵣⵓ ⵏ ⵡⴰⴹⴼⴰⵕ.',
      quote_text:
        '« [...] ⵓⵔ ⵉⵇⴱⵉⵍ ⴰⴷ ⵓⵔ ⵜⴻⵜⵜⵓⵔⴰⵔ ⵜⴷⴰⴱⵉⵔⵜ ⵉ ⵉⵙⵉⴽⴰⵢⵏ ⵏ ⵉⵎⵣⴷⴰⵖ. [...] »',
      quote_source:
        'ⴰⴽⴽⵯⵙ ⵙⴳ ⵓⵏⵏⴰⵢ ⴰⴳⵍⴷⴰⵏ',
      steps_title: 'ⵎⴰⵎⴽ ⴰⴷ ⵜⵙⵉⵡⴹⴹ ⴰⵙⵉⴽⴰⵢ',
      step1_title: 'ⵙⵏⵓⵍⴼⵓ ⴰⵎⵉⴹⴰⵏ',
      step1_desc: 'ⴰⵣⵎⵎⴻⵎ ⵖⵔ ⵜⵙⴳⴰ ⵙ ⵉⵙⴰⵍⴰⵏ ⵏⵏⴽ.',
      step2_title: 'ⵙⵉⵡⴹⴹ ⴰⵙⵉⴽⴰⵢ',
      step2_desc: 'ⵙⵎⴷ ⵜⴰⵎⵙⵙⵜⴰⵏⵜ ⴷ ⵙⴻⵏⴼⵍ ⵜⵉⵡⵔⵉⵇⵉⵏ.',
      step3_title: 'ⵎⵓⵣⵣⵓ ⴰⵙⵉⴽⴰⵢ',
      step3_desc: 'ⵙⵙⴻⵎⵔⵙ ⵓⵜⵓⵏ ⵏ ⵓⵎⵓⵣⵣⵓ ⵉ ⵓⵙⵙⵉⵏⵉ ⵏ ⵡⴰⴹⴼⴰⵕ.',
      step4_title: 'ⴰⵡⵉ ⵜⵉⵔⵉⵔⵉ',
      step4_desc: 'ⴰⵡⵉ ⵜⵉⵔⵉⵔⵉ ⵙⴳ ⵜⴷⴰⴱⵉⵔⵜ ⴳ ⵓⵎⵉⴹⴰⵏ ⵏⵏⴽ.',
      footer_rights: '© 2024 Sawti. ⴰⴽⴽ ⵉⵣⵔⴼⴰⵏ ⴷ ⵉⵎⵎⴰⵍ.',
      audio_label: 'ⴰⵎⴰⵙⵍⵉ',
      anthem_label: 'ⵉⵣⵍⵉ ⴰⵏⴰⵎⵓⵔ ⴰⵎⵖⵔⵉⴱⵉ'
    }
  };

  function applyLanguage(lang) {
    const dict = translations[lang] || translations.ar;

    // Update text for all elements with data-i18n
    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      const key = el.getAttribute('data-i18n');
      if (dict[key]) {
        if (dict[key].includes('<br>')) {
          el.innerHTML = dict[key];
        } else {
          el.textContent = dict[key];
        }
      }
    });

    // Update HTML direction and language
    if (lang === 'ar') {
      htmlEl.setAttribute('dir', 'rtl');
    } else {
      htmlEl.setAttribute('dir', 'ltr'); // French and Amazigh are LTR
    }
    htmlEl.setAttribute('lang', lang);

    // Update active language button style
    langButtons.forEach(function (btn) {
      btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
    });

    // Switch royal carousel to correct language slides
    if (window.switchRoyalLang) window.switchRoyalLang(lang);

    // Keep audio button label fixed as requested
    updateAudioButtonLabel();
  }

  function updateAudioButtonLabel() {
    if (!audioBtn) return;
    const isPaused = audioEl ? audioEl.paused : true;
    const iconClass = isPaused ? 'fa-play-circle' : 'fa-pause-circle';

    // Get current translation
    const currentLang = htmlEl.getAttribute('lang') || 'ar';
    const dict = translations[currentLang] || translations.ar;
    const textLabel = dict.anthem_label || 'النشيد الوطني المغربي';

    // Keep flag and update icon + text
    const currentHTML = audioBtn.innerHTML;
    const svgPartMatch = currentHTML.match(/<svg[\s\S]*?<\/svg>/);
    const svgPart = svgPartMatch ? svgPartMatch[0] : '';

    audioBtn.innerHTML = svgPart + ' <i class="fas ' + iconClass + '"></i> ' + textLabel;
  }

  async function tryAutoplayAudio() {
    if (!audioEl) return;
    try {
      audioEl.volume = 0.6;
      await audioEl.play();
    } catch (error) {
      // Autoplay may be blocked by browser until user interaction
      // We keep paused state and let user press the button.
    } finally {
      updateAudioButtonLabel();
    }
  }

  async function tryAutoplayVideo() {
    if (!videoEl) return;
    try {
      videoEl.muted = true;
      videoEl.play().catch(function (error) {
        console.log('Video autoplay failed:', error);
      });
    } catch (error) {
      console.log('Video error:', error);
    }
  }

  // Language button events
  console.log('✅ Language buttons found:', langButtons.length);
  langButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const selectedLang = btn.getAttribute('data-lang');
      console.log('🌐 Language changed to:', selectedLang);
      applyLanguage(selectedLang);

      // Update dropdown toggle text in Arabic UI style
      if (langToggleBtn) {
        if (selectedLang === 'fr') {
          langToggleBtn.innerHTML = 'Français <i class="fas fa-globe"></i>';
        } else if (selectedLang === 'am') {
          langToggleBtn.innerHTML = 'ⵜⴰⵎⴰⵣⵉⵖⵜ <i class="fas fa-globe"></i>';
        } else {
          langToggleBtn.innerHTML = 'العربية <i class="fas fa-globe"></i>';
        }
      }

      // Close dropdown after selection
      if (langDropdown) {
        langDropdown.classList.remove('open');
      }
    });
  });

  // Toggle language dropdown
  if (langToggleBtn && langDropdown) {
    console.log('✅ Language toggle button handler attached');
    langToggleBtn.addEventListener('click', function (event) {
      event.stopPropagation();
      console.log('🔽 Language dropdown toggled');
      langDropdown.classList.toggle('open');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
      if (!langDropdown.contains(event.target)) {
        langDropdown.classList.remove('open');
      }
    });
  }

  // Audio button event
  if (audioBtn && audioEl) {
    console.log('✅ Audio button handler attached');
    audioBtn.addEventListener('click', async function (event) {
      event.preventDefault();
      event.stopPropagation();
      console.log('🔊 Audio button clicked');
      try {
        if (audioEl.paused) {
          console.log('▶️ Playing audio...');
          await audioEl.play();
        } else {
          console.log('⏸️ Pausing audio...');
          audioEl.pause();
        }
      } catch (error) {
        console.error('❌ Audio error:', error);
      } finally {
        updateAudioButtonLabel();
      }
    });
  } else {
    console.error('❌ Audio button or element not found');
  }

  // Mobile menu toggle
  const navbarToggle = document.getElementById('navbar-toggle');
  const navMenu = document.getElementById('nav-menu');
  const navLinks = document.querySelectorAll('.nav-link');

  console.log('✅ Navbar elements:', {
    navbarToggle: !!navbarToggle,
    navMenu: !!navMenu,
    navLinks: navLinks.length
  });

  if (navbarToggle) {
    console.log('✅ Navbar toggle handler attached');
    navbarToggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('☰ Navbar toggled');
      navbarToggle.classList.toggle('active');
      navMenu.classList.toggle('active');
    });

    // Close menu when a link is clicked
    navLinks.forEach(link => {
      link.addEventListener('click', function () {
        navbarToggle.classList.remove('active');
        navMenu.classList.remove('active');

        // Update active state
        navLinks.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
      });
    });
  } else {
    console.error('❌ Navbar toggle button not found');
  }

  // Set initial active link
  const currentPage = window.location.pathname;
  navLinks.forEach(link => {
    if (link.getAttribute('href').includes(currentPage.split('/').pop() || 'index')) {
      link.classList.add('active');
    }
  });

  // ============================================
  // Royal Carousel - Language-aware rotation
  // ============================================
  let royalCurrentIndex = 0;
  let royalAutoplay;
  let royalCurrentLang = 'ar';

  // Per-slide image config
  const royalSlidePhotos = [
    { src: 'm6.jpeg', alt: 'جلالة الملك محمد السادس', caption: '', book: false },
    { src: 'dostoree.jpeg', alt: 'دستور المملكة المغربية', caption: 'دستور المملكة المغربية', book: true },
    { src: 'jarida.jpg', alt: 'الجريدة الرسمية', caption: 'الجريدة الرسمية', book: true }
  ];

  function updateRoyalPhoto(index) {
    const photoImg = document.getElementById('royal-slide-img');
    const photoCaption = document.getElementById('royal-slide-caption');
    const data = royalSlidePhotos[index] || royalSlidePhotos[0];
    if (photoImg) {
      photoImg.src = data.src;
      photoImg.alt = data.alt;
      photoImg.classList.toggle('book-cover', data.book);
    }
    if (photoCaption) {
      photoCaption.textContent = data.caption;
    }
  }

  function getRoyalSlides(lang) {
    return document.querySelectorAll(`.royal-slide[data-lang-slide="${lang}"]`);
  }

  function showRoyalSlide(index, lang) {
    lang = lang || royalCurrentLang;
    const slides = getRoyalSlides(lang);
    const dots = document.querySelectorAll('.royal-dot');

    // Hide all slides
    document.querySelectorAll('.royal-slide').forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));

    // Show current
    if (slides[index]) slides[index].classList.add('active');
    if (dots[index]) dots[index].classList.add('active');

    royalCurrentIndex = index;
    updateRoyalPhoto(index);
  }

  function nextRoyalSlide() {
    const slides = getRoyalSlides(royalCurrentLang);
    showRoyalSlide((royalCurrentIndex + 1) % slides.length);
  }

  function startRoyalAutoplay() {
    clearInterval(royalAutoplay);
    royalAutoplay = setInterval(nextRoyalSlide, 6000);
  }

  // Dot click
  document.querySelectorAll('.royal-dot').forEach((dot, i) => {
    dot.addEventListener('click', function () {
      showRoyalSlide(parseInt(this.getAttribute('data-goto')));
      startRoyalAutoplay();
    });
  });

  // Pause on hover
  const royalWrapper = document.querySelector('.royal-slides-wrapper');
  if (royalWrapper) {
    royalWrapper.addEventListener('mouseenter', () => clearInterval(royalAutoplay));
    royalWrapper.addEventListener('mouseleave', startRoyalAutoplay);
  }

  // Called when language changes - switch to correct language slides
  window.switchRoyalLang = function (lang) {
    royalCurrentLang = lang;
    royalCurrentIndex = 0;
    showRoyalSlide(0, lang);
    startRoyalAutoplay();
  };

  // Start with Arabic
  showRoyalSlide(0, 'ar');
  startRoyalAutoplay();

  // Initialize with Arabic and try to autoplay
  applyLanguage('ar');
  tryAutoplayAudio();
  tryAutoplayVideo();
});
// أضف هذا في نهاية ملف script.js

// تحميل الملفات الخاصة بالصفحة الحالية
document.addEventListener('DOMContentLoaded', function () {
  const currentPage = window.location.pathname;

  // إذا كنا في صفحة submit.php
  if (currentPage.includes('submit.php')) {
    const script = document.createElement('script');
    script.src = 'js/submit.js';
    document.body.appendChild(script);
  }

  // إذا كنا في صفحة form.php
  if (currentPage.includes('form.php')) {
    const script = document.createElement('script');
    script.src = 'js/form.js';
    document.body.appendChild(script);
  }
});