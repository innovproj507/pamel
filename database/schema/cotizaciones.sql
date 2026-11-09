-- ─────────────────────────────────────────────────────────────────────
-- Tablas de las solicitudes de cotización
--
-- Core\Models\QuoteRequest las usa desde el carrito (OrderController) y
-- desde el panel (/manager/quote-requests), pero ningún script las creaba:
-- en una base nueva el listado salía vacío y el error "Table
-- 'quote_requests' doesn't exist" quedaba solo en php_errors.log.
--
-- Es repetible: si las tablas ya están, no hace nada.
-- ─────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS quote_requests (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    token         VARCHAR(64)  NOT NULL,
    user_id       INT UNSIGNED NULL,
    customer_name VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL,
    phone         VARCHAR(50)  NOT NULL,
    message       TEXT         NULL,
    status        ENUM('pending','quoted','completed','cancelled') NOT NULL DEFAULT 'pending',
    notes         TEXT         NULL,
    quoted_at     DATETIME     NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_quote_token (token),
    KEY idx_quote_status_created (status, created_at),
    KEY idx_quote_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quote_request_items (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    quote_request_id INT UNSIGNED NOT NULL,
    product_id       INT          NULL,
    product_name     VARCHAR(255) NOT NULL,
    quantity         INT          NOT NULL DEFAULT 1,
    price_at_request DECIMAL(10,2) NULL,
    PRIMARY KEY (id),
    KEY idx_item_quote (quote_request_id),
    CONSTRAINT fk_quote_item_request FOREIGN KEY (quote_request_id)
        REFERENCES quote_requests (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
