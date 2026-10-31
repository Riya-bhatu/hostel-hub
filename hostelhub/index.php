<?php $page_title = 'Home'; require 'header.php'; ?>
<section class="hero p-5 rounded-4 mb-4">
  <div class="row align-items-center">
    <div class="col-lg-7">
      <h1 class="display-5 fw-bold">A smarter way to manage hostel life.</h1>
      <p class="lead">View rooms, request accommodation and raise maintenance complaints from one simple portal.</p>
      <a class="btn btn-light btn-lg me-2" href="register.php">Get Started</a>
      <a class="btn btn-outline-light btn-lg" href="rooms.php">Explore Rooms</a>
    </div>
    <div class="col-lg-5 text-center mt-4 mt-lg-0"><div class="hero-icon">🏠</div></div>
  </div>
</section>
<div class="row g-3">
  <div class="col-md-4"><div class="card h-100"><div class="card-body"><h5>Room Management</h5><p>View room types, capacity, availability and monthly fees.</p></div></div></div>
  <div class="col-md-4"><div class="card h-100"><div class="card-body"><h5>Online Requests</h5><p>Submit room allocation requests and track their status.</p></div></div></div>
  <div class="col-md-4"><div class="card h-100"><div class="card-body"><h5>Complaint Tracking</h5><p>Report hostel issues and follow their resolution status.</p></div></div></div>
</div>
<?php require 'footer.php'; ?>
