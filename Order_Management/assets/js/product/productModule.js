
  /* ========== CREATE PRODUCT (AJAX) ========== */
  // @ts-nocheck

document.getElementById("addProductForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const res = await fetch("../backend/productApi.php?action=create", {
      method: "POST",
      body: formData
    });
    const data = await res.json();
    alert(data.message); // show success/error like delete
    if (data.status === "success") {
      this.reset();
      document.getElementById("addProductModal").style.display = "none";
      loadProducts(); // refresh table
    }
  } catch (error) {
    console.error("Error creating product:", error);
  }
});
/* ========== LOAD PRODUCTS ========== */
async function loadProducts() {
  try {
    const res = await fetch("../backend/productApi.php?action=read");
    const data = await res.json();
    const tbody = document.querySelector("#productsTable tbody");
    tbody.innerHTML = "";
    if (data.status === "success" && data.data.length > 0) {
      data.data.forEach(product => {
        const row = `
          <tr>
            <td>${product.id}</td>
            <td>${product.category}</td>
            <td>${product.title}</td>
            <td>$${product.regular_price}</td>
            <td>$${product.special_offer ?? '-'}</td>
            <td>${product.image ? `<img src="backend/${product.image}" width="50">` : '-'}</td>
            <td>
              <button class="action-btn edit" onclick='openEditModal(${JSON.stringify(product)})'>
                <i class="fas fa-edit"></i>
              </button>
              <button class="action-btn delete" onclick="deleteProduct(${product.id})">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        `;

        tbody.insertAdjacentHTML("beforeend", row);
      });
    } else {
      tbody.innerHTML = "<tr><td colspan='7'>No products found.</td></tr>";
    }
  } catch (error) {
    console.error("Error loading products:", error);
  }
}
/* ========== OPEN EDIT MODAL ========== */
function openEditModal(product) {
  document.getElementById("editId").value = product.id;
  document.getElementById("editCategory").value = product.category;
  document.getElementById("editTitle").value = product.title;
  document.getElementById("editDescription").value = product.description;
  document.getElementById("editRegularPrice").value = product.regular_price;
  document.getElementById("editSpecialOffer").value = product.special_offer;
  document.getElementById("editWeight").value = product.weight;
  document.getElementById("editLength").value = product.length;
  document.getElementById("editWidth").value = product.width;
  document.getElementById("editHeight").value = product.height;
  document.getElementById("editNotes").value = product.notes;
  document.getElementById("editAdditional").value = product.additional;
  document.getElementById("editMultiPrice").checked = product.multi_price == 1;

  // Image preview
  document.getElementById("editExistingImage").value = product.image;
  document.getElementById("editImagePreview").innerHTML = product.image 
    ? `<img src="../backend/${product.image}" width="80">`
    : "";

  document.getElementById("editProductModal").style.display = "flex";
}

/* ========== CLOSE EDIT MODAL ========== */
document.getElementById("closeEditModal").onclick = () => {
  document.getElementById("editProductModal").style.display = "none";
};
document.getElementById("cancelEdit").onclick = () => {
  document.getElementById("editProductModal").style.display = "none";
};
/* ========== UPDATE PRODUCT ========== */
document.getElementById("editProductForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const res = await fetch("../backend/productApi.php?action=update", {
      method: "POST",
      body: formData
    });
    const data = await res.json();
    alert(data.message);
    if (data.status === "success") {
      this.reset();
      document.getElementById("editProductModal").style.display = "none";
      loadProducts();
    }
  } catch (error) {
    console.error("Error updating product:", error);
  }
});
/* ========== DELETE PRODUCT ========== */
async function deleteProduct(id) {
  if (!confirm("Are you sure you want to delete this product?")) return;
  const formData = new FormData();
  formData.append("id", id);
  try {
    const res = await fetch("../backend/productApi.php?action=delete", {
      method: "POST",
      body: formData
    });
    const data = await res.json();
    alert(data.message);
    if (data.status === "success") {
      loadProducts();
    }
  } catch (error) {
    console.error("Error deleting product:", error);
  }
}
/* ========== INITIAL LOAD ========== */
document.addEventListener("DOMContentLoaded", loadProducts);