-- Online Quiz System: Sample Data
-- Run this after schema.sql to populate categories, quizzes, and questions.
-- No admin account is seeded. Use cli/create-admin.php to create one.

USE quiz_system;

-- Categories
INSERT INTO categories (category_name) VALUES
('Web Development'),
('Database'),
('Programming');

-- Quizzes
INSERT INTO quizzes (title, category_id, description, status) VALUES
('HTML Fundamentals', 1, 'Test your knowledge of core HTML elements, attributes, and document structure.', 'active'),
('SQL Fundamentals', 2, 'Basic SQL queries, clauses, and functions for relational databases.', 'active'),
('C Programming Basics', 3, 'Entry-level questions on C syntax, data types, control flow, and standard library functions.', 'active');

-- Questions for HTML Fundamentals (quiz_id = 1)
INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES
(1, 'Which HTML element is used to define the title of a document, shown in the browser tab?', '<title>', '<head>', '<meta>', '<header>', 'A'),
(1, 'What does the href attribute of the <a> tag specify?', 'The font size of the link', 'The URL the link points to', 'The color of the link', 'The tooltip text for the link', 'B'),
(1, 'Which HTML element is used to create an unordered (bulleted) list?', '<ol>', '<list>', '<ul>', '<dl>', 'C'),
(1, 'What is the correct HTML element for inserting a line break?', '<lb>', '<break>', '<newline>', '<br>', 'D'),
(1, 'Which attribute is used to provide alternative text for an image when it cannot be displayed?', 'title', 'src', 'alt', 'longdesc', 'C');

-- Questions for SQL Fundamentals (quiz_id = 2)
INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES
(2, 'Which SQL statement is used to retrieve data from a database table?', 'GET', 'SELECT', 'EXTRACT', 'PULL', 'B'),
(2, 'Which SQL clause is used to filter rows based on a specified condition?', 'HAVING', 'FILTER', 'WHERE', 'CONDITION', 'C'),
(2, 'Which SQL keyword is used to sort the result set of a query?', 'SORT', 'ORDER BY', 'ARRANGE', 'GROUP BY', 'B'),
(2, 'What does the SQL COUNT() aggregate function return?', 'The sum of all values', 'The average of all values', 'The number of rows that match the query', 'The maximum value in the column', 'C'),
(2, 'Which SQL statement is used to add new rows to a table?', 'ADD', 'INSERT INTO', 'UPDATE', 'APPEND', 'B');

-- Questions for C Programming Basics (quiz_id = 3)
INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES
(3, 'Which function is the entry point of every C program?', 'start()', 'begin()', 'main()', 'init()', 'C'),
(3, 'Which format specifier is used to print an integer with printf() in C?', '%f', '%c', '%s', '%d', 'D'),
(3, 'What is the size of the int data type in a typical 32-bit C compiler?', '2 bytes', '4 bytes', '8 bytes', '1 byte', 'B'),
(3, 'Which header file must be included to use the printf() and scanf() functions in C?', 'stdlib.h', 'string.h', 'stdio.h', 'conio.h', 'C'),
(3, 'What does the break statement do when used inside a loop in C?', 'Skips the current iteration and continues with the next', 'Terminates the loop immediately and resumes after it', 'Restarts the loop from the beginning', 'Pauses the loop until a key is pressed', 'B');
