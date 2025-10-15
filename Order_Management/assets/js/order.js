document.addEventListener('DOMContentLoaded', () => {

  const modal = document.getElementById('orderModal');
  const modalBody = document.getElementById('modalBody');
  const closeModal = document.getElementById('closeModal');

  closeModal.onclick = () => modal.style.display = 'none';
  window.onclick = e => { if(e.target === modal) modal.style.display = 'none'; }

  const reloadPage = () => location.reload();

  // Fetch modal content
  async function fetchModal(url){
    try {
      const res = await fetch(url);
      modalBody.innerHTML = await res.text();
      modal.style.display = 'flex';
      attachModalForm();
    } catch(err) {
      alert('Error fetching modal content: ' + err.message);
    }
  }

  // Attach form submission for modal
  function attachModalForm(){
    const form = document.getElementById('updateOrderForm');
    if(!form) return;

    form.addEventListener('submit', async e => {
      e.preventDefault();
      const formData = new FormData(form);

      try {
        const res = await fetch('order_manage.php', { method:'POST', body: formData });
        const text = await res.text();
        if(text.trim() === 'Success'){
          alert('✅ Order updated successfully');
          modal.style.display = 'none';
          reloadPage();
        } else {
          alert('❌ Update failed: ' + text);
        }
      } catch(err){
        alert('❌ AJAX error: ' + err.message);
      }
    });
  }

  // View
  document.querySelectorAll('.viewBtn').forEach(btn => btn.addEventListener('click', ()=> fetchModal(`order_manage.php?view=${btn.dataset.id}`)));

  // Edit
  document.querySelectorAll('.editBtn').forEach(btn => btn.addEventListener('click', ()=> fetchModal(`order_manage.php?update=${btn.dataset.id}`)));

  // Delete
  document.querySelectorAll('.deleteBtn').forEach(btn => btn.addEventListener('click', async ()=>{
    if(!confirm('Delete this order?')) return;
    try{
      const res = await fetch(`order_manage.php?delete=${btn.dataset.id}`);
      const text = await res.text();
      alert(text.includes('Deleted') ? '🗑️ Order Deleted!' : '❌ Error: '+text);
      reloadPage();
    } catch(err){ alert('❌ AJAX error: '+err.message);}
  }));

  // Inline status
  document.querySelectorAll('.status-select').forEach(select => select.addEventListener('change', async ()=>{
    const formData = new URLSearchParams({ id: select.dataset.id, status: select.value });
    try{
      const res = await fetch('order_manage.php', { method:'POST', body: formData });
      const text = await res.text();
      alert(text.includes('Error') ? '❌ ' + text : '✅ Status updated!');
      reloadPage();
    } catch(err){ alert('❌ AJAX error: '+err.message);}
  }));

});
