-- SQL para actualizar tabla orders con campos de pago
-- Ejecutar en la base de datos cms_db

-- Actualizar tabla orders para agregar campos de pago
ALTER TABLE orders 
ADD COLUMN payment_id VARCHAR(100) NULL AFTER status,
ADD COLUMN payment_method VARCHAR(50) NULL AFTER payment_id,
ADD COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending' AFTER payment_method;

-- Verificar que se agregaron correctamente
DESCRIBE orders;
