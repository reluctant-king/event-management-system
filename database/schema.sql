-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS event_management_system;

-- Use that database
USE event_management_system;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  role ENUM('user', 'organizer'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  organizer_id INT,
  title VARCHAR(255),
  description TEXT,
  event_date DATE,
  event_time TIME,
  location VARCHAR(255),
  category VARCHAR(100),
  is_private BOOLEAN DEFAULT 0,
  ticket_type VARCHAR(100),
  ticket_price DECIMAL(10,2),
  image_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  user_id INT,
  ticket_type VARCHAR(100),
  price DECIMAL(10,2),
  purchase_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  qr_path VARCHAR(255),
  ticket_id VARCHAR(100),
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE discussions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  user_id INT,
  message TEXT,
  posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE polls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  question TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id)
);

CREATE TABLE poll_options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  poll_id INT,
  option_text VARCHAR(255),
  FOREIGN KEY (poll_id) REFERENCES polls(id)
);

CREATE TABLE poll_votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  poll_id INT,
  option_id INT,
  user_id INT,
  FOREIGN KEY (poll_id) REFERENCES polls(id),
  FOREIGN KEY (option_id) REFERENCES poll_options(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE qna (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  user_id INT,
  question TEXT,
  answered BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  answer TEXT,
  FOREIGN KEY (event_id) REFERENCES events(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);



INSERT INTO users (name, email, password, role) VALUES
('John Doe', 'john@example.com', 'password123', 'user'),
('Event Corp', 'organizer1@events.com', 'password123', 'organizer');