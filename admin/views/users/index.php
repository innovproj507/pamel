<?php
/** Listado de usuarios del panel. */
$users        = $users ?? [];
$search       = $search       ?? '';
$roleFilter   = $roleFilter   ?? '';
$statusFilter = $statusFilter ?? '';
$totalUsers   = (int)($totalUsers  ?? 0);
$currentPage  = (int)($currentPage ?? 1);
$totalPages   = (int)($totalPages  ?? 1);
$perPage      = (int)($perPage     ?? 20);

$hayFiltros = $search !== '' || $roleFilter !== '' || $statusFilter !== '';

// Roles reales del sistema, con su nombre en español y su tinte.
$roles = [
    'admin'             => ['Administrador',     'p-violeta'],
    'academic_director' => ['Director Académico','p-indigo'],
    'teacher'           => ['Instructor',        'p-azul'],
    'student'           => ['Estudiante',        'p-verde'],
    'editor'            => ['Editor',            'p-ambar'],
    'customer'          => ['Cliente',           'p-gris'],
];

// Tinte del redondel: estable por persona, para reconocerla entre páginas.
$tintes = ['', 'av-verde', 'av-ambar', 'av-rosa', 'av-violeta', 'av-cian'];
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Usuarios</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($totalUsers) ?> usuario<?= $totalUsers === 1 ? '' : 's' ?> encontrado<?= $totalUsers === 1 ? '' : 's' ?>
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
        <a href="/manager/users/create" class="btn-primario">
            <i data-lucide="user-plus" class="ic ic-16"></i> Crear Usuario
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="card card-pad mb-4 flex items-center gap-3" style="border-color:#bbf7d0; background:#f0fdf4">
    <i data-lucide="circle-check" class="ic ic-17 text-emerald-600 flex-shrink-0"></i>
    <p class="text-[12.5px] font-bold text-emerald-800"><?= htmlspecialchars($_GET['success']) ?></p>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="card card-pad mb-4 flex items-center gap-3" style="border-color:#fecaca; background:#fef2f2">
    <i data-lucide="circle-alert" class="ic ic-17 text-rose-600 flex-shrink-0"></i>
    <p class="text-[12.5px] font-bold text-rose-800"><?= htmlspecialchars($_GET['error']) ?></p>
</div>
<?php endif; ?>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/users"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot" for="f-q">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="f-q" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                       placeholder="Buscar por nombre o correo…"
                       class="campo" style="padding-left:32px; width:250px; border-radius:999px">
            </div>
        </div>

        <div>
            <label class="filtro-rot" for="f-rol">Rol</label>
            <select id="f-rol" name="role" class="campo" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                <?php foreach ($roles as $clave => [$nombre, $_]): ?>
                <option value="<?= $clave ?>" <?= $roleFilter === $clave ? 'selected' : '' ?>><?= $nombre ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-estado">Estado</label>
            <select id="f-estado" name="status" class="campo" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="active"   <?= $statusFilter === 'active'   ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Bloqueado</option>
            </select>
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Buscar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/users" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>

        <input type="hidden" name="per_page" value="<?= $perPage ?>">
    </form>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="vacio">
                        <i data-lucide="search-x" class="ic"></i>
                        <p>No se encontraron usuarios.</p>
                        <?php if ($hayFiltros): ?>
                        <a href="/manager/users" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Limpiar filtros
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $user):
                    $rol    = $user['role']   ?? 'customer';
                    $estado = $user['status'] ?? 'active';
                    $activo = $estado === 'active';
                    [$rolNombre, $rolClase] = $roles[$rol] ?? [ucfirst($rol), 'p-gris'];
                    $nombre  = trim($user['name'] ?? '') ?: $user['email'];
                    $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
                    $tinte   = $tintes[(int)$user['id'] % count($tintes)];
                ?>
                <tr>

                    <!-- Usuario: inicial, nombre y correo -->
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar-ini <?= $tinte ?>"><?= htmlspecialchars($inicial) ?></div>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:280px">
                                    <?= htmlspecialchars($user['name'] ?? 'Sin nombre') ?>
                                </div>
                                <div class="text-[11px] font-semibold text-slate-400 truncate" style="max-width:280px">
                                    <?= htmlspecialchars($user['email']) ?>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Rol -->
                    <td><span class="pastilla <?= $rolClase ?>"><?= htmlspecialchars($rolNombre) ?></span></td>

                    <!-- Estado -->
                    <td>
                        <span class="pastilla <?= $activo ? 'p-verde' : 'p-rojo' ?>">
                            <span class="punto"></span><?= $activo ? 'Activo' : 'Bloqueado' ?>
                        </span>
                    </td>

                    <!-- Acciones -->
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/users/<?= $user['id'] ?>/toggle-status"
                               class="acc <?= $activo ? 'acc-ambar' : 'acc-verde' ?>"
                               title="<?= $activo ? 'Bloquear la cuenta' : 'Reactivar la cuenta' ?>">
                                <i data-lucide="<?= $activo ? 'user-x' : 'user-check' ?>" class="ic" style="width:12px;height:12px"></i>
                                <?= $activo ? 'Bloquear' : 'Activar' ?>
                            </a>
                            <a href="/manager/users/<?= $user['id'] ?>/edit" class="acc acc-gris" title="Editar usuario">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                            <form action="/manager/users/<?= $user['id'] ?>/delete" method="POST"
                                  onsubmit="return confirm('¿Eliminar al usuario «<?= htmlspecialchars(addslashes($nombre), ENT_QUOTES) ?>»? Esta acción no se puede deshacer.')">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo" title="Eliminar usuario">
                                    <i data-lucide="trash-2" class="ic" style="width:12px;height:12px"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    $ruta     = '/manager/users';
    $total    = $totalUsers;
    $page     = $currentPage;
    $lastPage = $totalPages;
    $filtros  = ['search' => $search, 'role' => $roleFilter, 'status' => $statusFilter];
    include \Core\Config::get('paths.root') . '/admin/views/partials/paginador.php';
    ?>
</div>
