document.addEventListener('DOMContentLoaded', () => {

    // --- 1. DARK MODE TOGGLE (State Management) ---
    const themeBtn = document.getElementById('theme-toggle');
    const htmlEl = document.documentElement;
    const icon = themeBtn.querySelector('i');

    // LocalStorage'dan önceki tercihi kontrol et
    const savedTheme = localStorage.getItem('theme') || 'dark';
    htmlEl.setAttribute('data-theme', savedTheme);
    updateIcon(savedTheme);

    themeBtn.addEventListener('click', () => {
        const currentTheme = htmlEl.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        htmlEl.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme); // Tercihi kaydet
        updateIcon(newTheme);
    });

    function updateIcon(theme) {
        if (theme === 'dark') {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    }

    // --- 2. AJAX ILE PROJELERİ ÇEKME (Fetch API) ---
    const gridContainer = document.getElementById('portfolio-grid');

    async function loadProjects() {
        try {
            const response = await fetch('get_projects.php');
            const result = await response.json();

            if (result.status === 'success') {
                gridContainer.innerHTML = ''; // Loading text'i temizle

                result.data.forEach(project => {
                    const card = document.createElement('div');
                    card.className = 'project-card fade-in'; // Animasyon ve kart stili

                    // project-info div'i eklendi!
                    card.innerHTML = `
                        <img src="${project.image_url}" alt="${project.title}">
                        <div class="project-info">
                            <h3>${project.title}</h3>
                            <span class="tech-stack">${project.tech_stack}</span>
                            <p>${project.description}</p>
                        </div>
                    `;
                    gridContainer.appendChild(card);
                });
            } else {
                gridContainer.innerHTML = `<p>${result.message}</p>`;
            }
        } catch (error) {
            gridContainer.innerHTML = `<p>Error loading projects. Check your server connection.</p>`;
        }
    }

    loadProjects(); // Sayfa açılınca projeleri yükle

    // --- 3. FORM VALIDASYONU VE AJAX SUBMIT ---
    const contactForm = document.getElementById('contact-form');
    const statusMsg = document.getElementById('form-status');

    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Sayfanın yenilenmesini engelle

        // Form verilerini al
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();

        // Basit Frontend Validasyonu
        if (!name || !email || !message) {
            showStatus('Please fill in all fields.', 'red');
            return;
        }

        const btn = contactForm.querySelector('button');
        btn.innerHTML = 'Sending... <i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            // PHP API'sine veriyi gönder
            const response = await fetch('submit_contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, message })
            });

            const result = await response.json();

            if (result.status === 'success') {
                showStatus(result.message, 'var(--accent-color)');
                contactForm.reset(); // Formu temizle
            } else {
                showStatus(result.message, 'red');
            }
        } catch (error) {
            showStatus('Connection error. Please try again.', 'red');
        } finally {
            btn.innerHTML = 'Send Message <i class="fa-solid fa-paper-plane"></i>';
            btn.disabled = false;
        }
    });

    function showStatus(text, color) {
        statusMsg.style.color = color;
        statusMsg.textContent = text;
        setTimeout(() => statusMsg.textContent = '', 5000); // 5 saniye sonra gizle
    }
    // --- 4. SCROLL REVEAL (AŞAĞI KAYDIRDIKÇA YÜKLENME) ---
    const revealElements = document.querySelectorAll('.reveal');

    // Ayarlar: Elementin %15'i ekranda göründüğünde tetikle
    const revealOptions = {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealOnScroll = new IntersectionObserver(function (entries, observer) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return; // Ekranda değilse hiçbir şey yapma
            }
            // Ekrana girdiyse 'active' sınıfını ekle (Görünür yap)
            entry.target.classList.add('active');

            // Animasyon bir kere çalıştıktan sonra takibi bırak (Performans için)
            observer.unobserve(entry.target);
        });
    }, revealOptions);

    // Sayfadaki tüm .reveal sınıfına sahip öğeleri izlemeye başla
    revealElements.forEach(el => {
        revealOnScroll.observe(el);
    });
});