// Size Chart Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sizeChartBtn = document.getElementById('sizeChartBtn');
    const sizeChartModal = document.getElementById('sizeChartModal');
    const closeSizeChartBtn = document.getElementById('closeSizeChartBtn');

    // Open size chart modal
    sizeChartBtn.addEventListener('click', function() {
        sizeChartModal.classList.remove('hidden');
    });

    // Close size chart modal
    closeSizeChartBtn.addEventListener('click', function() {
        sizeChartModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    sizeChartModal.addEventListener('click', function(e) {
        if (e.target === sizeChartModal) {
            sizeChartModal.classList.add('hidden');
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !sizeChartModal.classList.contains('hidden')) {
            sizeChartModal.classList.add('hidden');
        }
    });

    // Jersey Preview Modal Functionality
    const jerseyModal = document.getElementById('jerseyModal');
    const closeJerseyBtn = document.getElementById('closeJerseyBtn');

    // Close jersey modal
    closeJerseyBtn.addEventListener('click', function() {
        jerseyModal.classList.add('hidden');
    });

    // Close jersey modal when clicking outside
    jerseyModal.addEventListener('click', function(e) {
        if (e.target === jerseyModal) {
            jerseyModal.classList.add('hidden');
        }
    });

    // Close jersey modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !jerseyModal.classList.contains('hidden')) {
            jerseyModal.classList.add('hidden');
        }
    });

    // Jersey Order Modal Functionality
    const jerseyOrderModal = document.getElementById('jerseyOrderModal');
    const closeJerseyOrderBtn = document.getElementById('closeJerseyOrderBtn');
    const jerseyOrderForm = document.getElementById('jerseyOrderForm');

    // Close jersey order modal
    closeJerseyOrderBtn.addEventListener('click', function() {
        jerseyOrderModal.classList.add('hidden');
    });

    // Close jersey order modal when clicking outside
    jerseyOrderModal.addEventListener('click', function(e) {
        if (e.target === jerseyOrderModal) {
            jerseyOrderModal.classList.add('hidden');
        }
    });

    // Close jersey order modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !jerseyOrderModal.classList.contains('hidden')) {
            jerseyOrderModal.classList.add('hidden');
        }
    });

    // Handle jersey order form submission
    jerseyOrderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('orderName').value;
        const address = document.getElementById('orderPhone').value;
        const color = document.querySelector('input[name="orderJerseyColor"]:checked').value;
        const size = document.querySelector('input[name="orderJerseySize"]:checked').value;
        
        // Format the message for WhatsApp
        const message = `*ORDER JERSEY FUN RUN 2025*%0A%0A` +
            `*Nama:* ${name}%0A` +
            `*Alamat Pengiriman:* ${address}%0A` +
            `*Warna Jersey:* ${color.charAt(0).toUpperCase() + color.slice(1)}%0A` +
            `*Size Jersey:* ${size}%0A` +
            `*Harga:* Rp 100.000%0A%0A` +
            `*Fun Run - Lari Sama Mantan*%0A` +
            `12 Oktober 2025%0A` +
            `Hotel Dafam Semarang`;
        
        // Open WhatsApp with the order details
        const whatsappUrl = `https://wa.me/6281770019808?text=${message}`;
        window.open(whatsappUrl, '_blank');
        
        // Close the modal
        jerseyOrderModal.classList.add('hidden');
        
        // Reset the form
        jerseyOrderForm.reset();
    });
});

// Function to show jersey popup
function showJerseyPopup(color) {
    const jerseyModal = document.getElementById('jerseyModal');
    const jerseyPreview = document.getElementById('jerseyPreview');
    
    // Set the image source based on color
    jerseyPreview.src = `./assets/image/jersey-${color}.png`;
    jerseyPreview.alt = `${color.charAt(0).toUpperCase() + color.slice(1)} Jersey Preview`;
    
    // Show the modal
    jerseyModal.classList.remove('hidden');
}

// Function to open jersey order modal
function openJerseyOrderModal() {
    const jerseyOrderModal = document.getElementById('jerseyOrderModal');
    jerseyOrderModal.classList.remove('hidden');
}

function copyToClipboard() {
    const rekNumber = document.getElementById('rek-number').innerText;
    
    navigator.clipboard.writeText(rekNumber)
      .then(() => {
        alert('Nomor rekening berhasil disalin!');
      })
      .catch(err => {
        console.error('Gagal menyalin: ', err);
      });
  }

  const singleRadio = document.getElementById('single');
  const coupleRadio = document.getElementById('couple');
  const coupleContainer = document.getElementById('CoupleContainer');
  const coupleName = document.getElementById('coupleName');
  const coupleMantan = document.getElementById('coupleMantan');

  const scouple = document.getElementById('scouple');
  const mcouple = document.getElementById('mcouple');
  const lcouple = document.getElementById('lcouple');
  const xlcouple = document.getElementById('xlcouple');
  const xxlcouple = document.getElementById('xxlcouple');
  const xxxlcouple = document.getElementById('3xlcouple'); // Updated ID

  const coupleDarkblue = document.getElementById('coupleDarkblue');
  const couplePurple = document.getElementById('couplePurple');

  const registerBtn = document.getElementById("registerBtn");
  const closeBtn = document.getElementById("closeBtn");
  const box = document.getElementById("box");
  const signupContainer = document.getElementById("signup-container");

  // Hide the Couple Section initially and remove required attributes
  if (coupleContainer && coupleContainer.classList.contains('hidden')) {
    if (coupleName) coupleName.removeAttribute('required');
    if (coupleMantan) coupleMantan.removeAttribute('required');
    if (scouple) scouple.removeAttribute('required');
    if (mcouple) mcouple.removeAttribute('required');
    if (lcouple) lcouple.removeAttribute('required');
    if (xlcouple) xlcouple.removeAttribute('required');
    if (xxlcouple) xxlcouple.removeAttribute('required');
    if (xxxlcouple) xxxlcouple.removeAttribute('required');
    if (coupleDarkblue) coupleDarkblue.removeAttribute('required');
    if (couplePurple) couplePurple.removeAttribute('required');
  }

  // Show Couple Section and add required attributes when "Couple" radio is checked
  if (coupleRadio) {
    coupleRadio.addEventListener('change', function () {
      if (this.checked && coupleContainer) {
        coupleContainer.classList.remove('hidden');
        if (coupleName) coupleName.setAttribute('required', 'true');
        if (coupleMantan) coupleMantan.setAttribute('required', 'true');
        if (scouple) scouple.setAttribute('required', 'true');
        if (mcouple) mcouple.setAttribute('required', 'true');
        if (lcouple) lcouple.setAttribute('required', 'true');
        if (xlcouple) xlcouple.setAttribute('required', 'true');
        if (xxlcouple) xxlcouple.setAttribute('required', 'true');
        if (xxxlcouple) xxxlcouple.setAttribute('required', 'true');
        if (coupleDarkblue) coupleDarkblue.setAttribute('required', 'true');
        if (couplePurple) couplePurple.setAttribute('required', 'true');
      }
    });
  }

  // Hide Couple Section and remove required attributes when "Single" radio is checked
  if (singleRadio) {
    singleRadio.addEventListener('change', function () {
      if (this.checked && coupleContainer) {
        coupleContainer.classList.add('hidden');
        if (coupleName) coupleName.removeAttribute('required');
        if (coupleMantan) coupleMantan.removeAttribute('required');
        if (scouple) scouple.removeAttribute('required');
        if (mcouple) mcouple.removeAttribute('required');
        if (lcouple) lcouple.removeAttribute('required');
        if (xlcouple) xlcouple.removeAttribute('required');
        if (xxlcouple) xxlcouple.removeAttribute('required');
        if (xxxlcouple) xxxlcouple.removeAttribute('required');
        if (coupleDarkblue) coupleDarkblue.removeAttribute('required');
        if (couplePurple) couplePurple.removeAttribute('required');
      }
    });
  }

  if (registerBtn) {
    registerBtn.addEventListener("click", function() {
        if (box) box.classList.add("expanded");
        registerBtn.classList.add("hidden");
        setTimeout(function() {
          if (signupContainer) signupContainer.classList.remove("hidden");
        }, 200);
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", function() {
        if (box) box.classList.remove("expanded");
        if (registerBtn) registerBtn.classList.remove("hidden");
        setTimeout(function() {
          if (signupContainer) signupContainer.classList.add("hidden");
        }, 200);
    });
  }

//   function formatPrice(price) {
//       if (price >= 1000000) {
//           return (price / 1000000).toFixed(1) + 'M';
//       } else if (price >= 1000) {
//           return (price / 1000).toFixed(0) + 'K';
//       } else {
//           return price;
//       }
//   }

//   fetch('get_items.php')
//     .then(response => response.json())
//     .then(data => {
//       const container = document.getElementById('items-container'); // Misalnya ada container untuk menampung data
//       data.forEach(item => {
//         const itemElement = document.createElement('div');
        
//         const descriptionElement = document.createElement('p');
//         descriptionElement.className = 'helvetica text-gray-400 text-sm';
//         descriptionElement.textContent = `${item.description}`;
        
//         const priceElement = document.createElement('p');
//         priceElement.className = 'helvetica text-[#ff5b1c] font-bold';
//         priceElement.textContent = `IDR ${formatPrice(item.price)} / Person`;
        
//         itemElement.appendChild(descriptionElement);
//         itemElement.appendChild(priceElement);
        
//         container.appendChild(itemElement);
//       });
//     })
//     .catch(error => console.error('Error:', error));

    // Function to format the price
    function formatPrice(price) {
        if (price >= 1000000) {
            return (price / 1000000).toFixed(1) + 'M';
        } else if (price >= 1000) {
            return (price / 1000).toFixed(0) + 'K';
        } else {
            return price;
        }
    }

    // Function to calculate the correct price and description based on single/couple selection
    function getPriceAndDescriptionForSelection(item) {
        if (coupleRadio.checked) {
            // Use couple price and couple description if 'Couple' is selected
            return {
                price: item.couplePrice ? item.couplePrice : item.price,
                description: item.coupleDescription || item.description // Use couple description if available
            };
        } else {
            // Use single price and single description if 'Single' is selected
            return {
                price: item.price,
                description: item.description
            };
        }
    }   

    // Function to render items dynamically
    function renderItems(data) {
        const container = document.getElementById('items-container');
        container.innerHTML = ''; // Clear current items before re-rendering

        data.forEach(item => {
            const itemElement = document.createElement('div');

            const descriptionElement = document.createElement('p');
            descriptionElement.className = 'helvetica text-gray-400 text-sm';

            // Get the correct price and description based on the radio button selection
            const { price, description } = getPriceAndDescriptionForSelection(item);
            descriptionElement.textContent = `${description}`;

            // Create price element
            const priceElement = document.createElement('p');
            priceElement.className = 'helvetica text-[#ff5b1c] font-bold';
            priceElement.textContent = `IDR ${formatPrice(price)}`;

            itemElement.appendChild(descriptionElement);
            itemElement.appendChild(priceElement);

            container.appendChild(itemElement);
        });
    }

    // Fetch data and display items initially
    fetch('get_items.php')
    .then(response => response.json())
    .then(data => {
        // Initial rendering
        renderItems(data);

        // Event listeners to update prices and descriptions when radio button selection changes
        coupleRadio.addEventListener('change', function() {
            if (this.checked) {
                renderItems(data); // Re-render with updated prices and descriptions for "Couple"
            }
        });

        singleRadio.addEventListener('change', function() {
            if (this.checked) {
                renderItems(data); // Re-render with updated prices and descriptions for "Single"
            }
        });
    })
    .catch(error => console.error('Error:', error));



    function formatFileName(fileName) {
        const nameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.')) || fileName; // Menghapus ekstensi
        if (nameWithoutExt.length > 10) {
            return nameWithoutExt.substring(0, 10) + '...';
        }
        return nameWithoutExt; 
    }

    function formatFileSize(size) {
        if (size >= 1000000) {
            return (size / 1000000).toFixed(2) + ' MB';
        } else if (size >= 1000) {
            return (size / 1000).toFixed(0) + ' KB';
        } else {
            return size + ' bytes';
        }
    }

    const paymentInput = document.getElementById('paymentproof');
    const paymentName = document.getElementById('file-name');
    const paymentSize = document.getElementById('file-size');

    paymentInput.addEventListener('change', function () {
        const file = paymentInput.files[0];
        if (file) {
            const fileName = formatFileName(file.name);
            const fileSize = formatFileSize(file.size);
            
            paymentName.textContent = fileName;
            paymentSize.textContent = `${fileSize}`;
        } else {
            paymentName.textContent = ''; // Clear if no file selected
            paymentSize.textContent = ''; // Clear if no file selected
        }
    });

    //===========================================================

    function generateTransactionId() {
        return 'FR-LSM-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
    }

    const transactionIdInput = document.getElementById('transactionid');
    const generatedTransactionId = generateTransactionId();
    if (transactionIdInput) {
        transactionIdInput.value = generatedTransactionId;
    }

    const registrationForm = document.getElementById('registrationForm');
    console.log('Registration form found:', registrationForm);
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
        console.log('Form submitted!');
        event.preventDefault(); 

        const formData = new FormData();

        const registrationTypeElement = document.querySelector('input[name="registrationType"]:checked');
        const usernameElement = document.getElementById('name');
        const mantanElement = document.getElementById('mantan');
        const phoneElement = document.getElementById('phone');
        const emailElement = document.getElementById('email');
        const sizeElement = document.querySelector('input[name="size"]:checked');
        const jerseyColorElement = document.querySelector('input[name="jerseyColor"]:checked');

        if (!registrationTypeElement || !usernameElement || !mantanElement || !phoneElement || !emailElement || !sizeElement || !jerseyColorElement) {
            alert('Mohon lengkapi semua field yang diperlukan');
            return;
        }

        const registrationType = registrationTypeElement.value;
        const username = usernameElement.value;
        const mantan = mantanElement.value;
        const phone = phoneElement.value;
        const email = emailElement.value;
        const size = sizeElement.value;
        const jerseyColor = jerseyColorElement.value;

        formData.append('transactionid', generatedTransactionId);
        formData.append('registrationType', registrationType);

        formData.append('username', username);
        formData.append('mantan', mantan);
        formData.append('phone', phone);
        formData.append('email', email);
        formData.append('size', size);
        formData.append('jerseyColor', jerseyColor);

        if (registrationType === 'couple') {
            const coupleUsernameElement = document.getElementById('coupleName');
            const coupleMantanElement = document.getElementById('coupleMantan');
            const coupleSizeElement = document.querySelector('input[name="coupleSize"]:checked');
            const coupleJerseyColorElement = document.querySelector('input[name="coupleJerseyColor"]:checked');

            if (!coupleUsernameElement || !coupleMantanElement || !coupleSizeElement || !coupleJerseyColorElement) {
                alert('Mohon lengkapi semua field untuk pasangan');
                return;
            }

            const coupleUsername = coupleUsernameElement.value;
            const coupleMantan = coupleMantanElement.value;
            const coupleSize = coupleSizeElement.value;
            const coupleJerseyColor = coupleJerseyColorElement.value;
            formData.append('coupleUsername', coupleUsername);
            formData.append('coupleMantan', coupleMantan);
            formData.append('coupleSize', coupleSize);
            formData.append('coupleJerseyColor', coupleJerseyColor);
        }

        fetch('register.php', {
            method: 'POST',
            body: formData 
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); 
            if (data.status === 'success') {
                localStorage.setItem('registrationSuccess', 'true');
                localStorage.setItem('transactionid', generatedTransactionId);

                if (box) box.classList.remove("expanded");
                if (registerBtn) registerBtn.classList.remove("hidden");
                setTimeout(function() {
                    if (signupContainer) signupContainer.classList.add("hidden");
                }, 200);
                
                const transactionIdFromLocalStorage = localStorage.getItem('transactionid');

                const successModal = document.getElementById('successModal');
                if (successModal) successModal.classList.remove('hidden');

                // const transactionIdDisplay = document.getElementById('transaction-id');
                fetch(`get_transaction.php?transaction_id=${transactionIdFromLocalStorage}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Transaction Details:', data);

                    const transactionDetailsContainer = document.getElementById('transaction-details-container');
                    if (data.error) {
                        if (transactionDetailsContainer) {
                            transactionDetailsContainer.innerHTML = `<p class="text-center text-red-500">${data.error}</p>`;
                        }
                    } else {
                        const totalAmount = parseFloat(data[0].total_amount); 
                        const transactionDate = data[0].transaction_date;

                        const formattedDate = new Date(transactionDate.replace(' ', 'T')).toLocaleString();  

                        const formattedAmount = 'Rp' + new Intl.NumberFormat('id-ID', {
                            maximumFractionDigits: 0 
                        }).format(totalAmount);

                        if (transactionDetailsContainer) {
                            transactionDetailsContainer.innerHTML = `
                                <ul>
                                    <li class="flex justify-between">
                                        <span><strong>Transaction ID</strong></span>
                                        <span class="text-rigth">${transactionIdFromLocalStorage}</span>
                                    </li>
                                    <li class="flex justify-between">
                                        <span><strong>Total Amount</strong></span>
                                        <span class="text-rigth"><strong>${formattedAmount}</strong></span>
                                    </li>
                                    <li class="flex justify-between">
                                        <span><strong>Transaction Date</strong></span>
                                        <span class="text-right">${formattedDate}</span>
                                    </li>
                                </ul>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching transaction details:', error);
                    const transactionDetailsContainer = document.getElementById('transaction-details-container');
                    if (transactionDetailsContainer) {
                        transactionDetailsContainer.innerHTML = `<p class="text-center text-red-500">Terjadi kesalahan saat mengambil detail transaksi.</p>`;
                    }
                });

                registrationForm.reset();
            } else {
                    alert('Terjadi kesalahan, coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim data.');
            });
        });
    }
    //===========================================================

    window.addEventListener('load', function() {
        if (localStorage.getItem('registrationSuccess') === 'true') {
            const successModal = document.getElementById('successModal');
            successModal.classList.remove('hidden');
            const transactionIdFromLocalStorage = localStorage.getItem('transactionid');

        fetch(`get_transaction.php?transaction_id=${transactionIdFromLocalStorage}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Transaction Details:', data);

                const transactionDetailsContainer = document.getElementById('transaction-details-container');
                if (data.error) {
                    transactionDetailsContainer.innerHTML = `<p class="text-center text-red-500">${data.error}</p>`;
                } else {
                    const totalAmount = parseFloat(data[0].total_amount);
                    const transactionDate = data[0].transaction_date;

                    const formattedDate = new Date(transactionDate.replace(' ', 'T')).toLocaleString(); 

                    const formattedAmount = 'Rp' + new Intl.NumberFormat('id-ID', {
                      maximumFractionDigits: 0 
                    }).format(totalAmount);

                    transactionDetailsContainer.innerHTML = `
                      <ul>
                          <li class="flex justify-between">
                              <span><strong>Transaction ID</strong></span>
                              <span class="text-rigth">${transactionIdFromLocalStorage}</span>
                          </li>
                          <li class="flex justify-between">
                              <span><strong>Total Amount</strong></span>
                              <span class="text-rigth"><strong>${formattedAmount}</strong></span>
                          </li>
                          <li class="flex justify-between">
                              <span><strong>Transaction Date</strong></span>
                              <span class="text-right">${formattedDate}</span>
                          </li>
                      </ul>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching transaction details:', error);
                const transactionDetailsContainer = document.getElementById('transaction-details-container');
                transactionDetailsContainer.innerHTML = `<p class="text-center text-red-500">Terjadi kesalahan saat mengambil detail transaksi.</p>`;
            });
          }
        });

        const uploadButton = document.getElementById("uploadButton");
        if (uploadButton) {
            uploadButton.addEventListener("click", function() {
            const fileInput = document.getElementById("paymentproof");
            const file = fileInput ? fileInput.files[0] : null;

            const transactionId = localStorage.getItem('transactionid');
            if (!transactionId) {
                alert("Transaction ID tidak ditemukan di localStorage.");
                return;
            }

            if (!file) {
                alert("Silakan pilih file untuk diupload.");
                return;
            }

            const formData = new FormData();
            formData.append("paymentproof", file);

            fetch(`prooft_payment.php?transaction_id=${transactionId}`, {
                method: "POST",
                body: formData,
            })
            .then(response => {
                console.log('Response:', response);
                return response.text();
            })
            .then(data => {
                try {
                    const jsonData = JSON.parse(data);
                    if (jsonData.success) {
                        const successModal = document.getElementById('successModal');
                        if (successModal) successModal.classList.add('hidden');

                        Swal.fire({
                            icon: 'success',
                            title: 'Thank you..',
                            text: 'Bukti pembayaran telah kami terima, silakan menunggu konfirmasi',
                            timer: 60000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'rounded-[24px] text-left text-gray-700 p-6 shadow-lg',
                                title: 'text-lg font-semibold',
                                content: 'text-sm text-gray-500',
                            },
                            willClose: () => {
                                localStorage.removeItem('transactionid');
                                localStorage.removeItem('registrationSuccess');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: jsonData.error || "Terjadi kesalahan saat mengupload file.",
                            customClass: {
                                popup: 'rounded-[24px] text-left text-gray-700 p-6 shadow-lg',
                                title: 'text-lg font-semibold',
                                content: 'text-sm text-gray-500',
                            }
                        });
                    }
                } catch (error) {
                    console.error("Error parsing JSON:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "Terjadi kesalahan saat mengupload file.",
                        customClass: {
                                popup: 'rounded-[24px] text-left text-gray-700 p-6 shadow-lg',
                                title: 'text-lg font-semibold',
                                content: 'text-sm text-gray-500',
                            }
                        });
                    }
                });
            });
        });
    }

    // Harga dasar
    const BASE_PRICE_SINGLE = 100000;
    const BASE_PRICE_COUPLE = 200000;
    const EXTRA_SIZE = ['3xl', '4xl', '5xl'];
    const EXTRA_PRICE = 10000;

    // Helper untuk cek apakah size termasuk extra
    function isExtraSize(size) {
        return EXTRA_SIZE.includes(size.toLowerCase());
    }

    // Update harga di form utama
    function updateMainFormPrice() {
        let price = BASE_PRICE_SINGLE;
        let size = document.querySelector('input[name="size"]:checked');
        if (coupleRadio && coupleRadio.checked) {
            price = BASE_PRICE_COUPLE;
            let size1 = document.querySelector('input[name="size"]:checked');
            let size2 = document.querySelector('input[name="coupleSize"]:checked');
            let extra = 0;
            if (size1 && isExtraSize(size1.value)) extra += EXTRA_PRICE;
            if (size2 && isExtraSize(size2.value)) extra += EXTRA_PRICE;
            price += extra;
        } else {
            if (size && isExtraSize(size.value)) price += EXTRA_PRICE;
        }
        // Update tampilan harga
        let priceDisplay = document.querySelector('.bg-gray-50 .font-bold');
        if (priceDisplay) {
            priceDisplay.textContent = 'Rp ' + price.toLocaleString('id-ID');
        }
    }

    // Event listener untuk update harga saat size berubah
    document.querySelectorAll('input[name="size"]').forEach(el => {
        if (el) el.addEventListener('change', updateMainFormPrice);
    });
    document.querySelectorAll('input[name="coupleSize"]').forEach(el => {
        if (el) el.addEventListener('change', updateMainFormPrice);
    });
    if (coupleRadio) coupleRadio.addEventListener('change', updateMainFormPrice);
    if (singleRadio) singleRadio.addEventListener('change', updateMainFormPrice);
    // Panggil sekali saat load
    updateMainFormPrice();

    // Update harga di modal order jersey
    function updateOrderModalPrice() {
        let price = BASE_PRICE_SINGLE;
        let size = document.querySelector('input[name="orderJerseySize"]:checked');
        if (size && isExtraSize(size.value)) price += EXTRA_PRICE;
        let priceDisplay = document.querySelector('#jerseyOrderModal .font-bold');
        if (priceDisplay) {
            priceDisplay.textContent = 'Rp ' + price.toLocaleString('id-ID');
        }
    }
    
    // Event listener untuk order modal price
    document.querySelectorAll('input[name="orderJerseySize"]').forEach(el => {
        if (el) el.addEventListener('change', updateOrderModalPrice);
    });
    
    // Panggil sekali saat modal dibuka
    const jerseyOrderModal = document.getElementById('jerseyOrderModal');
    if (jerseyOrderModal) {
        jerseyOrderModal.addEventListener('click', function(e) {
            if (e.target.id === 'jerseyOrderModal') updateOrderModalPrice();
        });
    }
});