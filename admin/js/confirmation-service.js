class ConfirmationService {
    constructor() {
        this.whatsappService = new WhatsAppService();
        this.qrService = new QRCodeService();
        this.transactionService = new TransactionService();
    }

    async processConfirmation(transactionId, phoneNumber) {
        try {
            NProgress.start();

            const updateResult = await this.transactionService.confirmPayment(transactionId);
            
            const qrResult = await this.qrService.generateAndSave(transactionId);
            
            await this.whatsappService.sendConfirmation(phoneNumber, qrResult.fileUrl);

            return {
                success: true,
                message: 'Payment confirmed and WhatsApp message with QR code sent successfully',
                qrUrl: qrResult.fileUrl
            };

        } catch (error) {
            console.error('Confirmation process failed:', error);
            throw error;
        } finally {
            NProgress.done();
        }
    }

    showSuccess(message) {
        iziToast.success({
            title: 'Success',
            message: message,
            position: 'topRight'
        });
    }

    showError(message) {
        iziToast.error({
            title: 'Error',
            message: message,
            position: 'topRight'
        });
    }

    showInfo(message) {
        iziToast.info({
            title: 'Info',
            message: message,
            position: 'topRight'
        });
    }
}

window.ConfirmationService = ConfirmationService;
