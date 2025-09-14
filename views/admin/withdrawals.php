<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container">
    <h2>Withdrawal Requests</h2>
    <?php flash('withdrawal_message'); ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>PayPal Email</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['withdrawals'] as $withdrawal) : ?>
                <tr>
                    <td><?php echo $withdrawal->id; ?></td>
                    <td><?php echo $withdrawal->username; ?></td>
                    <td>$<?php echo $withdrawal->amount; ?></td>
                    <td><?php echo $withdrawal->paypal_email; ?></td>
                    <td><?php echo $withdrawal->created_at; ?></td>
                    <td><?php echo $withdrawal->status; ?></td>
                    <td>
                        <?php if($withdrawal->status == 'pending') : ?>
                            <form action="<?php echo URLROOT; ?>/admin/process_withdrawal" method="post" style="display: inline-block;">
                                <input type="hidden" name="withdrawal_id" value="<?php echo $withdrawal->id; ?>">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                            <form action="<?php echo URLROOT; ?>/admin/process_withdrawal" method="post" style="display: inline-block;">
                                <input type="hidden" name="withdrawal_id" value="<?php echo $withdrawal->id; ?>">
                                <input type="hidden" name="status" value="rejected">
                                <textarea name="notes" placeholder="Reason for rejection"></textarea>
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                        <?php else: ?>
                            <?php echo ucfirst($withdrawal->status); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>