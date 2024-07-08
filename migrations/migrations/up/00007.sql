-- Migrate to Version 7
DROP TRIGGER IF EXISTS bi_histories;
CREATE TRIGGER bi_histories
    BEFORE INSERT
    ON histories
    FOR EACH ROW
BEGIN
    DECLARE borrowedCount INT;
    DECLARE availableStock INT;
    DECLARE isBorrowed INT;

    SELECT COUNT(history_id) INTO borrowedCount FROM histories WHERE user_id = NEW.user_id AND status IN ('pending', 'read');
    IF borrowedCount >= 3 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User cannot borrow more than 3 books';
    END IF;

    SELECT COUNT(history_id) INTO isBorrowed FROM histories WHERE user_id = NEW.user_id AND book_id = NEW.book_id AND status IN ('pending', 'read');
    IF isBorrowed > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User already borrowed this book';
    END IF;

    SELECT stock - borrowed INTO availableStock FROM books WHERE book_id = NEW.book_id;
    IF availableStock < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book is out of stock';
    END IF;
END;

DROP TRIGGER IF EXISTS ai_histories;
CREATE TRIGGER ai_histories
    AFTER INSERT
    ON histories
    FOR EACH ROW
BEGIN
    UPDATE books SET borrowed = borrowed + 1 WHERE book_id = NEW.book_id;
END;

DROP TRIGGER IF EXISTS bu_histories;
CREATE TRIGGER bu_histories
    BEFORE UPDATE
    ON histories
    FOR EACH ROW
BEGIN
    IF OLD.status = 'cancel' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The history status cannot be updated';
    END IF;

    IF NEW.status = 'success' AND OLD.status != 'read' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The history status must be read';
    END IF;
END;

DROP TRIGGER IF EXISTS au_histories;
CREATE TRIGGER au_histories
    AFTER UPDATE
    ON histories
    FOR EACH ROW
BEGIN
    IF NEW.status = 'success' THEN
        UPDATE books SET borrowed = borrowed - 1, borrowed_count = borrowed_count + 1 WHERE book_id = OLD.book_id;
        UPDATE users SET points = points + NEW.point, book_count = book_count + 1 WHERE user_id = OLD.user_id;
    END IF;

    IF NEW.status = 'cancel' THEN
        UPDATE books SET borrowed = borrowed - 1 WHERE book_id = OLD.book_id;
    END IF;
END;

DROP TRIGGER IF EXISTS bi_rating_histories;
CREATE TRIGGER bi_rating_histories
    BEFORE INSERT
    ON rating_histories
    FOR EACH ROW
BEGIN
    DECLARE status VARCHAR(10);
    SELECT status INTO status FROM histories WHERE history_id = NEW.history_id;
    IF status != 'success' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The history status must be success';
    END IF;
END;

DROP TRIGGER IF EXISTS ai_rating_histories;
CREATE TRIGGER ai_rating_histories
    AFTER INSERT
    ON rating_histories
    FOR EACH ROW
BEGIN
    DECLARE bookId INT;
    SELECT book_id INTO bookId FROM histories WHERE history_id = NEW.history_id;
    UPDATE books SET rating = rating + NEW.rating, rating_count = rating_count + 1 WHERE book_id = bookId;
END;

DROP TRIGGER IF EXISTS au_rating_histories;
CREATE TRIGGER au_rating_histories
    AFTER UPDATE
    ON rating_histories
    FOR EACH ROW
BEGIN
    DECLARE bookId INT;
    SELECT book_id INTO bookId FROM histories WHERE history_id = NEW.history_id;
    UPDATE books SET rating = rating - OLD.rating + NEW.rating WHERE book_id = bookId;
END;
