-- Migrate to Version 5
CREATE TRIGGER bu_book_borrow_stock
    BEFORE UPDATE
    ON books
    FOR EACH ROW
BEGIN
    IF NEW.stock < OLD.borrowed THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock cannot be less than available stock';
    ELSEIF NEW.borrowed > OLD.stock THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book is out of stock';
    END IF;
END;

CREATE FUNCTION getBookRating(rating INT, ratingCount INT)
    RETURNS DECIMAL(3, 2)
BEGIN
    DECLARE averageRating DECIMAL(3, 2);
    IF ratingCount = 0 THEN
        SET averageRating = 0;
    ELSE
        SET averageRating = rating / ratingCount;
    END IF;
    RETURN LEAST(averageRating, 5.00);
END;
