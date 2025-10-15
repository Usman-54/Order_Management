<div class="modal" id="editProductModal">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Product</h5>
              <button class="close-modal" id="closeEditModal">&times;</button>
            </div>
            <div class="modal-body">
              <form id="editProductForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">

                <div class="form-group">
                  <label class="form-label">Product Title</label>
                  <input type="text" class="form-control" name="title" id="editTitle" required>
                </div>

                <div class="form-group">
                  <label class="form-label">Regular Price</label>
                  <input type="number" class="form-control" name="regularPrice" id="editRegularPrice" step="0.01" required>
                </div>

                <div class="form-group">
                  <label class="form-label">Special Offer</label>
                  <input type="number" class="form-control" name="specialOffer" id="editSpecialOffer" step="0.01">
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
                  <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
              </form>
            </div>
          </div>
        </div>