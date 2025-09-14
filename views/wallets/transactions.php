<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Wallet Transactions</h2>
    <p>Here you can view all your wallet transactions.</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Order ID</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($data['transactions'])): ?>
                <tr>
                    <td colspan="5" class="text-center">You have no transactions yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach($data['transactions'] as $transaction): ?>
                    <tr>
                        <td><?php echo date('Y-m-d H:i', strtotime($transaction->created_at)); ?></td>
                        <td><?php echo ucfirst($transaction->type); ?></td>
                        <td>$<?php echo number_format($transaction->amount, 2); ?></td>
                        <td><?php echo htmlspecialchars($transaction->description); ?></td>
                        <td>
                            <?php if($transaction->order_id): ?>
                                <a href="<?php echo URLROOT; ?>/orders/details/<?php echo $transaction->order_id; ?>">
                                    #<?php echo $transaction->order_id; ?>
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
