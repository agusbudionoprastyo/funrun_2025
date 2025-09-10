class TransactionService {
    async updateStatus(transactionId, status) {
        const response = await fetch('update_transactions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                transaction_id: transactionId, 
                status: status 
            })
        });

        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Failed to update transaction status');
        }

        return result;
    }

    async confirmPayment(transactionId) {
        return await this.updateStatus(transactionId, 'verified');
    }
}

window.TransactionService = TransactionService;
