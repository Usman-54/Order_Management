<form id="addProductForm" enctype="multipart/form-data">

  <!-- CATEGORY SECTION -->
  <div class="form-group mb-3">
    <label class="form-label" for="category">Product Category</label>
    <div class="input-group">
      <select id="category" name="category" class="form-control" required>
        <option value="">-- Select Category --</option>
        <option value="All">All</option>
        <?php
        $categories = $conn->query("SELECT category_id, name FROM categories ORDER BY name ASC");
        while ($row = $categories->fetch_assoc()) {
          echo '<option value="' . htmlspecialchars($row['category_id']) . '">' . htmlspecialchars($row['name']) . '</option>';
        }
        ?>
      </select>

      <!-- 🔍 Search Button -->
      <button type="button" class="btn btn-outline-primary" id="searchCategoryBtn" title="Search Category">
        <i class="bi bi-search"></i>
      </button>

      <!-- ➕ Add Button -->
      <button type="button" class="btn btn-outline-success" id="addCategoryBtn" title="Add New Category">
        <i class="bi bi-plus-circle"></i>
      </button>
    </div>
  </div>

  <!-- PRODUCT FIELDS (wrap so we can hide/show easily) -->
  <div id="productFields">
    <div class="form-group mb-3">
      <label class="form-label" for="title">Product Title</label>
      <input type="text" id="title" name="title" class="form-control" required>
    </div>

    <div class="form-group mb-3">
      <label class="form-label" for="description">Product Description</label>
      <textarea id="description" name="description" class="form-control"></textarea>
    </div>

    <div class="form-group mb-3">
      <label class="form-label" for="image">Featured Image</label>
      <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
    </div>

    <div class="form-group mb-3">
      <label class="form-label" for="regularPrice">Regular Price ($)</label>
      <input type="number" step="0.01" id="regularPrice" name="regularPrice" class="form-control" required>
    </div>

    <div class="form-group mb-3">
      <label class="form-label" for="specialOffer">Special Offer ($)</label>
      <input type="number" step="0.01" id="specialOffer" name="specialOffer" class="form-control">
    </div>

    <div class="form-group mb-3">
      <label class="form-label" for="weight">Weight (kg)</label>
      <input type="number" step="0.01" id="weight" name="weight" class="form-control">
    </div>

    <div class="form-group mb-3">
      <label class="form-label">Dimensions (cm)</label>
      <div class="d-flex gap-2">
        <input type="number" step="0.01" placeholder="Length" name="length" class="form-control">
        <input type="number" step="0.01" placeholder="Width" name="width" class="form-control">
        <input type="number" step="0.01" placeholder="Height" name="height" class="form-control">
      </div>
    </div>

    <div class="form-group mb-3">
      <label class="form-label" for="notes">Notes</label>
      <textarea id="notes" name="notes" class="form-control"></textarea>
    </div>

    <div class="form-group mb-3">
      <label class="form-label" for="additional">Additional Items</label>
      <input type="text" id="additional" name="additional" class="form-control">
    </div>

    <div class="form-group mb-3 d-flex align-items-center gap-2">
      <input type="checkbox" id="multiPrice" name="multiPrice">
      <label for="multiPrice" class="mb-0">Enable Multiple Prices by Sizes</label>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelModal">Cancel</button>
      <button type="submit" class="btn btn-primary">Upload Product</button>
    </div>
  </div>
</form>

<!-- Embedded Category Section (hidden by default) -->
<div id="categorySection" style="display:none; margin-top:12px;">
  <h5 class="mt-3">Add New Category</h5>
  <div class="form-group mb-3">
    <label for="newCategoryName">Category Name</label>
    <input type="text" id="newCategoryName" class="form-control" placeholder="Enter category name">
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" id="cancelCategoryBtn">Cancel</button>
    <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>
  </div>
</div>