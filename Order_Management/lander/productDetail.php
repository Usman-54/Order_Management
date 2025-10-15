<?php
include "../backend/db.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { echo '<div class="alert alert-danger">Invalid product id.</div>'; exit; }

$stmt = $conn->prepare("SELECT id, title, description, image, regular_price FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) { echo '<div class="alert alert-danger">Product not found.</div>'; exit; }
$product = $result->fetch_assoc();

$itemImage = str_replace('\\', '/', $product['image'] ?? '');
$serverPath = __DIR__ . "/../backend/" . $itemImage;
$webPath = "../backend/" . $itemImage;
if (empty($itemImage) || !file_exists($serverPath)) { $webPath = "../backend/uploads/default.jpg"; }
?>

<div id="productOverlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;justify-content:center;align-items:center;">
  <div id="productDetailCard" style="background:#fff;width:100%;max-width:500px;border-radius:10px;overflow:hidden;max-height:90vh;display:flex;flex-direction:column;">
    
    <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem;border-bottom:1px solid #ddd;">
      <h4 style="margin:0;"><?php echo htmlspecialchars($product['title']); ?></h4>
      <button id="closeDetail" style="border:none;background:none;font-size:1.5rem;cursor:pointer;">&times;</button>
    </div>

    <div style="padding:15px;overflow-y:auto;flex:1;">
      <img src="<?php echo htmlspecialchars($webPath); ?>" style="width:100%;max-height:250px;object-fit:cover;border-radius:10px;margin-bottom:15px;">
      <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
      <h6 class="text-success mb-3">
        Base Price: $<span class="basePrice" data-base="<?php echo $product['regular_price']; ?>"><?php echo number_format($product['regular_price'],2); ?></span>
      </h6>

      <form class="addToCartForm" data-product-id="<?php echo $product['id']; ?>">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="title" value="<?php echo htmlspecialchars($product['title'], ENT_QUOTES); ?>">
        <input type="hidden" name="price" class="hiddenPrice" value="<?php echo $product['regular_price']; ?>">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($webPath, ENT_QUOTES); ?>">
        <input type="hidden" name="extras" class="extrasInput">
        <input type="hidden" name="drink_name" class="drinkInput">
        <input type="hidden" name="sauce_name" class="sauceInput">

        <div class="mb-3">
          <label>Quantity:</label>
          <input type="number" name="quantity" class="quantity" value="1" min="1" style="width:70px;padding:5px;">
        </div>

        <div class="mb-3">
          <label>Select Size:</label>
          <select name="size" class="size" style="width:100%;padding:5px;">
            <option value="medium" data-price="0">Medium</option>
            <option value="large" data-price="30">Large (+$30)</option>
            <option value="family" data-price="130">Family (+$130)</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Extras:</label><br>
          <input type="checkbox" class="extra-option" value="cheese" data-price="0"> Extra Cheese<br>
          <input type="checkbox" class="extra-option" value="meat" data-price="5"> Extra Meat (+$5)
        </div>

        <div class="mb-3">
          <label>Drink:</label>
          <select name="drink" class="drink" style="width:100%;padding:5px;">
            <option value="coke_small" data-price="0">Coca-Cola 0.5l</option>
            <option value="coke_large" data-price="15">Coca-Cola 1.5l (+$15)</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Sauce:</label>
          <select name="sauce" class="sauce" style="width:100%;padding:5px;">
            <option value="garlic" data-price="0">Garlic Sauce</option>
            <option value="bbq" data-price="0">Barbecue Sauce</option>
          </select>
        </div>

        <div class="mb-3">
          <strong>Total Price: $<span class="totalPrice"><?php echo number_format($product['regular_price'],2); ?></span></strong>
        </div>

        <button type="submit" style="width:100%;padding:10px;background:red;color:#fff;border:none;cursor:pointer;">Add to Cart</button>
      </form>
    </div>
  </div>
</div>
