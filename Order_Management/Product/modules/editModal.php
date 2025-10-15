<form id="editProductForm" enctype="multipart/form-data">
  <input type="hidden" name="id" id="editId">
  <input type="hidden" name="existingImage" id="editExistingImage">

  <div class="form-group">
    <label class="form-label" for="editCategory">Product Category</label>
    <select id="editCategory" name="category" class="form-control" required>
      <option value="">-- Select Category --</option>
      <option value="All">All</option>
      <option value="Pizza">Pizza</option>
      <option value="Burger">Burger</option>
      <option value="Calzone">Calzone</option>
      <option value="Middagsmany">Middagsmany</option>
      <option value="Kebab">Kebab</option>
      <option value="Salat Meny">Salat Meny</option>
    </select>
  </div>

  <div class="form-group">
    <label class="form-label" for="editTitle">Product Title</label>
    <input type="text" id="editTitle" name="title" class="form-control" required>
  </div>

  <div class="form-group">
    <label class="form-label" for="editDescription">Product Description</label>
    <textarea id="editDescription" name="description" class="form-control"></textarea>
  </div>

  <div class="form-group">
    <label class="form-label" for="editImage">Featured Image</label>
    <input type="file" id="editImage" name="image" class="form-control" accept="image/*">
    <small>Leave empty to keep current image</small>
    <div id="editImagePreview" style="margin-top:10px;"></div>
  </div>

  <div class="form-group">
    <label class="form-label" for="editRegularPrice">Regular Price ($)</label>
    <input type="number" step="0.01" id="editRegularPrice" name="regularPrice" class="form-control" required>
  </div>

  <div class="form-group">
    <label class="form-label" for="editSpecialOffer">Special Offer ($)</label>
    <input type="number" step="0.01" id="editSpecialOffer" name="specialOffer" class="form-control">
  </div>

  <div class="form-group">
    <label class="form-label" for="editWeight">Weight (kg)</label>
    <input type="number" step="0.01" id="editWeight" name="weight" class="form-control">
  </div>

  <div class="form-group">
    <label class="form-label">Dimensions (cm)</label>
    <div style="display:flex; gap:10px;">
      <input type="number" step="0.01" placeholder="Length" id="editLength" name="length" class="form-control">
      <input type="number" step="0.01" placeholder="Width" id="editWidth" name="width" class="form-control">
      <input type="number" step="0.01" placeholder="Height" id="editHeight" name="height" class="form-control">
    </div>
  </div>

  <div class="form-group">
    <label class="form-label" for="editNotes">Notes</label>
    <textarea id="editNotes" name="notes" class="form-control"></textarea>
  </div>

  <div class="form-group">
    <label class="form-label" for="editAdditional">Additional Items</label>
    <input type="text" id="editAdditional" name="additional" class="form-control">
  </div>

  <div class="form-group" style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="editMultiPrice" name="multiPrice">
    <label for="editMultiPrice">Enable Multiple Prices by Sizes</label>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
    <button type="submit" class="btn btn-primary">Update Product</button>
  </div>
</form>
