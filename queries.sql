INSERT INTO categories (title, symbol_code) VALUES
('Доски и лыжи', 'boards'),
('Крепления', 'attachment'),
('Ботинки', 'boots'),
('Одежда', 'clothing'),
('Инструменты', 'tools'),
('Разное', 'other');

INSERT INTO users (name, email, password, contact_info) VALUES
('Иван Петров', 'ivan.petrov@example.com', '5f4dcc3b5', 'Телефон: +7-911-123-45-67'),
('Мария Сидорова', 'maria.sidorova@example.com', '5f4dcc3b5', 'Телефон: +7-912-345-67-89'),
('Алексей Козлов', 'alexey.kozlov@example.com', '5f4d4dcc3', 'Телефон: +7-913-456-78-90'),
('Екатерина Новикова', 'ekaterina.novikova@example.com', '5f4dcc', 'Телефон: +7-914-567-89-01');

INSERT INTO lots (title, description, image, start_price, bidding_step, author_id, category_id, end_date) VALUES
('2014 Rossignol District Snowboard', 'Отличный сноуборд для фристайла', 'img/lot-1.jpg', 10999, 500, 1, 1, '2025-11-10'),
('DC Ply Mens 2016/2017 Snowboard', 'Профессиональный сноуборд DC', 'img/lot-2.jpg', 15999, 1000, 2, 1, '2025-10-01'),
('Крепления Union Contact Pro 2015 года размер L/XL', 'Надежные крепления для сноуборда', 'img/lot-3.jpg', 8000, 300, 3, 2, '2025-10-02'),
('Ботинки для сноуборда DC Mutiny Charocal', 'Утепленные ботинки для зимнего катания', 'img/lot-4.jpg', 10999, 500, 4, 3, '2025-10-03'),
('Куртка для сноуборда DC Mutiny Charocal', 'Ветро- и водонепроницаемая куртка', 'img/lot-5.jpg', 7500, 250, 1, 4, '2025-10-04'),
('Маска Oakley Canopy', 'Защитная маска с антифог покрытием', 'img/lot-6.jpg', 5400, 200, 2, 5, '2025-10-05');

INSERT INTO bids (amount, user_id, lot_id) VALUES
(12000.00, 1, 7),
(12500.00, 2, 8),
(13000.00, 3, 9),
(13500.00, 4, 10);

-- вывод всех категорий
SELECT title FROM categories;

-- получить самые новые, открытые лоты.
--  Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
 SELECT lots.title AS lot_title, lots.start_price, lots.image, lots.category_id, lots.end_date, categories.title AS category_title
 FROM lots
 JOIN categories ON lots.category_id = categories.id
 WHERE lots.end_date > NOW()
 ORDER BY lots.created_at DESC LIMIT 6;

-- показать лот по его ID. Получите также название категории, к которой принадлежит лот;
SELECT
    l.id,
    l.title As lots_title,
    l.description,
    l.image,
    l.start_price,
    l.bidding_step,
    l.created_at,
    l.end_date,
    c.title AS category_title
FROM lots l
JOIN categories c ON l.category_id = c.id
WHERE l.id = 12;

-- обновить название лота по его идентификатору;
UPDATE lots SET title = '2015 Rossignol District Snowboard' WHERE id = '7';
