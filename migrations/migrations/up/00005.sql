-- Migrate to Version 5
CREATE TRIGGER bu_book_borrow_stock
    BEFORE UPDATE
    ON books
    FOR EACH ROW
BEGIN
    IF NEW.stock < OLD.borrow THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock cannot be less than available stock';
    ELSEIF NEW.borrow > OLD.stock THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book is out of stock';
    END IF;
END;
