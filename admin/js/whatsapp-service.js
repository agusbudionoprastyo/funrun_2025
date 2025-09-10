class WhatsAppService {
    constructor() {
        this.textApiUrl = 'https://dev-iptv-wa.appdewa.com/message/send-text';
        this.imageApiUrl = 'https://dev-iptv-wa.appdewa.com/message/send-image';
        this.session = 'funrun';
        this.messageTemplate = `*Pembayaran Anda telah diverifikasi!*

_Tunjukkan QR code ini kepada staff kami saat pengambilan_ *RCP* (racepack).
_Terima kasih atas partisipasi anda._

*Pengambilan Racepack*
11 Oktober 2025
10:00 - 19:00 WIB
Hotel Dafam Semarang

*Funrun - Lari Sama Mantan*
Tgl 12 Oktober 2025
Start 06:00 WIB

*access Runmap*
https://funrun.dafam.cloud`;
    }

    async sendConfirmation(phoneNumber, qrCodeUrl = null, retries = 3) {
        if (!phoneNumber) {
            throw new Error('Phone number is required');
        }

        const apiUrl = qrCodeUrl ? this.imageApiUrl : this.textApiUrl;
        const payload = {
            session: this.session,
            to: phoneNumber,
            text: this.messageTemplate
        };

        if (qrCodeUrl) {
            payload.image_url = qrCodeUrl;
        }

        for (let attempt = 1; attempt <= retries; attempt++) {
            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                
                if (result.success === false) {
                    throw new Error(result.message || 'WhatsApp API returned error');
                }

                return { success: true, data: result };
            } catch (error) {
                console.warn(`WhatsApp attempt ${attempt} failed:`, error.message);
                
                if (attempt === retries) {
                    throw new Error(`Failed after ${retries} attempts: ${error.message}`);
                }
                
                await this.delay(1000 * attempt);
            }
        }
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

window.WhatsAppService = WhatsAppService;
