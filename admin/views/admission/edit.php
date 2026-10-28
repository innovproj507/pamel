<?php
/**
 * Corrección de una solicitud de admisión.
 *
 * Aquí se arreglan los datos del solicitante y la fecha en que entró la
 * solicitud. El estado NO se toca desde aquí: se cambia pulsando su
 * pastilla en el listado, que es donde está a mano.
 */
$request           = $request        ?? [];
$requestCourses    = $requestCourses ?? [];
$nivelesEducativos = $nivelesEducativos ?? [];

$id = (int) $request['id'];

/*
    La fecha de nacimiento está guardada como texto libre y en la base
    conviven «22/10/1998», «30-08-2000» y hasta «13/08/01983». Un selector
    de fecha las dejaría todas en blanco y al guardar se perderían, así
    que va en un campo de texto y se respeta lo que haya.
*/
$nacimiento = (string) ($request['date_of_birth'] ?? '');

// created_at sí es una marca de tiempo de verdad.
$creada = !empty($request['created_at'])
    ? date('Y-m-d\TH:i', strtotime($request['created_at']))
    : '';
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager/admission-requests/<?= $id ?>" class="btn-atras" title="Volver a la solicitud">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Editar solicitud</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            Nº <?= $id ?> ·
            <?= htmlspecialchars(trim($request['given_name'] . ' ' . $request['surname'])) ?>
        </p>
    </div>
</div>

<form method="POST" action="/manager/admission-requests/<?= $id ?>/update" class="space-y-4">
    <?= \Core\Security::getCsrfField() ?>

    <!-- Datos del solicitante -->
    <div class="card card-pad">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[.09em] mb-4">
            Datos del solicitante
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="filtro-rot" for="e-nombre">Nombre <span class="text-rose-500">*</span></label>
                <input type="text" id="e-nombre" name="given_name" required maxlength="100" class="campo w-full"
                       value="<?= htmlspecialchars($request['given_name'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="filtro-rot" for="e-apellido">Apellidos</label>
                <input type="text" id="e-apellido" name="surname" maxlength="100" class="campo w-full"
                       value="<?= htmlspecialchars($request['surname'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="filtro-rot" for="e-pasaporte">Pasaporte / Cédula</label>
                <input type="text" id="e-pasaporte" name="passport_id" maxlength="50" class="campo w-full"
                       value="<?= htmlspecialchars($request['passport_id'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="filtro-rot" for="e-nacionalidad">Nacionalidad</label>
                <input type="text" id="e-nacionalidad" name="nationality" maxlength="50" class="campo w-full"
                       value="<?= htmlspecialchars($request['nationality'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="filtro-rot" for="e-nacimiento">Fecha de nacimiento</label>
                <input type="text" id="e-nacimiento" name="date_of_birth" maxlength="20" class="campo w-full"
                       value="<?= htmlspecialchars($nacimiento, ENT_QUOTES) ?>"
                       placeholder="dd/mm/aaaa">
                <p class="text-[10.5px] font-semibold text-slate-400 mt-1">
                    Campo de texto: las solicitudes guardadas usan formatos distintos y un
                    selector de fecha las borraría.
                </p>
            </div>

            <div>
                <label class="filtro-rot" for="e-pais">País de nacimiento</label>
                <input type="text" id="e-pais" name="country_of_birth" maxlength="50" class="campo w-full"
                       value="<?= htmlspecialchars($request['country_of_birth'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="filtro-rot" for="e-nivel">Nivel educativo</label>
                <select id="e-nivel" name="education_level" class="campo w-full">
                    <option value="">Sin indicar</option>
                    <?php foreach ($nivelesEducativos as $n): ?>
                    <option value="<?= htmlspecialchars($n, ENT_QUOTES) ?>"
                            <?= ($request['education_level'] ?? '') === $n ? 'selected' : '' ?>>
                        <?= htmlspecialchars($n) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="filtro-rot" for="e-capacidad">Capacidad / Cargo</label>
                <input type="text" id="e-capacidad" name="capacity" maxlength="100" class="campo w-full"
                       value="<?= htmlspecialchars($request['capacity'] ?? '', ENT_QUOTES) ?>">
            </div>
        </div>
    </div>

    <!-- Contacto -->
    <div class="card card-pad">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[.09em] mb-4">Contacto</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="filtro-rot" for="e-correo">Correo electrónico <span class="text-rose-500">*</span></label>
                <input type="email" id="e-correo" name="email" required maxlength="100" class="campo w-full"
                       value="<?= htmlspecialchars($request['email'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="filtro-rot" for="e-telefono">Teléfono</label>
                <input type="text" id="e-telefono" name="phone" maxlength="30" class="campo w-full"
                       value="<?= htmlspecialchars($request['phone'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div class="md:col-span-2">
                <label class="filtro-rot" for="e-direccion">Dirección</label>
                <textarea id="e-direccion" name="address" rows="2" class="campo w-full"
                          style="height:auto; padding:9px 11px; line-height:1.5"><?= htmlspecialchars($request['address'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Fecha de la solicitud -->
    <div class="card card-pad">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[.09em] mb-4">
            Fecha de la solicitud
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="filtro-rot" for="e-creada">Recibida el</label>
                <input type="datetime-local" id="e-creada" name="created_at" class="campo w-full"
                       value="<?= htmlspecialchars($creada, ENT_QUOTES) ?>">
                <p class="text-[10.5px] font-semibold text-slate-400 mt-1">
                    Es la fecha que sale en el listado. Déjala vacía para no cambiarla.
                </p>
            </div>
        </div>
    </div>

    <!-- Cursos y estado: de sólo lectura, se cambian en otro sitio -->
    <div class="card card-pad">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[.09em] mb-4">
            Cursos solicitados
        </h3>

        <?php if ($requestCourses): ?>
        <div class="flex flex-wrap gap-2">
            <?php foreach ($requestCourses as $c): ?>
            <span class="pastilla <?= ($c['mode'] ?? 'new') === 'refresher' ? 'p-violeta' : 'p-azul' ?>">
                <?= htmlspecialchars($c['course_name']) ?>
                · <?= ($c['mode'] ?? 'new') === 'refresher' ? 'renovación' : 'primera vez' ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-[12.5px] text-slate-400"><?= htmlspecialchars($request['course'] ?? '—') ?></p>
        <?php endif; ?>

        <p class="text-[10.5px] font-semibold text-slate-400 mt-3">
            Los cursos y el estado no se editan aquí: el estado se cambia pulsando su
            pastilla en el listado de solicitudes.
        </p>
    </div>

    <!-- Acciones -->
    <div class="flex items-center gap-2.5 flex-wrap">
        <button type="submit" class="btn-primario">
            <i data-lucide="save" class="ic ic-16"></i> Guardar cambios
        </button>
        <a href="/manager/admission-requests/<?= $id ?>" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Cancelar
        </a>
    </div>
</form>
