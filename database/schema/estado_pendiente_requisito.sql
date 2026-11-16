-- ─────────────────────────────────────────────────────────────────────
-- Estado "pendiente de requisito" en las solicitudes de admisión
--
-- El F-46 debe mostrar el estado de la solicitud con cuatro valores:
-- aprobada, no aprobada, pendiente de requisito y en revisión. La
-- columna tenía pending / reviewed / approved / rejected; faltaba el
-- tercero. Los valores existentes no cambian de significado:
--   pending   = Pendiente (recién recibida)
--   reviewed  = En revisión
--   pending_requirement = Pendiente de requisito (falta algún documento)
--   approved  = Aprobada
--   rejected  = No aprobada
--
-- Es repetible: volver a ejecutarla deja la columna igual.
-- ─────────────────────────────────────────────────────────────────────

ALTER TABLE admission_requests
    MODIFY COLUMN status
    ENUM('pending','reviewed','pending_requirement','approved','rejected')
    DEFAULT 'pending';
