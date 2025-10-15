<form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-4"><input type="text" class="form-control" name="name" placeholder="Full Name" required></div>
                        <div class="col-md-4"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
                        <div class="col-md-4"><input type="tel" class="form-control" name="phone" placeholder="Phone" required></div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Order Date</label>
                            <input type="text" id="delivery_date" class="form-control" name="delivery_date" placeholder="Select date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Order Time</label>
                            <input type="text" id="delivery_time" class="form-control" name="delivery_time" placeholder="Select time" required>
                        </div>
                    </div>

                    <div class="instructions-heading mt-3">Instructions / Notes</div>
                    <textarea class="form-textarea" name="notes" placeholder="Add a note"></textarea>

                    <div class="mt-3 d-flex gap-2">
                        <form id="checkoutForm" method="POST">
                            <!-- your inputs -->
                            <button type="submit" class="btn btn-danger btn-lg">Place Order</button>
                        </form>

                        <!-- <button type="submit" class="btn btn-danger btn-lg">Place Order</button> -->
                        <a href="index.php" class="btn btn-secondary btn-lg">Close</a>
                    </div>
                </form>