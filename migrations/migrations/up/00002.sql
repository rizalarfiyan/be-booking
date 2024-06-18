-- Migrate to Version 2
CREATE TABLE contacts
(
    contact_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50)     NOT NULL,
    last_name  VARCHAR(50)     NOT NULL DEFAULT '',
    email      VARCHAR(100)    NOT NULL,
    phone      VARCHAR(20)     NOT NULL,
    message    VARCHAR(1000)   NOT NULL,
    is_read    BOOLEAN         NOT NULL DEFAULT FALSE,
    created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
