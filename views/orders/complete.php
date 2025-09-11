<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="row mt-5">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light">
            <h2>Mark Order as Complete</h2>
            <p>Please provide the URL where you have placed the link as proof of completion.</p>
            <form action="<?php echo URLROOT; ?>/orders/complete/<?php echo $data['order_id']; ?>" method="post">
                <div class="form-group mb-3">
                    <label for="proof_url">Proof URL: <sup>*</sup></label>
                    <input type="url" name="proof_url" class="form-control <?php echo (!empty($data['proof_url_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['proof_url']; ?>">
                    <span class="invalid-feedback"><?php echo $data['proof_url_err']; ?></span>
                </div>
                <input type="submit" class="btn btn-success" value="Submit Proof">
                <a href="<?php echo URLROOT; ?>/users/dashboard" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>