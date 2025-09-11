<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-body bg-light">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <img src="<?php echo htmlspecialchars($data['user']->profile_image_url); ?>" class="img-fluid rounded-circle mb-3" alt="Profile Picture">
                    </div>
                    <div class="col-md-9">
                        <h2><?php echo htmlspecialchars($data['user']->username); ?>'s Profile</h2>
                        <p class="text-muted">Member since: <?php echo date('Y-m-d', strtotime($data['user']->created_at)); ?></p>
                        <?php if(!empty($data['user']->country)): ?>
                            <p class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($data['user']->country); ?></p>
                        <?php endif; ?>
                        <?php if(!empty($data['user']->website_url)): ?>
                            <a href="<?php echo htmlspecialchars($data['user']->website_url); ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Visit Website</a>
                        <?php endif; ?>
                    </div>
                </div>
                <hr>
                <h4>Bio:</h4>
                <p><?php echo nl2br(htmlspecialchars($data['user']->bio)); ?></p>
                <hr>
                <h4>Average Rating: 
                    <?php if($data['average_rating'] > 0): ?>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($data['average_rating']); ?> / 5</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">No ratings yet</span>
                    <?php endif; ?>
                </h4>
                
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $data['user']->id): ?>
                    <a href="<?php echo URLROOT; ?>/conversations/start/<?php echo $data['order_id_for_chat']; ?>" class="btn btn-primary mt-3">Message <?php echo htmlspecialchars($data['user']->username); ?></a>
                <?php endif; ?>

                <h4 class="mt-4">Reviews:</h4>
                <?php if(empty($data['reviews'])): ?>
                    <p class="text-muted">No reviews yet.</p>
                <?php else: ?>
                    <?php foreach($data['reviews'] as $review): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Rating: <?php echo htmlspecialchars($review->rating); ?> / 5</h5>
                                <h6 class="card-subtitle mb-2 text-muted">By <?php echo htmlspecialchars($review->reviewer_username); ?> on <?php echo date('Y-m-d', strtotime($review->created_at)); ?></h6>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($review->comment)); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>