CREATE DATABASE IF NOT EXISTS studentdb;
USE studentdb;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    email VARCHAR(100),
    mobile VARCHAR(20),
    gender VARCHAR(20),
    dob DATE,
    course VARCHAR(50),
    address TEXT,
    password VARCHAR(255)
);
