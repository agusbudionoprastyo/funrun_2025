class QRCodeService {
    constructor() {
        this.qrSize = 500;
    }

    async generateQRCode(transactionId) {
        return new Promise((resolve, reject) => {
            try {
                QRCode.toDataURL(transactionId, { 
                    width: this.qrSize, 
                    height: this.qrSize 
                }, (err, url) => {
                    if (err) reject(err);
                    else resolve(url);
                });
            } catch (err) {
                reject(err);
            }
        });
    }

    async saveQRCode(qrCodeDataUrl, transactionId) {
        const response = await fetch('save_qr_code.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                qr_code_data_url: qrCodeDataUrl, 
                transaction_id: transactionId 
            })
        });

        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to save QR code');
        }

        return result.file_url;
    }

    async generateAndSave(transactionId) {
        const qrCodeDataUrl = await this.generateQRCode(transactionId);
        const fileUrl = await this.saveQRCode(qrCodeDataUrl, transactionId);
        return { dataUrl: qrCodeDataUrl, fileUrl };
    }
}

window.QRCodeService = QRCodeService;
