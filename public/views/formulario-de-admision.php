<?php
// Límite real de subida: se comparte con la validación del servidor.
$maxUploadBytes = \Controllers\AdmissionController::maxUploadBytes();
$maxUploadLabel = number_format($maxUploadBytes / (1024 * 1024), 0) . ' MB';
?>
<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-32 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/barco-ourcompany.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-600/80 to-cyan-700/80"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4"><?php echo __('admission.hero.title'); ?></h1>
                <p class="text-xl text-blue-100"><?php echo __('admission.hero.subtitle'); ?></p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                
                <!-- Header -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border-t-4 border-cyan-500">
                    <div class="flex items-center justify-between mb-6">
                        <img src="/assets/images/logo.png" alt="PAMEL Logo" class="h-20">
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-600">F-46</p>
                                <p class="text-sm text-gray-600">V.02</p>
                            </div>
                            <img src="/assets/images/ssss.png" alt="PAMEL Logo" class="h-20">
                        </div>
                    </div>
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">PANAMA MARITIME E-LEARNING (PAMEL), S.A.</h2>
                        <p class="text-sm text-gray-700 mb-1"><?php echo __('admission.form.quality_system'); ?></p>
                        <p class="text-base font-semibold text-gray-800"><?php echo __('admission.form.header'); ?></p>
                    </div>
                </div>

                <!-- Form -->
                <form id="admissionForm" class="bg-white rounded-2xl shadow-xl p-8" method="POST">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 pb-4 border-b-2 border-gray-200">
                        <?php echo __('admission.form.personal_info'); ?>
                    </h3>

                    <div class="space-y-6">
                        
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.full_name'); ?>
                            </label>
                            <input type="text" name="full_name" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- ID or Passport -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.id_passport'); ?>
                            </label>
                            <input type="text" name="id_passport" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.dob'); ?>
                            </label>
                            <input type="text" name="dob" placeholder="DD/MM/YYYY" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Nationality -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.nationality'); ?>
                            </label>
                            <input type="text" name="nationality" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Country of Birth -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.country_of_birth'); ?>
                            </label>
                            <input type="text" name="country_of_birth" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!--
                            Nivel educativo: lista cerrada en vez de texto libre. El valor
                            que se guarda es la etiqueta en español, que es la que se
                            imprime en el formulario oficial F-41, sin depender del idioma
                            con que el solicitante rellene la página.
                        -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.education_level'); ?>
                            </label>
                            <select name="education_level" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition bg-white">
                                <option value=""><?php echo __('admission.form.education_select'); ?></option>
                                <?php foreach ([
                                    'Primaria'            => 'education_primary',
                                    'Secundaria'          => 'education_secondary',
                                    'Técnico / Vocacional' => 'education_technical',
                                    'Universitario'       => 'education_university',
                                    'Postgrado'           => 'education_postgraduate',
                                    'Otro'                => 'education_other',
                                ] as $valor => $clave): ?>
                                <option value="<?= htmlspecialchars($valor) ?>">
                                    <?php echo __('admission.form.' . $clave); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.email'); ?>
                            </label>
                            <input type="email" name="email" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.phone'); ?>
                            </label>
                            <input type="tel" name="phone" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.address'); ?>
                            </label>
                            <input type="text" name="address" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-6 pt-4 pb-4 border-b-2 border-gray-200">
                            <?php echo __('admission.form.course_info'); ?>
                        </h3>

                        <!-- Elegir cursos: se pueden añadir varios -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="courseSelect">
                                <?php echo __('admission.form.select_course'); ?>
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <!--
                                    Sin name: este desplegable sólo sirve para elegir.
                                    Lo que se envía son los campos ocultos de cada
                                    curso ya añadido, más abajo.
                                -->
                                <select id="courseSelect"
                                    class="flex-1 min-w-0 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition bg-white">
                                    <option value=""><?php echo __('admission.form.select_placeholder'); ?></option>

                                    <?php if (isset($courses) && !empty($courses)): ?>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= htmlspecialchars($course['name'], ENT_QUOTES) ?>"
                                                    data-pid="<?= (int) $course['id'] ?>">
                                                <?= htmlspecialchars($course['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="button" id="addCourse"
                                    class="flex-shrink-0 px-5 py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    <i class="fas fa-plus mr-1"></i><?php echo __('admission.form.add_course'); ?>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2"><?php echo __('admission.form.add_course_hint'); ?></p>
                        </div>

                        <!-- Cursos ya añadidos, cada uno con su modalidad -->
                        <div id="cursosWrap" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.selected_courses'); ?>
                                <span id="cursosCount" class="text-cyan-600"></span>
                            </label>
                            <div id="cursosLista" class="space-y-3"></div>
                        </div>

                        <!--
                            Requisitos de TODOS los cursos elegidos, unificados.
                            Si cuatro cursos piden el pasaporte, aparece una vez.
                        -->
                        <div id="reqPanel" class="hidden rounded-xl border-2 border-cyan-100 bg-cyan-50/40 p-5">
                            <h4 class="text-sm font-bold text-gray-800 mb-1 flex items-center gap-2">
                                <i class="fas fa-clipboard-check text-cyan-600"></i>
                                <?php echo __('admission.form.requirements_title'); ?>
                            </h4>
                            <p class="text-xs text-gray-500 mb-4"><?php echo __('admission.form.requirements_hint'); ?></p>
                            <ul id="reqList" class="space-y-3"></ul>
                        </div>
                        <!--
                            Aquí había dos campos fijos, identificación y certificado
                            médico, visibles desde el principio aunque no se hubiera
                            elegido curso. Ahora los documentos salen de los requisitos
                            del curso, que es donde está escrito qué pide cada uno.
                        -->

                        <!-- Consent Checkbox -->
                        <div class="bg-cyan-50 p-6 rounded-xl border-2 border-cyan-100 mt-8 mb-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="consent" name="consent" type="checkbox" required
                                        class="focus:ring-cyan-500 h-6 w-6 text-cyan-600 border-gray-300 rounded cursor-pointer transition">
                                </div>
                                <div class="ml-4 text-sm">
                                    <label for="consent" class="font-bold text-gray-900 cursor-pointer select-none text-base">
                                        <?php echo __('admission.form.consent_label'); ?>
                                    </label>
                                    <p class="text-gray-600 text-sm mt-1">
                                        <?php echo __('admission.form.consent_text'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6">
                            <button type="submit" id="submitBtn" disabled
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-full font-bold uppercase tracking-wider transition transform shadow-lg text-lg opacity-50 cursor-not-allowed">
                                <?php echo __('admission.form.submit'); ?>
                            </button>
                        </div>

                    </div>
                </form>

                <!-- Privacy Policy -->
                <div class="bg-gray-50 rounded-2xl p-6 mt-8 border-l-4 border-cyan-500">
                    <p class="text-xs text-gray-600 leading-relaxed text-justify">
                        <?php echo __('admission.form.privacy_info'); ?>
                    </p>
                </div>

            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('admissionForm');
    const consentCheckbox = document.getElementById('consent');
    const submitBtn = document.getElementById('submitBtn');

    if (consentCheckbox && submitBtn) {
        consentCheckbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
            if (this.checked) {
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.add('hover:scale-105');
            } else {
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:scale-105');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!consentCheckbox.checked) {
                alert('Debe aceptar los términos para continuar.');
                return;
            }

            // Sin cursos no hay solicitud. El desplegable ya no se envía, así
            // que el "required" del navegador no cubre este caso.
            if (!this.querySelector('input[name="courses[]"]')) {
                alert(<?= json_encode(__('admission.form.no_courses'), JSON_UNESCAPED_UNICODE) ?>);
                document.getElementById('courseSelect').focus();
                return;
            }

            // Se revisa el tamaño aquí para no hacer esperar una subida
            // completa que el servidor va a rechazar igualmente.
            const MAX_BYTES = <?= (int) $maxUploadBytes ?>;
            for (const input of this.querySelectorAll('input[type="file"]')) {
                const archivo = input.files && input.files[0];
                if (archivo && archivo.size > MAX_BYTES) {
                    const pesa  = (archivo.size / (1024 * 1024)).toFixed(1);
                    const tope  = Math.round(MAX_BYTES / (1024 * 1024));
                    // El nombre del campo es un hash; la etiqueta legible va
                    // en data-etiqueta, puesta al pintar el requisito.
                    const campo = input.dataset.etiqueta || input.name;
                    alert('❌ «' + campo + '»: el archivo pesa ' + pesa + ' MB y el máximo es ' + tope + ' MB.\n\n'
                        + 'Comprime el PDF o reduce la resolución del escaneo e inténtalo de nuevo.');
                    input.focus();
                    return;
                }
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> <?php echo __('admission.form.sending'); ?>';
            
            // Prepare form data
            const formData = new FormData(this);
            
            // Submit via AJAX
            fetch('/admission/submit', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text().then(texto => {
                try {
                    return JSON.parse(texto);
                } catch (e) {
                    // Respuesta que no es JSON: casi siempre el servidor web
                    // rechazando el envío por tamaño antes de llegar a PHP.
                    if (response.status === 413) {
                        const tope = Math.round(MAX_BYTES / (1024 * 1024));
                        throw new Error('Los archivos son demasiado grandes para el servidor. '
                            + 'Cada documento debe pesar como máximo ' + tope + ' MB.');
                    }
                    if (response.status === 502 || response.status === 504) {
                        // El servidor cortó la respuesta, pero el envío pudo completarse.
                        throw new Error('No pudimos confirmar el envío por un problema del servidor.\n\n'
                            + 'Es posible que tu solicitud SÍ se haya registrado. '
                            + 'No la envíes de nuevo: escríbenos a info@pamel.edu.pa para confirmarlo.');
                    }
                    throw new Error('El servidor respondió de forma inesperada (código '
                        + response.status + '). Inténtalo de nuevo o escríbenos.');
                }
            }))
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('✅ ' + data.message);
                    // Reset form
                    this.reset();
                    // Reset submit button state
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.classList.remove('hover:scale-105');
                } else {
                    // Show error message
                    alert('❌ ' + (data.message || 'Error'));
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                alert('❌ Error: ' + (error.message || 'Error'));
            })
            .finally(() => {
                // Reset button text
                submitBtn.innerHTML = originalText;
                if(consentCheckbox.checked) {
                    submitBtn.disabled = false;
                }
            });
        });
    }
});
</script>

<script>
    // Requisitos por curso y modo, servidos desde la base.
    const REQUISITOS = <?= json_encode($requirements ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const TEXTOS = {
        adjunto:         <?= json_encode(__('admission.form.req_attach'), JSON_UNESCAPED_UNICODE) ?>,
        paraCursos:      <?= json_encode(__('admission.form.req_for_courses'), JSON_UNESCAPED_UNICODE) ?>,
        paraTodos:       <?= json_encode(__('admission.form.req_for_all'), JSON_UNESCAPED_UNICODE) ?>,
        yaAnadido:       <?= json_encode(__('admission.form.course_already_added'), JSON_UNESCAPED_UNICODE) ?>,
        quitar:          <?= json_encode(__('admission.form.remove_course'), JSON_UNESCAPED_UNICODE) ?>,
        modoNuevo:       <?= json_encode(__('admission.form.mode_new'), JSON_UNESCAPED_UNICODE) ?>,
        modoNuevoPista:  <?= json_encode(__('admission.form.mode_new_hint'), JSON_UNESCAPED_UNICODE) ?>,
        modoRenovar:     <?= json_encode(__('admission.form.mode_refresher'), JSON_UNESCAPED_UNICODE) ?>,
        modoRenovarPista:<?= json_encode(__('admission.form.mode_refresher_hint'), JSON_UNESCAPED_UNICODE) ?>
    };

    (function () {
        const select  = document.getElementById('courseSelect');
        const boton   = document.getElementById('addCourse');
        const wrap    = document.getElementById('cursosWrap');
        const lista   = document.getElementById('cursosLista');
        const cuenta  = document.getElementById('cursosCount');
        const panel   = document.getElementById('reqPanel');
        const reqList = document.getElementById('reqList');
        if (!select || !boton || !panel) { return; }

        // Cursos elegidos: { pid, nombre, modo }. El orden es el de elección.
        const elegidos = [];

        /** ¿Ese curso tiene lista propia de renovación? */
        function tieneRefresco(pid) {
            const def = REQUISITOS[pid];
            return !!(def && (def.refresher || []).length);
        }

        function anadir() {
            const opt = select.options[select.selectedIndex];
            const pid = opt ? opt.getAttribute('data-pid') : null;
            if (!pid) { return; }

            if (elegidos.some(c => c.pid === pid)) {
                alert(TEXTOS.yaAnadido);
                return;
            }

            elegidos.push({ pid: pid, nombre: opt.textContent.trim(), modo: 'new' });
            select.selectedIndex = 0;
            pintarCursos();
            pintarRequisitos();
        }

        function quitar(pid) {
            const i = elegidos.findIndex(c => c.pid === pid);
            if (i >= 0) { elegidos.splice(i, 1); }
            pintarCursos();
            pintarRequisitos();
        }

        // ── Lista de cursos, cada uno con su modalidad ──────────────────
        function pintarCursos() {
            lista.innerHTML = '';
            wrap.classList.toggle('hidden', elegidos.length === 0);
            cuenta.textContent = elegidos.length ? '(' + elegidos.length + ')' : '';

            elegidos.forEach(function (c) {
                const caja = document.createElement('div');
                caja.className = 'rounded-lg border-2 border-cyan-200 bg-white p-4';

                // Cabecera: nombre y botón de quitar
                const cab = document.createElement('div');
                cab.className = 'flex items-start justify-between gap-3 mb-3';

                const titulo = document.createElement('p');
                titulo.className = 'text-sm font-bold text-gray-800 flex-1 min-w-0';
                titulo.textContent = c.nombre;
                cab.appendChild(titulo);

                const quita = document.createElement('button');
                quita.type = 'button';
                quita.className = 'flex-shrink-0 w-7 h-7 rounded-lg text-gray-400 hover:bg-rose-50 hover:text-rose-600 transition';
                quita.title = TEXTOS.quitar;
                quita.innerHTML = '<i class="fas fa-times text-sm"></i>';
                quita.addEventListener('click', function () { quitar(c.pid); });
                cab.appendChild(quita);

                caja.appendChild(cab);

                // Los campos que se envían de verdad.
                const hid = document.createElement('input');
                hid.type = 'hidden';
                hid.name = 'courses[]';
                hid.value = c.pid;
                caja.appendChild(hid);

                // Modalidad propia: se puede renovar uno y estrenar otro.
                const modos = document.createElement('div');
                modos.className = 'grid grid-cols-1 sm:grid-cols-2 gap-2';

                [['new', TEXTOS.modoNuevo, TEXTOS.modoNuevoPista],
                 ['refresher', TEXTOS.modoRenovar, TEXTOS.modoRenovarPista]].forEach(function (m) {
                    // El refresco sólo se ofrece si ese curso tiene su lista.
                    if (m[0] === 'refresher' && !tieneRefresco(c.pid)) { return; }

                    const et = document.createElement('label');
                    et.className = 'flex items-start gap-2.5 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-cyan-400 transition has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-50';

                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'course_mode[' + c.pid + ']';
                    radio.value = m[0];
                    radio.checked = (c.modo === m[0]);
                    radio.className = 'mt-0.5 w-4 h-4 text-cyan-600 focus:ring-cyan-500';
                    radio.addEventListener('change', function () {
                        c.modo = m[0];
                        pintarRequisitos();
                    });
                    et.appendChild(radio);

                    const txt = document.createElement('span');
                    txt.innerHTML = '<span class="block text-xs font-bold text-gray-800"></span>'
                                  + '<span class="block text-[11px] text-gray-500 mt-0.5"></span>';
                    txt.children[0].textContent = m[1];
                    txt.children[1].textContent = m[2];
                    et.appendChild(txt);

                    modos.appendChild(et);
                });

                caja.appendChild(modos);
                lista.appendChild(caja);
            });
        }

        // ── Requisitos de todos los cursos, unificados ──────────────────
        function pintarRequisitos() {
            // Lo que ya haya adjuntado no se pierde al añadir o quitar un
            // curso: se guarda por grupo y se vuelve a poner si sigue
            // haciendo falta.
            const yaSubidos = {};
            reqList.querySelectorAll('input[type="file"]').forEach(function (i) {
                if (i.files && i.files.length) { yaSubidos[i.dataset.grupo] = i.files; }
            });

            // Agrupar por `grupo`, que PHP calcula a partir del texto del
            // requisito. Dos cursos que piden "ID / PASSPORT" traen el mismo
            // grupo aunque sus ids de requisito sean distintos.
            const grupos = new Map();

            elegidos.forEach(function (c) {
                const def = REQUISITOS[c.pid];
                if (!def) { return; }

                (def[c.modo] || []).forEach(function (r) {
                    let g = grupos.get(r.grupo);
                    if (!g) {
                        g = { grupo: r.grupo, label: r.label, adjunto: r.adjunto, cursos: [] };
                        grupos.set(r.grupo, g);
                    }
                    // Si un curso lo pide con documento y otro no, se pide.
                    if (r.adjunto) { g.adjunto = true; }
                    if (g.cursos.indexOf(c.nombre) === -1) { g.cursos.push(c.nombre); }
                });
            });

            reqList.innerHTML = '';

            if (!grupos.size) { panel.classList.add('hidden'); return; }
            panel.classList.remove('hidden');

            // Primero lo que hay que adjuntar: es lo que da trabajo.
            const items = Array.from(grupos.values()).sort(function (a, b) {
                return (b.adjunto - a.adjunto) || a.label.localeCompare(b.label);
            });

            items.forEach(function (r) {
                const li = document.createElement('li');
                li.className = 'flex items-start gap-3 text-sm';

                const icono = document.createElement('i');
                icono.className = r.adjunto
                    ? 'fas fa-paperclip text-cyan-600 mt-1 flex-shrink-0'
                    : 'fas fa-circle-info text-gray-400 mt-1 flex-shrink-0';
                li.appendChild(icono);

                const cuerpo = document.createElement('div');
                cuerpo.className = 'flex-1 min-w-0';

                const texto = document.createElement('p');
                texto.className = 'text-gray-700';
                texto.textContent = r.label;
                cuerpo.appendChild(texto);

                // Con varios cursos, se dice cuáles lo piden: así se entiende
                // que un solo documento vale para todos ellos.
                if (elegidos.length > 1) {
                    const quien = document.createElement('p');
                    quien.className = 'text-[11px] text-cyan-700 mt-0.5';
                    quien.textContent = r.cursos.length === elegidos.length
                        ? TEXTOS.paraTodos
                        : TEXTOS.paraCursos.replace(':cursos', r.cursos.join(', '));
                    cuerpo.appendChild(quien);
                }

                if (r.adjunto) {
                    const campo = document.createElement('input');
                    campo.type = 'file';
                    campo.name = 'req_file[' + r.grupo + ']';
                    campo.dataset.grupo = r.grupo;
                    campo.dataset.etiqueta = r.label;
                    campo.accept = '.jpg,.jpeg,.png,.pdf';
                    campo.className = 'mt-2 w-full text-xs px-3 py-2 border-2 border-gray-300 rounded-lg bg-white focus:border-cyan-500 focus:outline-none';
                    if (yaSubidos[r.grupo]) { campo.files = yaSubidos[r.grupo]; }
                    cuerpo.appendChild(campo);

                    const pista = document.createElement('p');
                    pista.className = 'text-[11px] text-gray-500 mt-1';
                    pista.textContent = TEXTOS.adjunto;
                    cuerpo.appendChild(pista);
                }

                li.appendChild(cuerpo);
                reqList.appendChild(li);
            });
        }

        boton.addEventListener('click', anadir);
        // Añadir con Enter sin que se envíe el formulario entero.
        select.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); anadir(); }
        });

        pintarCursos();
        pintarRequisitos();
    })();
</script>
