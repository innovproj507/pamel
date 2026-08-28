<?php

namespace Core {

    /**
     * Mapa central de capacidades por rol.
     *
     * Toda decisión de "quién puede hacer qué" vive aquí. Los controladores
     * preguntan por una capacidad concreta (`lms.quizzes.manage`), nunca por un
     * rol, de modo que añadir o ajustar un rol es un cambio en este archivo.
     */
    class Permissions
    {
        public const ADMIN             = 'admin';
        public const ACADEMIC_DIRECTOR = 'academic_director';
        public const TEACHER           = 'teacher';
        public const STUDENT           = 'student';
        public const EDITOR            = 'editor';

        /** Roles que pueden entrar al panel /manager. */
        public const STAFF_ROLES = [self::ADMIN, self::ACADEMIC_DIRECTOR, self::TEACHER];

        /**
         * Capacidades por rol. '*' concede todo.
         */
        private static $map = [

            self::ADMIN => ['*'],

            // Director Académico: todo lo académico y de servicios al estudiante.
            // Sin configuración del sistema, sin comercio, sin gestionar admins.
            self::ACADEMIC_DIRECTOR => [
                'panel.access',
                'lms.courses.view',   'lms.courses.manage',
                'lms.lessons.view',   'lms.lessons.manage',
                'lms.quizzes.view',   'lms.quizzes.manage',
                'lms.categories.manage',
                'lms.forums.view',    'lms.forums.manage',
                'lms.students.view',  'lms.students.manage',
                'lms.teachers.view',  'lms.teachers.assign',
                'admissions.view',    'admissions.manage',
                'surveys.view',       'surveys.manage',
                'contact.view',
                'users.view',         'users.manage',
            ],

            // Instructor: seguimiento de sus estudiantes y foros.
            // El contenido (manuales, quizzes, exámenes) es de solo lectura.
            self::TEACHER => [
                'panel.access',
                'lms.courses.view',
                'lms.lessons.view',
                'lms.quizzes.view',
                'lms.students.view',
                'lms.forums.view',    'lms.forums.manage',
            ],

            self::EDITOR  => ['panel.access'],
            self::STUDENT => [],
        ];

        /** Etiquetas legibles para la interfaz. */
        private static $labels = [
            self::ADMIN             => 'Administrador',
            self::ACADEMIC_DIRECTOR => 'Director Académico',
            self::TEACHER           => 'Instructor',
            self::EDITOR            => 'Editor',
            self::STUDENT           => 'Estudiante',
        ];

        /**
         * Fuerza la carga de la clase durante el arranque, de modo que el
         * helper global can() esté siempre disponible en las vistas.
         */
        public static function boot(): void
        {
        }

        /**
         * ¿El rol tiene la capacidad indicada?
         */
        public static function roleCan($role, string $capability): bool
        {
            if (!$role || !isset(self::$map[$role])) {
                return false;
            }
            $caps = self::$map[$role];
            return in_array('*', $caps, true) || in_array($capability, $caps, true);
        }

        /**
         * Todas las capacidades de un rol (['*'] para admin).
         */
        public static function capabilities($role): array
        {
            return self::$map[$role] ?? [];
        }

        public static function label($role): string
        {
            return self::$labels[$role] ?? (string) $role;
        }

        public static function allRoles(): array
        {
            return array_keys(self::$labels);
        }

        /**
         * Roles que un usuario puede asignar a otros. Solo un admin puede crear
         * o degradar cuentas de administrador y de dirección académica.
         */
        public static function assignableRoles($actorRole): array
        {
            if ($actorRole === self::ADMIN) {
                return self::allRoles();
            }
            if ($actorRole === self::ACADEMIC_DIRECTOR) {
                return [self::TEACHER, self::STUDENT];
            }
            return [];
        }

        /**
         * ¿Puede $actorRole administrar una cuenta cuyo rol es $targetRole?
         */
        public static function canManageUserWithRole($actorRole, $targetRole): bool
        {
            if ($actorRole === self::ADMIN) {
                return true;
            }
            if ($actorRole === self::ACADEMIC_DIRECTOR) {
                return !in_array($targetRole, [self::ADMIN, self::ACADEMIC_DIRECTOR], true);
            }
            return false;
        }
    }
}

namespace {
    if (!function_exists('can')) {
        /**
         * ¿El usuario en sesión tiene la capacidad indicada?
         * Disponible en cualquier vista para condicionar botones y enlaces.
         */
        function can($capability)
        {
            return \Core\Auth::getInstance()->can($capability);
        }
    }
}
