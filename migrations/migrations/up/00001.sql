-- Migrate to Version 0
CREATE TABLE users
(
    user_id    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50)     NOT NULL,
    last_name  VARCHAR(50),
    email      VARCHAR(100) UNIQUE,
    password   VARCHAR(100),
    role       ENUM ('admin', 'guest'),
    points     INT,
    book_count INT,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE verifications
(
    verification_id INT PRIMARY KEY                        NOT NULL AUTO_INCREMENT,
    user_id         INT                                    NOT NULL,
    code            VARCHAR(50) UNIQUE                     NOT NULL,
    type            ENUM ('activation', 'forgot_password') NOT NULL,
    created_at      DATETIME,
    activation_at   DATETIME,
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);
