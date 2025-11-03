-- Newsletter subscribers table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    status VARCHAR(50) DEFAULT 'active' COMMENT 'active, unsubscribed'
);

CREATE INDEX idx_newsletter_email ON newsletter_subscribers(email);
