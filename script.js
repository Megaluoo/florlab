document.addEventListener('DOMContentLoaded', () => {

    // --- VARIABLES GLOBALES ---
    let bcvRate = 0;
    let allTests = [];

    // --- SELECTORES DE ELEMENTOS ---
    const loader = document.getElementById('loader');
    const header = document.getElementById('header');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.getElementById('mainNav');
    const closeMenu = document.getElementById('closeMenu');
    const labAnimation = document.getElementById('labAnimation');
    const toast = document.getElementById('toast');
    const resultsForm = document.getElementById('resultsForm');
    const doctorPortalBtn = document.getElementById('doctorPortalBtn');
    const doctorPortalModal = document.getElementById('doctorPortalModal');
    const closeDoctorPortal = document.getElementById('closeDoctorPortal');
    const portalNavItems = document.querySelectorAll('.portal-nav-item');
    const portalSections = document.querySelectorAll('.portal-section');
    const doctorLoginForm = document.getElementById('doctorLoginForm');
    const requestHomeServiceBtn = document.getElementById('requestHomeService');
    const statNumbers = document.querySelectorAll('.stat-number');
    const searchInput = document.getElementById("test-search");
    const searchResults = document.getElementById("search-results");
    const bcvDisplay = document.getElementById("bcv-rate-display");

    /* ---------- Loader ---------- */
    window.addEventListener('load', () => {
        setTimeout(() => {
            if (loader) loader.classList.add('hidden');
            if (typeof createLabAnimation === 'function') createLabAnimation();
        }, 500);
    });

    /* ---------- Menú móvil ---------- */
    if (mobileMenuToggle && mainNav && closeMenu) {
        mobileMenuToggle.addEventListener('click', () => mainNav.classList.add('active'));
        closeMenu.addEventListener('click', () => mainNav.classList.remove('active'));
        mainNav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => mainNav.classList.remove('active')));
    }

    /* ---------- Header Scroll ---------- */
    window.addEventListener('scroll', () => {
        if (header) header.classList.toggle('scrolled', window.scrollY > 50);
    });

    /* ---------- Smooth Scroll ---------- */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', e => {
            e.preventDefault();
            const targetId = anchor.getAttribute('href');
            if (!targetId || targetId === '#') return;
            const target = document.querySelector(targetId);
            if (!target) return;
            const offset = header ? header.offsetHeight : 80;
            const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top, behavior: 'smooth' });
            if (mainNav) mainNav.classList.remove('active');
        });
    });

    /* ---------- Toast ---------- */
    function showToast(message, type = 'success') {
        if (!toast) return;
        const msg = toast.querySelector('.toast-message');
        const icon = toast.querySelector('.toast-icon i');
        msg.textContent = message;
        toast.className = 'toast ' + type;
        icon.className = type === 'success'
            ? 'fas fa-check-circle'
            : type === 'error'
            ? 'fas fa-exclamation-circle'
            : 'fas fa-info-circle';
        toast.offsetHeight;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }

    /* ---------- Utilidades ---------- */
    const normalizeText = t => t ? t.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") : "";
    const formatCurrency = n => isNaN(n) ? '---' : new Intl.NumberFormat("es-VE",{minimumFractionDigits:2,maximumFractionDigits:2}).format(n);

    /* ---------- BCV ---------- */
    async function fetchBCVRate() {
        try {
            const res = await fetch('https://ve.dolarapi.com/v1/dolares/oficial');
            if (!res.ok) throw new Error();
            const data = await res.json();
            bcvRate = parseFloat(data.promedio);
            bcvDisplay.textContent = formatCurrency(bcvRate);
        } catch {
            bcvDisplay.textContent = "No disponible";
            bcvDisplay.parentElement.style.color = "#c62828";
        }
    }

    /* ---------- Tests ---------- */
    async function loadTestData() {
        try {
            const res = await fetch("tests.json?v=" + Date.now());
            allTests = await res.json();
        } catch {
            allTests = [];
            showToast('No se pudieron cargar los exámenes', 'error');
        }
    }

    /* ---------- Buscador ---------- */
    function handleSearch() {
        if (!searchInput || !searchResults || !allTests) return;
        const query = normalizeText(searchInput.value);
        searchResults.innerHTML = "";
        if (query.length < 2) return searchResults.style.display = "none";
        const filtered = allTests.filter(t => normalizeText(t.name).includes(query) || t.keywords.some(k => normalizeText(k).startsWith(query)));
        searchResults.style.display = "block";
        filtered.length > 0
            ? filtered.forEach(test => {
                const usd = parseFloat(test.price_usd);
                const bs = bcvRate ? usd * bcvRate : 0;
                const div = document.createElement("div");
                div.className = "result-item";
                div.innerHTML = `<span class="result-item-name">${test.name}</span>
                                 <span class="result-item-price">
                                 <span class="price-usd">$${formatCurrency(usd)}</span>
                                 <span class="price-bcv">Bs. ${bs > 0 ? formatCurrency(bs) : '---'}</span></span>`;
                div.addEventListener('click',()=>{searchInput.value=test.name;searchResults.style.display='none';});
                searchResults.appendChild(div);
            })
            : searchResults.innerHTML = `<div class="result-item"><span>No se encontraron exámenes.</span></div>`;
    }

    if(searchInput){
        searchInput.addEventListener("input", handleSearch);
        searchInput.addEventListener("focus", handleSearch);
    }

    document.addEventListener("click", e => {
        if (!e.target.closest(".search-wrapper") && searchResults)
            searchResults.style.display = "none";
    });

    /* ---------- Formulario resultados ---------- */
    if (resultsForm) {
        resultsForm.addEventListener('submit', e => {
            const input = document.getElementById('order-number');
            if (!input.value) {
                e.preventDefault();
                showToast('Por favor, ingresa tu número de orden.', 'error');
            } else showToast('Procesando tu solicitud...', 'info');
        });
    }

    /* ---------- Carrusel automático de testimonios ---------- */
    const SPEED = 30;
    const viewport = document.querySelector('.testimonials-viewport');
    const track = document.querySelector('.testimonials-track');

    if (viewport && track) {
        const cards = Array.from(track.children);
        const viewportWidth = viewport.clientWidth;

        // Duplica tarjetas hasta tener un ancho suficiente
        let i = 0;
        while (track.scrollWidth < viewportWidth * 2 && i < 10) {
            cards.forEach(c => track.appendChild(c.cloneNode(true)));
            i++;
        }

        let pos = 0, last = null, running = true;

        viewport.addEventListener('mouseenter', () => running = false);
        viewport.addEventListener('mouseleave', () => running = true);

        function loop(t) {
            if (!last) last = t;
            const dt = (t - last) / 1000;
            last = t;
            if (running) {
                pos += SPEED * dt;
                if (pos >= track.scrollWidth / 2) pos -= track.scrollWidth / 2;
                track.style.transform = `translateX(${-pos}px)`;
            }
            requestAnimationFrame(loop);
        }
        requestAnimationFrame(loop);
    }

    /* ---------- Portal Médico y animaciones ---------- */
    if (doctorPortalBtn && doctorPortalModal && closeDoctorPortal) {
        doctorPortalBtn.addEventListener('click', e => {
            e.preventDefault();
            doctorPortalModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
        closeDoctorPortal.addEventListener('click', () => {
            doctorPortalModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
        window.addEventListener('click', e => {
            if (e.target === doctorPortalModal) {
                doctorPortalModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    if (portalNavItems && portalSections) {
        portalNavItems.forEach(item => item.addEventListener('click', function() {
            const id = this.getAttribute('data-section');
            portalNavItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            portalSections.forEach(s => s.classList.remove('active'));
            document.getElementById(id)?.classList.add('active');
        }));
    }

    if (doctorLoginForm) {
        doctorLoginForm.addEventListener('submit', e => {
            e.preventDefault();
            showToast('Iniciando sesión (demo)...', 'success');
            setTimeout(() => showToast('Acceso al portal médico (demo).', 'info'), 1500);
            e.target.reset();
        });
    }

    if (requestHomeServiceBtn)
        requestHomeServiceBtn.addEventListener('click', e => {
            e.preventDefault();
            showToast('Solicitud recibida. Te contactaremos pronto.', 'success');
        });

    /* ---------- Animación contadores ---------- */
    function animateCounter(el) {
        const target = +el.getAttribute('data-target');
        let current = 0;
        const duration = 1500, step = target / (duration / 10);
        const update = () => {
            current += step;
            if (current < target) {
                el.innerText = Math.ceil(current).toLocaleString();
                setTimeout(update, 10);
            } else el.innerText = target.toLocaleString();
        };
        update();
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                if (e.target.classList.contains('stat-number')) animateCounter(e.target);
                else e.target.style.animation = 'fadeIn 0.8s ease forwards';
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.feature-card, .service-card, .testimonial-card, .stat-number')
        .forEach(el => observer.observe(el));

    /* ---------- Animación laboratorio ---------- */
    function createLabAnimation() {
        if (!labAnimation) return;
        for (let i = 0; i < 15; i++) {
            const m = document.createElement('div');
            m.className = 'molecule';
            const size = 5 + Math.random() * 15;
            Object.assign(m.style, {
                width: `${size}px`, height: `${size}px`,
                left: `${Math.random() * 100}%`,
                top: `${Math.random() * 100}%`,
                animationDelay: `${Math.random() * 5}s`,
                animationDuration: `${10 + Math.random() * 10}s`,
                opacity: `${0.1 + Math.random() * 0.2}`
            });
            labAnimation.appendChild(m);
        }
    }

    /* ---------- Inicialización ---------- */
    (async () => {
        await fetchBCVRate();
        await loadTestData();
    })();

});
