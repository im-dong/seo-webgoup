<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <?php flash('wallet_message'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h4>Request Withdrawal</h4></div>
                <div class="card-body">
                    <p>Your current withdrawable balance is: <strong class="text-success">$<?php echo number_format($data['wallet']->withdrawable_balance, 2); ?></strong></p>
                    <hr>
                    <form action="<?php echo URLROOT; ?>/wallets" method="post">
                        <div class="form-group mb-3">
                            <label for="amount">Amount (USD): <sup>*</sup></label>
                            <input type="text" name="amount" class="form-control <?php echo (!empty($data['amount_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['amount']; ?>">
                            <span class="invalid-feedback"><?php echo $data['amount_err']; ?></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="paypal_email">Your PayPal Email: <sup>*</sup></label>
                            <input type="email" name="paypal_email" class="form-control <?php echo (!empty($data['paypal_email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['paypal_email']; ?>">
                            <span class="invalid-feedback"><?php echo $data['paypal_email_err']; ?></span>
                        </div>
                        <div class="d-grid">
                            <input type="submit" class="btn btn-success" value="Submit Request">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h4>Withdrawal History</h4>
            <p>Coming soon...</p>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>