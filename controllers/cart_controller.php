<?php
require_once __DIR__ . '/../classes/cart_class.php';
require_once __DIR__ . '/../classes/perfume_class.php';

class CartController {
    private $cart;
    private $perfume;

    public function __construct($customer_id = null) {
        $this->cart = new Cart($customer_id);
        $this->perfume = new Perfume();
    }

    public function addItem($perfume_id, $quantity = 1) {
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Quantity must be greater than 0'];
        }

        $perfume = $this->perfume->getById($perfume_id);
        if (!$perfume) {
            return ['success' => false, 'message' => 'Perfume not found'];
        }

        if ($perfume['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }

        return $this->cart->addItem($perfume_id, $quantity);
    }

    public function getItems() {
        return $this->cart->getItems();
    }

    public function removeItem($cart_item_id) {
        return $this->cart->removeItem($cart_item_id);
    }

    public function updateQuantity($cart_item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cart_item_id);
        }

        return $this->cart->updateQuantity($cart_item_id, $quantity);
    }

    public function clearCart() {
        return $this->cart->clearCart();
    }

    public function getTotal() {
        return $this->cart->getTotal();
    }
}
?>
