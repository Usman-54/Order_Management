<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="../../assets/css/profile.css">
</head>
<body>

<div class="container">
  <div class="profile-card">

    <!-- Header -->
    <div class="profile-header">
      <div class="profile-avatar">
        <i class="bi bi-person-fill"></i>
      </div>
      <h3 class="fw-bold mb-0">My Profile</h3>
      <p class="text-light">Manage your personal details</p>
    </div>

    <!-- Body -->
    <div class="profile-body">
      <!-- View Section -->
      <div id="profile-view">
        <div class="info-box">
          <strong>Name:</strong> <span id="v-name"></span>
        </div>
        <div class="info-box">
          <strong>Email:</strong> <span id="v-email"></span>
        </div>
        <div class="info-box">
          <strong>Phone:</strong> <span id="v-phone"></span>
        </div>
        <div class="info-box">
          <strong>Address:</strong> <span id="v-address"></span>
        </div>

        <div class="text-center mt-4">
          <button class="btn btn-primary" id="edit-btn">
            <i class="bi bi-pencil-square me-1"></i> Edit Profile
          </button>
        </div>
      </div>

      <!-- Edit Form -->
      <form id="profile-form" class="d-none">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" name="name" id="name">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" id="email">
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" class="form-control" name="phone" id="phone">
        </div>
        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea class="form-control" name="address" id="address" rows="2"></textarea>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i> Save Changes
          </button>
          <button type="button" id="cancel-btn" class="btn btn-secondary">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
$(document).ready(function() {
  $.ajax({
    url: 'profile_logic.php',
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      if (res.status === 'success') {
        const d = res.data;
        $('#v-name').text(d.name);
        $('#v-email').text(d.email);
        $('#v-phone').text(d.phone_no);
       
        $('#v-address').text(d.address);
        $('#name').val(d.name);
        $('#email').val(d.email);
         $('#phone').val(d.phone_no);
        $('#address').val(d.address);
      } else {
        alert(res.message);
      }
    }
  });
});


  // ✅ Edit
  $('#edit-btn').on('click', function() {
    $('#profile-view').addClass('d-none');
    $('#profile-form').removeClass('d-none');
  });

  // ✅ Cancel
  $('#cancel-btn').on('click', function() {
    $('#profile-form').addClass('d-none');
    $('#profile-view').removeClass('d-none');
  });

  // ✅ Update profile
  $('#profile-form').on('submit', function(e) {
    e.preventDefault();
    $.post('profile_logic.php', $(this).serialize(), function(response) {
      console.log("Update:", response);
      const res = JSON.parse(response);
      if (res.status === 'success') {
        alert('✅ Profile updated successfully!');
        location.reload();
      } else {
        alert('❌ ' + res.message);
      }
    });
  });

</script>



</body>
</html>
