-- Migrate to Version 0
CREATE TABLE users
(
    user_id    INT PRIMARY KEY             NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50)                 NOT NULL,
    last_name  VARCHAR(50)                 NOT NULL DEFAULT '',
    email      VARCHAR(100) UNIQUE,
    password   VARCHAR(100)                NOT NULL,
    status     ENUM ('active', 'inactive') NOT NULL DEFAULT 'inactive',
    role       ENUM ('admin', 'guest')     NOT NULL DEFAULT 'guest',
    points     INT                         NOT NULL DEFAULT 0,
    book_count INT                         NOT NULL DEFAULT 0,
    created_at DATETIME                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE verifications
(
    verification_id INT PRIMARY KEY                        NOT NULL AUTO_INCREMENT,
    user_id         INT                                    NOT NULL,
    code            VARCHAR(50) UNIQUE                     NOT NULL,
    type            ENUM ('activation', 'forgot_password') NOT NULL,
    created_at      DATETIME                               NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expired_at      DATETIME                               NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 1 HOUR),
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);
