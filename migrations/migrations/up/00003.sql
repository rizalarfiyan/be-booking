-- Migrate to Version 3
CREATE TABLE categories
(
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50)        NOT NULL,
    slug        VARCHAR(50) UNIQUE NOT NULL,
    created_at  DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by  INT                NOT NULL,
    updated_at  DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by  INT                NOT NULL,
    deleted_at  DATETIME,
    deleted_by  INT
);
