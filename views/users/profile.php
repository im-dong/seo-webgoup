<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-body bg-light">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <img src="<?php echo !empty($data['user']->profile_image_url) ? (strpos($data['user']->profile_image_url, 'http') === 0 ? $data['user']->profile_image_url : URLROOT . $data['user']->profile_image_url) : '/assets/default.png'; ?>" class="img-fluid rounded-circle mb-3" alt="Profile Picture">
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
                <?php if ($data['is_admin_viewing']): ?>
                <div class="card border-danger mt-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-user-shield"></i> Admin Panel - Complete User Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Basic Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>User ID:</strong></td>
                                        <td><code><?php echo htmlspecialchars($data['user']->id); ?></code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Username:</strong></td>
                                        <td><?php echo htmlspecialchars($data['user']->username); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td><?php echo htmlspecialchars($data['user']->email); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Display Name:</strong></td>
                                        <td><?php echo htmlspecialchars($data['user']->display_name ?? 'Not set'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Role:</strong></td>
                                        <td><span class="badge bg-<?php echo ($data['user']->role == 'admin') ? 'danger' : 'primary'; ?>"><?php echo htmlspecialchars($data['user']->role ?? 'user'); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Status:</strong></td>
                                        <td><span class="badge bg-<?php echo ($data['user']->status ?? 1) ? 'success' : 'secondary'; ?>"><?php echo ($data['user']->status ?? 1) ? 'Active' : 'Inactive'; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email Verified:</strong></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($data['user']->email_verified ?? 0) ? 'success' : 'warning'; ?>">
                                                <?php echo ($data['user']->email_verified ?? 0) ? 'Verified' : 'Not Verified'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Member Since:</strong></td>
                                        <td><?php echo date('M j, Y H:i', strtotime($data['user']->created_at)); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Profile Details</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Profile Image:</strong></td>
                                        <td>
                                            <?php if(!empty($data['user']->profile_image_url)): ?>
                                                <img src="<?php echo (strpos($data['user']->profile_image_url, 'http') === 0 ? $data['user']->profile_image_url : URLROOT . $data['user']->profile_image_url); ?>" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                            <?php else: ?>
                                                <span class="text-muted">Default avatar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if(!empty($data['user']->website_url)): ?>
                                    <tr>
                                        <td><strong>Website:</strong></td>
                                        <td><a href="<?php echo htmlspecialchars($data['user']->website_url); ?>" target="_blank"><?php echo htmlspecialchars($data['user']->website_url); ?></a></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(!empty($data['user']->country)): ?>
                                    <tr>
                                        <td><strong>Country:</strong></td>
                                        <td><?php echo htmlspecialchars($data['user']->country); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(!empty($data['user']->contact_method)): ?>
                                    <tr>
                                        <td><strong>Contact Method:</strong></td>
                                        <td><?php echo htmlspecialchars($data['user']->contact_method); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(!empty($data['user']->bio)): ?>
                                    <tr>
                                        <td><strong>Bio:</strong></td>
                                        <td><?php echo nl2br(htmlspecialchars(substr($data['user']->bio, 0, 200))); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3">
                            <small><i class="fas fa-info-circle"></i> This detailed user information is only visible to administrators for account management purposes.</small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <hr>
                <h4>Bio:</h4>
                <p><?php echo !empty($data['user']->bio) ? nl2br(htmlspecialchars($data['user']->bio)) : 'No bio available.'; ?></p>
                <hr>
                <h4>Average Rating: 
                    <?php if($data['average_rating'] > 0): ?>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($data['average_rating']); ?> / 5</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">No ratings yet</span>
                    <?php endif; ?>
                </h4>
                
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $data['user']->id): ?>
                    <?php if($data['order_id_for_chat']): ?>
                        <a href="<?php echo URLROOT; ?>/conversations/start/<?php echo $data['order_id_for_chat']; ?>" class="btn btn-primary mt-3">Message <?php echo htmlspecialchars($data['user']->username); ?></a>
                    <?php else: ?>
                        <button class="btn btn-primary mt-3" disabled>Message <?php echo htmlspecialchars($data['user']->username); ?></button>
                        <small class="text-muted d-block">You can only message users with whom you have an order.</small>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="reviewTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="seller-reviews-tab" data-bs-toggle="tab" data-bs-target="#seller-reviews" type="button" role="tab" aria-controls="seller-reviews" aria-selected="true">Reviews as Seller</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="buyer-reviews-tab" data-bs-toggle="tab" data-bs-target="#buyer-reviews" type="button" role="tab" aria-controls="buyer-reviews" aria-selected="false">Reviews as Buyer</button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content" id="reviewTabsContent">
                    <div class="tab-pane fade show active" id="seller-reviews" role="tabpanel" aria-labelledby="seller-reviews-tab">
                        <h4 class="mt-4">Reviews as Seller:</h4>
                        <?php if(empty($data['seller_reviews'])): ?>
                            <p class="text-muted">No reviews as a seller yet.</p>
                        <?php else: ?>
                            <div class="review-list-seller">
                                <?php foreach(array_slice($data['seller_reviews'], 0, 5) as $review): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Rating: <?php echo htmlspecialchars($review->rating); ?> / 5</h5>
                                            <h6 class="card-subtitle mb-2 text-muted">By <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $review->reviewer_id; ?>"><?php echo htmlspecialchars($review->reviewer_username); ?></a> on <?php echo date('Y-m-d', strtotime($review->created_at)); ?></h6>
                                            <p class="card-text"><?php echo nl2br(htmlspecialchars($review->comment)); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if(count($data['seller_reviews']) > 5): ?>
                                <button class="btn btn-outline-primary btn-sm" id="show-more-seller">Show more</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="tab-pane fade" id="buyer-reviews" role="tabpanel" aria-labelledby="buyer-reviews-tab">
                        <h4 class="mt-4">Reviews as Buyer:</h4>
                        <?php if(empty($data['buyer_reviews'])): ?>
                            <p class="text-muted">No reviews as a buyer yet.</p>
                        <?php else: ?>
                            <div class="review-list-buyer">
                                <?php foreach(array_slice($data['buyer_reviews'], 0, 5) as $review): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Rating: <?php echo htmlspecialchars($review->rating); ?> / 5</h5>
                                            <h6 class="card-subtitle mb-2 text-muted">For <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $review->seller_id; ?>"><?php echo htmlspecialchars($review->seller_username); ?></a> on <?php echo date('Y-m-d', strtotime($review->created_at)); ?></h6>
                                            <p class="card-text"><?php echo nl2br(htmlspecialchars($review->comment)); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if(count($data['buyer_reviews']) > 5): ?>
                                <button class="btn btn-outline-primary btn-sm" id="show-more-buyer">Show more</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sellerReviews = <?php echo json_encode($data['seller_reviews']); ?>;
    const buyerReviews = <?php echo json_encode($data['buyer_reviews']); ?>;
    const sellerReviewList = document.querySelector('.review-list-seller');
    const buyerReviewList = document.querySelector('.review-list-buyer');
    const showMoreSellerBtn = document.getElementById('show-more-seller');
    const showMoreBuyerBtn = document.getElementById('show-more-buyer');
    let sellerOffset = 5;
    let buyerOffset = 5;

    if (showMoreSellerBtn) {
        showMoreSellerBtn.addEventListener('click', function() {
            const reviewsToShow = sellerReviews.slice(sellerOffset, sellerOffset + 5);
            reviewsToShow.forEach(review => {
                const reviewCard = createReviewCard(review, 'seller');
                sellerReviewList.appendChild(reviewCard);
            });
            sellerOffset += 5;
            if (sellerOffset >= sellerReviews.length) {
                showMoreSellerBtn.style.display = 'none';
            }
        });
    }

    if (showMoreBuyerBtn) {
        showMoreBuyerBtn.addEventListener('click', function() {
            const reviewsToShow = buyerReviews.slice(buyerOffset, buyerOffset + 5);
            reviewsToShow.forEach(review => {
                const reviewCard = createReviewCard(review, 'buyer');
                buyerReviewList.appendChild(reviewCard);
            });
            buyerOffset += 5;
            if (buyerOffset >= buyerReviews.length) {
                showMoreBuyerBtn.style.display = 'none';
            }
        });
    }

    function createReviewCard(review, type) {
        const card = document.createElement('div');
        card.className = 'card mb-3';

        const cardBody = document.createElement('div');
        cardBody.className = 'card-body';

        const rating = document.createElement('h5');
        rating.className = 'card-title';
        rating.textContent = `Rating: ${review.rating} / 5`;

        const subtitle = document.createElement('h6');
        subtitle.className = 'card-subtitle mb-2 text-muted';
        if (type === 'seller') {
            subtitle.innerHTML = `By <a href="<?php echo URLROOT; ?>/users/profile/${review.reviewer_id}">${review.reviewer_username}</a> on ${new Date(review.created_at).toLocaleDateString()}`;
        } else {
            subtitle.innerHTML = `For <a href="<?php echo URLROOT; ?>/users/profile/${review.seller_id}">${review.seller_username}</a> on ${new Date(review.created_at).toLocaleDateString()}`;
        }

        const comment = document.createElement('p');
        comment.className = 'card-text';
        comment.innerHTML = review.comment.replace(/\n/g, '<br>');

        cardBody.appendChild(rating);
        cardBody.appendChild(subtitle);
        cardBody.appendChild(comment);
        card.appendChild(cardBody);

        return card;
    }
});
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>