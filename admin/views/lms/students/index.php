<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800">Estudiantes</h1>
            <p class="text-slate-500 font-medium"><?= $stats['total'] ?> usuarios registrados</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/manager/lms/students/create" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-lg shadow-emerald-600/20">
                <i class="fas fa-user-plus"></i> Inscribir en Curso
            </a>
            <a href="/manager/users/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-lg shadow-blue-600/20">
                <i class="fas fa-plus"></i> Crear Usuario
            </a>
            <div class="flex bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
                <a href="/manager/lms/students" class="px-4 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-sm shadow-sm">
                    Estudiantes
                </a>
                <a href="/manager/users?role=teacher" class="px-4 py-1.5 rounded-lg text-slate-500 font-bold text-sm hover:bg-slate-50">
                    Profesores
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-4xl font-black text-slate-800"><?= $stats['total'] ?></p>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Estudiantes</p>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-4xl font-black text-slate-800"><?= $stats['active'] ?></p>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Activos</p>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-4xl font-black text-slate-800"><?= $stats['blocked'] ?></p>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Bloqueados</p>
        </div>
        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xl">
            <i class="fas fa-user-slash"></i>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-6">
    <div class="p-4 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
            <input type="text" placeholder="Buscar por nombre o email..." class="w-full pl-11 pr-4 py-2.5 rounded-2xl border-none bg-slate-50 text-sm font-medium focus:ring-2 focus:ring-blue-500/20 placeholder:text-slate-300">
        </div>
        <div class="flex items-center gap-2">
            <select class="rounded-xl border-slate-200 bg-white text-sm font-bold text-slate-600 py-2 pl-4 pr-10 focus:ring-2 focus:ring-blue-500/20">
                <option>Todos</option>
                <option>Activos</option>
                <option>Inactivos</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Usuario</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Cursos</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Registro</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($students as $s): 
                    $initials = strtoupper(substr($s['name'], 0, 1) . substr(explode(' ', $s['name'])[1] ?? '', 0, 1));
                    $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-rose-500', 'bg-amber-500', 'bg-indigo-500', 'bg-cyan-500', 'bg-violet-500'];
                    $bgColor = $colors[ord($s['name']) % count($colors)];
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-2xl <?= $bgColor ?> text-white flex items-center justify-center font-black text-xs shadow-lg shadow-<?= str_replace('bg-', '', $bgColor) ?>/20">
                                <?= $initials ?>
                            </div>
                            <span class="font-bold text-slate-700"><?= htmlspecialchars($s['name']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-slate-400"><?= htmlspecialchars($s['email']) ?></span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-50 text-blue-600 font-black text-xs">
                            <?= $s['course_count'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if ($s['status'] === 'active'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Bloqueado
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs font-bold text-slate-400"><?= date('d/m/Y', strtotime($s['created_at'])) ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/manager/lms/students/create?student_id=<?= $s['id'] ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors text-[10px] font-black uppercase">
                                <i class="fas fa-graduation-cap"></i> Inscribir
                            </a>
                            <a href="/manager/users/<?= $s['id'] ?>/edit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors text-[10px] font-black uppercase">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
