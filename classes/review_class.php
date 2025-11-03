<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

class Review {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->db->connect();
    }

    public function create($perfume_id, $customer_id, $rating, $comment) {
        $query = "INSERT INTO reviews (perfume_id, customer_id, rating, comment, status, created_at) 
                  VALUES (?, ?, ?, ?, 'pending', NOW())";
        return $this->db->write($query, 'iiis', [$perfume_id, $customer_id, $rating, $comment]);
    }

    public function getByPerfume($perfume_id, $approved_only = true) {
        $query = "SELECT r.id, r.rating, r.comment, r.created_at, c.name as customer_name 
                  FROM reviews r 
                  JOIN customers c ON r.customer_id = c.id 
                  WHERE r.perfume_id = ?";
        
        if ($approved_only) {
            $query .= " AND r.status = 'approved'";
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        return $this->db->read($query, 'i', [$perfume_id]);
    }

    public function getAll($status = null) {
        $query = "SELECT r.id, r.perfume_id, p.name as perfume_name, r.customer_id, c.name as customer_name, r.rating, r.comment, r.status, r.created_at 
                  FROM reviews r 
                  JOIN perfumes p ON r.perfume_id = p.id 
                  JOIN customers c ON r.customer_id = c.id";
        
        if ($status) {
            $query .= " WHERE r.status = ?";
            return $this->db->read($query, 's', [$status]);
        }
        
        $query .= " ORDER BY r.created_at DESC";
        return $this->db->read($query);
    }

    public function approve($id) {
        $query = "UPDATE reviews SET status = 'approved' WHERE id = ?";
        return $this->db->write($query, 'i', [$id]);
    }

    public function reject($id) {
        $query = "UPDATE reviews SET status = 'rejected' WHERE id = ?";
        return $this->db->write($query, 'i', [$id]);
    }

    public function delete($id) {
        $query = "DELETE FROM reviews WHERE id = ?";
        return $this->db->write($query, 'i', [$id]);
    }
}
?>
