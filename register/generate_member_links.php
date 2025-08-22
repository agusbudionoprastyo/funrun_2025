<?php
include('../helper/db.php');

// Define member codes for different communities
$memberCodes = [
    'SEMARANGRUNNER' => 'Semarang Runner Community',
    'FAKERUNNER' => 'Fake Runner Community',
    'BERLARIBERSAMA' => 'Berlari Bersama Community',
    'PLAYONAMBYAR' => 'Playon Ambyar Community',
    'PLAYONNDESO' => 'Playon Ndeso Community',
    'BESTIFITY' => 'Bestifity Community',
    'DURAKINGRUN' => 'Duraking Run Community',
    'SALATIGARB' => 'Salatiga Running Community',
    'PELARIAN' => 'Pelarian Community'
];

// Generate registration link for a specific member code
function generateRegistrationLink($memberCode, $baseUrl = null) {
    if (!$baseUrl) {
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $baseUrl .= dirname($_SERVER['REQUEST_URI']) . "/index.html";
    }
    
    return $baseUrl . "?member=" . urlencode($memberCode);
}

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$baseUrl .= dirname($_SERVER['REQUEST_URI']) . "/index.html";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Referral Links - Lari Sama Mantan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">
                <i class="fas fa-users mr-3 text-[#ff5b1c]"></i>
                Member Referral Links
            </h1>
            
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    How to Use
                </h2>
                <div class="text-gray-600 space-y-2">
                    <p>• Each link below is specific to a community/member referral</p>
                    <p>• When users click these links, they will be marked as referred by that community</p>
                    <p>• Users will see a "Member Registration" indicator showing who referred them</p>
                    <p>• No discount or voucher is applied - this is purely for tracking purposes</p>
                </div>
            </div>

            <div class="grid gap-6">
                <?php foreach ($memberCodes as $code => $description): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fas fa-user-friends mr-2 text-[#ff5b1c]"></i>
                                    <?php echo htmlspecialchars($code); ?>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    <?php echo htmlspecialchars($description); ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Referral Link
                                </span>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       value="<?php echo generateRegistrationLink($code, $baseUrl); ?>" 
                                       class="flex-1 p-2 border border-gray-300 rounded text-sm bg-gray-50" 
                                       readonly>
                                <button onclick="copyToClipboard(this.previousElementSibling)" 
                                        class="px-4 py-2 bg-[#ff5b1c] text-white rounded hover:bg-[#e54d1a] transition-colors text-sm">
                                    <i class="fas fa-copy mr-1"></i>Copy
                                </button>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="<?php echo generateRegistrationLink($code, $baseUrl); ?>" 
                                   target="_blank"
                                   class="flex-1 px-4 py-2 bg-blue-500 text-white text-center rounded hover:bg-blue-600 transition-colors text-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i>Test Link
                                </a>
                                
                                <button onclick="generateQRCode('<?php echo generateRegistrationLink($code, $baseUrl); ?>', '<?php echo $code; ?>')" 
                                        class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors text-sm">
                                    <i class="fas fa-qrcode mr-1"></i>QR Code
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Quick Links for Common Communities
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="?member=SEMARANGRUNNER" class="block p-3 bg-white rounded border hover:shadow-md transition-shadow">
                        <div class="font-medium text-gray-800">Semarang Runner</div>
                        <div class="text-sm text-gray-600">SEMARANGRUNNER</div>
                    </a>
                    <a href="?member=FAKERUNNER" class="block p-3 bg-white rounded border hover:shadow-md transition-shadow">
                        <div class="font-medium text-gray-800">Fake Runner</div>
                        <div class="text-sm text-gray-600">FAKERUNNER</div>
                    </a>
                    <a href="?member=BERLARIBERSAMA" class="block p-3 bg-white rounded border hover:shadow-md transition-shadow">
                        <div class="font-medium text-gray-800">Berlari Bersama</div>
                        <div class="text-sm text-gray-600">BERLARIBERSAMA</div>
                    </a>
                    <a href="?member=PELARIAN" class="block p-3 bg-white rounded border hover:shadow-md transition-shadow">
                        <div class="font-medium text-gray-800">Pelarian</div>
                        <div class="text-sm text-gray-600">PELARIAN</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div id="qrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">QR Code</h3>
                <button onclick="closeQRModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="qrCodeContainer" class="text-center"></div>
            <div class="mt-4 text-center">
                <button onclick="downloadQRCode()" class="px-4 py-2 bg-[#ff5b1c] text-white rounded hover:bg-[#e54d1a] transition-colors">
                    <i class="fas fa-download mr-1"></i>Download QR Code
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        function copyToClipboard(input) {
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            
            const button = input.nextElementSibling;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
            button.classList.remove('bg-[#ff5b1c]', 'hover:bg-[#e54d1a]');
            button.classList.add('bg-green-500', 'hover:bg-green-600');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                button.classList.add('bg-[#ff5b1c]', 'hover:bg-[#e54d1a]');
            }, 2000);
        }

        function generateQRCode(url, memberCode) {
            const modal = document.getElementById('qrModal');
            const container = document.getElementById('qrCodeContainer');
            
            container.innerHTML = '<div class="text-gray-500">Generating QR Code...</div>';
            modal.classList.remove('hidden');
            
            QRCode.toCanvas(container, url, {
                width: 200,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            }, function (error) {
                if (error) {
                    container.innerHTML = '<div class="text-red-500">Error generating QR code</div>';
                } else {
                    const canvas = container.querySelector('canvas');
                    canvas.style.border = '1px solid #e5e7eb';
                    canvas.style.borderRadius = '8px';
                    
                    // Add member code label
                    const label = document.createElement('div');
                    label.className = 'mt-2 text-sm text-gray-600';
                    label.textContent = memberCode;
                    container.appendChild(label);
                }
            });
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }

        function downloadQRCode() {
            const canvas = document.querySelector('#qrCodeContainer canvas');
            if (canvas) {
                const link = document.createElement('a');
                link.download = 'member-referral-qr.png';
                link.href = canvas.toDataURL();
                link.click();
            }
        }

        // Close modal when clicking outside
        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRModal();
            }
        });
    </script>
</body>
</html>
