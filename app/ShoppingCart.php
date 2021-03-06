<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model {
  protected $table = "shopping_carts";

  protected $fillable = [
    'status'
  ];

  public function order() {
    return $this->hasOne('App\Order');
  }

  public function approve() {
    $this->updateCustomIDAndStatus();
  }

  public function generateCustomID() {
    return md5($this->id.$this->updated_at);
  }

  public function updateCustomIDAndStatus() {
    $this->status = "approved";
    $this->customid = $this->generateCustomID();
    $this->save();
  }

  public function inShoppingCarts() {
    return $this->hasMany('App\InShoppingCart');
  }

  public function products() {
    return $this->belongsToMany('App\Product', 'in_shopping_carts');
  }

  public function productsSize() {
    return $this->products()->count();
  }

  public function total() {
    return $this->products()->sum('pricing');
  }

  public static function findOrCreateBySessionID($shopping_cart_id) {
    if (!empty($shopping_cart_id)) {
      return ShoppingCart::findBySessionID($shopping_cart_id);
    } else {
      return ShoppingCart::createWithoutSession($shopping_cart_id);
    }
  }

  public static function findBySessionID($shopping_cart_id) {
    return ShoppingCart::find($shopping_cart_id);
  }

  public static function createWithoutSession() {
    return ShoppingCart::create(['status' => 'incompleted']);
  }
}