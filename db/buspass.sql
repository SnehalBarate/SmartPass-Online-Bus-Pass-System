CREATE DATABASE buspass;
USE buspass;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255)
);

CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(50)
);

INSERT INTO admin (username,password) VALUES ('admin','admin');

CREATE TABLE bus_pass (
    pass_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    route VARCHAR(100),
    pass_type VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Pending',
    apply_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
