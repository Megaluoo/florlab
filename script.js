document.addEventListener('DOMContentLoaded', () => {

    // --- VARIABLES GLOBALES DEL PROYECTO ANTERIOR ---
    let bcvRate = 0;
    let allTests = [];

    // --- SELECTORES DE ELEMENTOS ---
    const header = document.querySelector('header');
    const searchInput = document.getElementById("test-search");
    const searchResults = document.getElementById("search-results");
    const bcvDisplay = document.getElementById("bcv-rate-display");

    // --- FUNCIONES DE SCROLL Y ANIMACIÓN (DEL NUEVO DISEÑO) ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // --- FUNCIONES DE DATOS (DEL PROYECTO ANTERIOR) ---

    /* --- Utilidades --- */
    const normalizeText = t => t ? t.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") : "";
    const formatCurrency = n => isNaN(n) ? '---' : new Intl.NumberFormat("es-VE",{minimumFractionDigits:2,maximumFractionDigits:2}).format(n);

    /* --- BCV --- */
    async function fetchBCVRate() {
        // Usamos la API pública ya que 'api_bcv.php' no correrá en GitHub Pages
        try {
            const res = await fetch('https://ve.dolarapi.com/v1/dolares/oficial');
            if (!res.ok) throw new Error();
            const data = await res.json();
            bcvRate = parseFloat(data.promedio);
            if (bcvDisplay) {
                bcvDisplay.textContent = formatCurrency(bcvRate);
            }
        } catch {
            if (bcvDisplay) {
                bcvDisplay.textContent = "No disponible";
                bcvDisplay.parentElement.style.color = "#c62828";
            }
        }
    }

    /* --- Cargar Tests --- */
    async function loadTestData() {
        try {
            // Usamos Date.now() para evitar problemas de caché
            const res = await fetch("tests.json?v=" + Date.now()); 
            allTests = await res.json();
        } catch {
            allTests = [];
            if (searchResults) {
                searchResults.innerHTML = `<div class="no-results">No se pudieron cargar los exámenes.</div>`;
                searchResults.style.display = "block";
            }
        }
    }

    /* --- Buscador Dinámico --- */
    function handleSearch() {
        if (!searchInput || !searchResults || !allTests) return;
        
        const query = normalizeText(searchInput.value);
        searchResults.innerHTML = "";
        
        if (query.length < 2) {
            searchResults.style.display = "none";
            return;
        }

        const filtered = allTests.filter(t => 
            normalizeText(t.name).includes(query) || 
            t.keywords.some(k => normalizeText(k).startsWith(query))
        );
        
        searchResults.style.display = "block";

        if (filtered.length > 0) {
            filtered.forEach(test => {
                const usd = parseFloat(test.price_usd);
                const bs = bcvRate ? usd * bcvRate : 0;
                const div = document.createElement("div");
                div.className = "result-item";
                div.innerHTML = `<span class="result-item-name">${test.name}</span>
                                 <span class="result-item-price">
                                    <span class="price-usd">$${formatCurrency(usd)}</span>
                                    <span class="price-bcv">Bs. ${bs > 0 ? formatCurrency(bs) : '---'}</span>
                                 </span>`;
                // Opcional: al hacer clic, se rellena el input
                div.addEventListener('click', () => {
                    searchInput.value = test.name;
                    searchResults.style.display = 'none';
                });
                searchResults.appendChild(div);
            });
        } else {
            searchResults.innerHTML = `<div class="no-results">No se encontraron exámenes con ese nombre.</div>`;
        }
    }

    if (searchInput) {
        searchInput.addEventListener("input", handleSearch);
        searchInput.addEventListener("focus", handleSearch);
    }

    // Ocultar resultados si se hace clic fuera
    document.addEventListener("click", e => {
        if (!e.target.closest(".tests-container") && searchResults) {
            searchResults.style.display = "none";
        }
    });

    /* --- Inicialización de Datos --- */
    (async () => {
        await fetchBCVRate();
        await loadTestData();
        // Llama a handleSearch por si el usuario ya había escrito algo (ej. autocompletar)
        handleSearch(); 
    })();

});
