-- Migrate to Version 4
CREATE TABLE books
(
    book_id      INT PRIMARY KEY     NOT NULL AUTO_INCREMENT,
    isbn         VARCHAR(50) UNIQUE  NOT NULL,
    sku          VARCHAR(50) UNIQUE  NOT NULL,
    author       JSON                NOT NULL,
    title        VARCHAR(120)        NOT NULL,
    slug         VARCHAR(120) UNIQUE NOT NULL,
    image        VARCHAR(255)        NOT NULL,
    pages        SMALLINT            NOT NULL DEFAULT 0,
    weight       FLOAT               NOT NULL DEFAULT 0,
    height       SMALLINT            NOT NULL DEFAULT 0,
    width        SMALLINT            NOT NULL DEFAULT 0,
    language     VARCHAR(20)         NOT NULL,
    description  TEXT                NOT NULL,
    stock        SMALLINT            NOT NULL DEFAULT 0,
    rating       INT                 NOT NULL DEFAULT 0,
    rating_count INT                 NOT NULL DEFAULT 0,
    borrow       SMALLINT            NOT NULL DEFAULT 0,
    published_at DATETIME,
    created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by   INT                 NOT NULL,
    updated_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by   INT                 NOT NULL,
    deleted_at   DATETIME,
    deleted_by   INT
);

CREATE TABLE book_categories
(
    book_category_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    book_id          INT             NOT NULL,
    category_id      INT             NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books (book_id),
    FOREIGN KEY (category_id) REFERENCES categories (category_id),
    CONSTRAINT book_category_uniq UNIQUE (book_id, category_id),
    INDEX book_id (book_id)
);
