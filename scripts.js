document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Custom Cursor ---
    const cursorDot = document.getElementById('cursor-dot');
    const cursorGlow = document.getElementById('cursor-glow');
    
    window.addEventListener('mousemove', (e) => {
        cursorDot.style.left = `${e.clientX}px`;
        cursorDot.style.top = `${e.clientY}px`;
        
        // Slight delay for the glow
        setTimeout(() => {
            cursorGlow.style.left = `${e.clientX}px`;
            cursorGlow.style.top = `${e.clientY}px`;
        }, 50);
    });

    // --- 2. Scroll Reveal Observer ---
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.cinematic-reveal').forEach(el => revealObserver.observe(el));

    // --- 3. Header Scrolled State ---
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
    });

    // --- 4. Background Particles (Pastel) ---
    const particlesContainer = document.getElementById('particles-bg');
    if (particlesContainer) {
        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle-pastel');
            particle.style.width = `${Math.random() * 20 + 10}px`;
            particle.style.height = particle.style.width;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.animationDuration = `${Math.random() * 15 + 15}s`;
            particle.style.animationDelay = `${Math.random() * 5}s`;
            particlesContainer.appendChild(particle);
        }
    }

    // --- 5. Social Proof Counters ---
    const counters = document.querySelectorAll('.proof-number');
    let countersStarted = false;

    const startCounters = () => {
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const duration = 2000; // ms
            const stepTime = Math.abs(Math.floor(duration / target));
            let current = 0;
            
            const timer = setInterval(() => {
                current += Math.ceil(target / 100);
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                // Format appropriately
                if(target === 5000) counter.innerText = `+${current.toLocaleString()}`;
                else if(target === 100) counter.innerText = `${current}%`;
                else if(target === 24) counter.innerText = `< ${current}h`;
            }, stepTime);
        });
    };

    const counterObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !countersStarted) {
            countersStarted = true;
            startCounters();
        }
    });
    if(document.querySelector('.social-proof')) {
        counterObserver.observe(document.querySelector('.social-proof'));
    }

    // --- 6. Bento Grid Logic with Molecular Scan ---
    const searchInput = document.getElementById('bento-search');
    const filterPills = document.querySelectorAll('.filter-pill');
    const bentoCards = document.querySelectorAll('.bento-card');
    const scanBar = document.getElementById('scan-bar');

    const filterCards = (category, searchTerm) => {
        // Trigger scanning effect
        scanBar.classList.remove('scanning');
        void scanBar.offsetWidth; // trigger reflow
        scanBar.classList.add('scanning');

        setTimeout(() => {
            bentoCards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                const cardTitle = card.querySelector('.bento-title').innerText.toLowerCase();
                const term = searchTerm.toLowerCase();

                if ((category === 'all' || cardCat === category) && cardTitle.includes(term)) {
                    card.style.display = 'flex';
                    card.style.animation = 'chatEnter 0.4s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        }, 250); // halfway through scan animation
    };

    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            filterPills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            filterCards(pill.getAttribute('data-filter'), searchInput ? searchInput.value : '');
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const activePill = document.querySelector('.filter-pill.active');
            filterCards(activePill ? activePill.getAttribute('data-filter') : 'all', e.target.value);
        });
    }

    // Hover logic for Bento cards
    const bentoGrid = document.getElementById('bento-grid');
    if (bentoGrid) {
        bentoGrid.onmousemove = e => {
            for(const card of document.getElementsByClassName("bento-card")) {
                const rect = card.getBoundingClientRect(),
                      x = e.clientX - rect.left,
                      y = e.clientY - rect.top;
                card.style.setProperty("--hover-x", `${x}px`);
                card.style.setProperty("--hover-y", `${y}px`);
            };
        };
    }

    // --- 7. Instagram Testimonials Simulator ---
    const igContainer = document.getElementById('ig-container');
    
    const testimonials = [
        { handle: "carlos_m92", text: "Me entregaron resultados el mismo día 🙌 Impresionante el nivel de tecnología.", time: "2 h" },
        { handle: "andyp_fit", text: "El laboratorio más moderno de Maracay sin duda. El trato es top 🔥", time: "5 h" },
        { handle: "valeriag_mom", text: "Excelente para mi bebé. Cero dolor y mucha paciencia. Recomendados 100%.", time: "1 d" },
        { handle: "dr_luisr", text: "Precisión impecable. Siempre refiero a mis pacientes a florlab.lc", time: "2 d" }
    ];

    let currentTestimonial = 0;

    const createIgComment = (data) => {
        const comment = document.createElement('div');
        comment.classList.add('ig-comment');
        
        // Use a generic placeholder that looks like a real IG profile pic (UI Faces API or similar, using random gradient for safety)
        const hue = Math.floor(Math.random() * 360);
        
        comment.innerHTML = `
            <div class="ig-avatar" style="background: hsl(${hue}, 70%, 80%);"></div>
            <div class="ig-content">
                <div>
                    <a href="#" class="ig-user">${data.handle}</a>
                    <span class="ig-text">${data.text}</span>
                </div>
                <div class="ig-actions">
                    <span>${data.time}</span>
                    <span>Responder</span>
                    <span>Ver traducción</span>
                </div>
            </div>
            <div class="ig-heart"></div>
        `;
        return comment;
    };

    const nextTestimonial = () => {
        if (!igContainer) return;

        const comment = createIgComment(testimonials[currentTestimonial]);
        igContainer.appendChild(comment);

        // Keep max 4 comments visible
        const comments = document.querySelectorAll('.ig-comment');
        if (comments.length > 4) {
            comments[0].style.display = 'none';
            comments[0].parentNode.removeChild(comments[0]);
        }

        currentTestimonial = (currentTestimonial + 1) % testimonials.length;
    };

    // Start testimonials loop when section is visible
    let testimonialsStarted = false;
    const testimonialsObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !testimonialsStarted) {
            testimonialsStarted = true;
            nextTestimonial(); // first one immediately
            setInterval(nextTestimonial, 3500); // New comment every 3.5 seconds
        }
    });
    
    const testimonialsSection = document.getElementById('testimonios');
    if (testimonialsSection) testimonialsObserver.observe(testimonialsSection);

});
