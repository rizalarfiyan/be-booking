-- Migrate to Version 6

CREATE TABLE histories
(
    history_id  INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id     INT             NOT NULL,
    book_id     INT             NOT NULL,
    status      ENUM ('read', 'success', 'pending', 'cancel') NOT NULL DEFAULT 'pending',
    point       FLOAT           NOT NULL DEFAULT 0,
    return_at   DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 7 DAY),
    borrow_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    borrow_by   INT             NOT NULL,
    returned_at datetime,
    returned_by INT,
    FOREIGN KEY (book_id) REFERENCES books (book_id),
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);

CREATE TABLE rating_histories
(
    history_id INT           NOT NULL UNIQUE PRIMARY KEY,
    rating     DECIMAL(3, 2) NOT NULL DEFAULT 0,
    review     JSON,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (history_id) REFERENCES histories (history_id)
);

ALTER TABLE books MODIFY COLUMN rating DECIMAL(20, 2) NOT NULL DEFAULT 0;
