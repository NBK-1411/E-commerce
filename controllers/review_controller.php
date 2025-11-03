<?php
require_once __DIR__ . '/../classes/review_class.php';

class ReviewController {
    private $review;

    public function __construct() {
        $this->review = new Review();
    }

    public function create($perfume_id, $customer_id, $rating, $comment) {
        if (empty($perfume_id) || empty($customer_id) || empty($rating)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Rating must be between 1 and 5'];
        }

        return $this->review->create($perfume_id, $customer_id, $rating, $comment);
    }

    public function getByPerfume($perfume_id, $approved_only = true) {
        return $this->review->getByPerfume($perfume_id, $approved_only);
    }

    public function getAll($status = null) {
        return $this->review->getAll($status);
    }

    public function approve($id) {
        return $this->review->approve($id);
    }

    public function reject($id) {
        return $this->review->reject($id);
    }

    public function delete($id) {
        return $this->review->delete($id);
    }
}
?>
