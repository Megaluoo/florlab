<?php
require_once 'includes/db.php';
$tests_catalog = $pdo->query("SELECT * FROM tests_catalog ORDER BY category, name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlorLab | Precisión Clínica del Futuro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Custom Cursor -->
    <div class="cursor-dot" id="cursor-dot"></div>
    <div class="cursor-glow" id="cursor-glow"></div>

    <header>
        <nav>
            <a href="#" class="logo">
                <img src="logo_transparent.png" alt="FlorLab Logo">
                <div class="logo-text">
                    <span class="company-name">Laboratorio Clínico</span>
                    <span class="brand-name">Florlab C.A.</span>
                </div>
            </a>
            <ul>
                <li><a href="#innovacion">Innovación</a></li>
                <li><a href="#autoridad">Autoridad</a></li>
                <li><a href="#testimonios">Reseñas</a></li>
                <li><a href="#catalogo">Catálogo</a></li>
                <li class="nav-cta"><a href="#portal">Portal VIP</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Background Particles -->
        <div class="particles-bg" id="particles-bg"></div>

        <!-- HERO -->
        <section class="hero">
            <div class="hero-text cinematic-reveal">
                <span class="subheadline">Precisión Molecular Avanzada</span>
                <h1>La certeza que tu salud <span class="text-gradient">merece.</span></h1>
                <p>Cada resultado es una decisión clínica. Aquí no hay margen de error. Experimenta la tecnología de un laboratorio diseñado para el futuro, hoy en Maracay.</p>
                <div class="cta-group">
                    <a href="https://wa.me/584243524393" class="cta-primary">Agendar Cita VIP</a>
                    <a href="#catalogo" class="cta-secondary">Ver Pruebas</a>
                </div>
            </div>
            <div class="hero-image cinematic-reveal" style="transition-delay: 0.2s;">
                <img src="logo_transparent.png" alt="Símbolo FlorLab">
            </div>
        </section>

        <!-- SOCIAL PROOF -->
        <div class="social-proof cinematic-reveal">
            <div class="proof-item">
                <span class="proof-number" data-target="5000">+0</span>
                <span class="proof-label">Pacientes Atendidos</span>
            </div>
            <div class="proof-item">
                <span class="proof-number" data-target="100">0%</span>
                <span class="proof-label">Precisión Diagnóstica</span>
            </div>
            <div class="proof-item">
                <span class="proof-number" data-target="24">&lt; 0h</span>
                <span class="proof-label">Entrega Promedio</span>
            </div>
        </div>

        <!-- INNOVACIÓN -->
        <section id="innovacion" class="section">
            <h2 class="section-title cinematic-reveal">El Laboratorio del <span class="text-accent">Futuro</span></h2>
            <p class="section-subtitle cinematic-reveal">Tecnología de escaneo molecular y procesos automatizados que garantizan la pureza de cada muestra.</p>
            
            <div class="why-us-grid">
                <div class="feature-card cinematic-reveal">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                    <h3>PCR en Tiempo Real</h3>
                    <p>Detección de patógenos a nivel de ADN/ARN. Resultados indiscutibles cuando la precisión es cuestión de vida o muerte.</p>
                </div>
                <div class="feature-card cinematic-reveal" style="transition-delay: 0.1s;">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <h3>Encriptación de Salud</h3>
                    <p>Tus resultados viajan a tu teléfono mediante un portal blindado, respetando tu privacidad al más alto nivel corporativo.</p>
                </div>
                <div class="feature-card cinematic-reveal" style="transition-delay: 0.2s;">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <h3>Protocolo Indoloro</h3>
                    <p>Extracción de muestras realizada por especialistas con técnicas pediátricas y herramientas ultra-finas.</p>
                </div>
            </div>
        </section>

        <!-- AUTORIDAD -->
        <section id="autoridad" class="authority-section section">
            <div class="authority-container cinematic-reveal">
                <div class="authority-image">
                    <img src="https://images.unsplash.com/photo-1594824436998-0522b10959ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Lic. María Gabriela">
                </div>
                <div class="authority-content">
                    <div class="badge-anim">🔬 Aval Científico Activo</div>
                    <h2>Lic. María Gabriela</h2>
                    <h3>Bióloga Molecular Titulada</h3>
                    <p class="validation-line">"Dirección científica que valida cada resultado emitido."</p>
                    <p>La tecnología necesita una mente maestra. Pionera en Aragua en Screening Neonatal y análisis de alta complejidad, la Licenciada aporta un rigor analítico inigualable en la región, supervisando personalmente cada reporte.</p>
                </div>
            </div>
        </section>

        <!-- TESTIMONIOS (Instagram Style) -->
        <section id="testimonios" class="testimonials-section section">
            <h2 class="section-title cinematic-reveal">Lo que dicen <span class="text-accent">en tiempo real</span></h2>
            <p class="section-subtitle cinematic-reveal">Opiniones reales de pacientes en nuestro Instagram oficial.</p>
            
            <div class="ig-container" id="ig-container">
                <div class="ig-header">
                    <img src="logo_transparent.png" alt="FlorLab">
                    <strong>florlab.lc</strong>
                    <span>• Seguidores reales</span>
                </div>
                <!-- Comments will be injected via JS here -->
            </div>
        </section>

        <!-- BENTO GRID CATALOG -->
        <section id="catalogo" class="section">
            <h2 class="section-title cinematic-reveal">Diccionario <span class="text-gradient">Clínico</span></h2>
            <p class="section-subtitle cinematic-reveal">Explora nuestro arsenal diagnóstico con la interfaz más rápida del mercado.</p>
            
            <div class="catalog-app cinematic-reveal">
                <div class="catalog-sidebar">
                    <div class="search-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="bento-search" placeholder="Buscar prueba...">
                    </div>
                    <div class="filter-pills" id="filter-pills">
                        <button class="filter-pill active" data-filter="all">Todas las Pruebas <span>→</span></button>
                        <button class="filter-pill" data-filter="molecular">Biología Molecular <span>→</span></button>
                        <button class="filter-pill" data-filter="hematologia">Hematología <span>→</span></button>
                        <button class="filter-pill" data-filter="quimica">Química Sanguínea <span>→</span></button>
                        <button class="filter-pill" data-filter="hormonas">Hormonas <span>→</span></button>
                        <button class="filter-pill" data-filter="inmunologia">Inmunología <span>→</span></button>
                        <button class="filter-pill" data-filter="rutina">Rutina Especial <span>→</span></button>
                    </div>
                </div>
                
                <div class="catalog-content">
                    <div class="molecular-scan" id="scan-bar"></div>
                    <div class="bento-grid" id="bento-grid">
                        <?php foreach($tests_catalog as $test): ?>
                            <?php 
                                $steps = explode("\n", $test['steps']); 
                            ?>
                            <div class="bento-card" data-category="<?= htmlspecialchars($test['category']) ?>">
                                <span class="bento-category"><?= htmlspecialchars($test['category']) ?></span>
                                <h3 class="bento-title"><?= htmlspecialchars($test['name']) ?></h3>
                                <?php if(!empty($test['steps'])): ?>
                                <ul class="bento-steps">
                                    <?php foreach($steps as $step): ?>
                                        <?php if(trim($step) !== ''): ?>
                                            <li><?= htmlspecialchars(trim($step)) ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                                <span class="bento-req"><?= htmlspecialchars($test['requirements'] ?: 'Sin requisito especial') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- VIP EXPERIENCE & PORTAL MOCKUP -->
        <section id="portal" class="vip-section section">
            <div class="vip-card cinematic-reveal">
                <h2 class="section-title">Esto no es un laboratorio tradicional.</h2>
                <p class="section-subtitle" style="color: var(--brand-blue); font-weight: 600;">Atención privada. Resultados digitales encriptados. Confianza absoluta.</p>
                
                <div class="portal-preview cinematic-reveal">
                    <form action="portal/login.php" method="POST" class="portal-mockup">
                        <div class="mockup-header-container">
                            <h3 class="mockup-header">Portal VIP de Pacientes</h3>
                            <div class="pulse-indicator"></div>
                        </div>
                        <p style="margin-bottom: 2rem; color: var(--text-muted); font-size:0.95rem;">Ingresa tu cédula para descargar tu informe médico confidencial.</p>
                        
                        <div class="input-group">
                            <label>Cédula de Identidad</label>
                            <input type="text" name="dni" class="mockup-input" placeholder="Ej: V-12345678" required>
                        </div>
                        
                        <div class="input-group">
                            <label>Contraseña Asignada</label>
                            <input type="password" name="password" class="mockup-input" placeholder="Tu contraseña segura" required>
                        </div>
                        
                        <button type="submit" class="mockup-btn">
                            Acceder al Portal Seguro
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- FINAL CTA -->
        <section class="cta-final">
            <h2 class="cinematic-reveal">Tu salud no admite esperas.</h2>
            <a href="https://wa.me/584243524393" class="cta-giant cinematic-reveal">Agenda en menos de 30 segundos</a>
        </section>
    </main>

    <footer>
        <img src="logo_transparent.png" alt="FlorLab" class="footer-logo">
        <p style="color: var(--text-muted);">&copy; 2026 FlorLab. Todos los derechos reservados. Desarrollo Clínico Nivel Enterprise.</p>
    </footer>

    <script src="scripts.js"></script>
</body>
</html>
