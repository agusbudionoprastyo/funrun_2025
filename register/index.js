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
  const xxxlcouple = document.getElementById('xxxlcouple');

  const registerBtn = document.getElementById("registerBtn");
  const closeBtn = document.getElementById("closeBtn");
  const box = document.getElementById("box");
  const signupContainer = document.getElementById("signup-container");

  // Hide the Couple Section initially and remove required attributes
  if (coupleContainer.classList.contains('hidden')) {
    coupleName.removeAttribute('required');
    coupleMantan.removeAttribute('required');
    scouple.removeAttribute('required');
    mcouple.removeAttribute('required');
    lcouple.removeAttribute('required');
    xlcouple.removeAttribute('required');
    xxlcouple.removeAttribute('required');
    xxxlcouple.removeAttribute('required');
  }

  // Show Couple Section and add required attributes when "Couple" radio is checked
  coupleRadio.addEventListener('change', function () {
    if (this.checked) {
      coupleContainer.classList.remove('hidden');
      coupleName.setAttribute('required', 'true');
      coupleMantan.setAttribute('required', 'true');
      scouple.setAttribute('required', 'true');
      mcouple.setAttribute('required', 'true');
      lcouple.setAttribute('required', 'true');
      xlcouple.setAttribute('required', 'true');
      xxlcouple.setAttribute('required', 'true');
      xxxlcouple.setAttribute('required', 'true');
    }
  });

  // Hide Couple Section and remove required attributes when "Single" radio is checked
  singleRadio.addEventListener('change', function () {
    if (this.checked) {
      coupleContainer.classList.add('hidden');
      coupleName.removeAttribute('required');
      coupleMantan.removeAttribute('required');
      scouple.removeAttribute('required');
      mcouple.removeAttribute('required');
      lcouple.removeAttribute('required');
      xlcouple.removeAttribute('required');
      xxlcouple.removeAttribute('required');
      xxxlcouple.removeAttribute('required');
    }
  });

  registerBtn.addEventListener("click", function() {
      box.classList.add("expanded");
      registerBtn.classList.add("hidden");
      setTimeout(function() {
        signupContainer.classList.remove("hidden");
      }, 200);
  });

  closeBtn.addEventListener("click", function() {
      box.classList.remove("expanded");
      registerBtn.classList.remove("hidden");
      setTimeout(function() {
        signupContainer.classList.add("hidden");
      }, 200);
  });

  function formatPrice(price) {
      if (price >= 1000000) {
          return (price / 1000000).toFixed(1) + 'M';
      } else if (price >= 1000) {
          return (price / 1000).toFixed(0) + 'K';
      } else {
          return price;
      }
  }

  fetch('get_items.php')
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById('items-container'); // Misalnya ada container untuk menampung data
      data.forEach(item => {
        const itemElement = document.createElement('div');
        
        const descriptionElement = document.createElement('p');
        descriptionElement.className = 'helvetica text-gray-400 text-sm';
        descriptionElement.textContent = `${item.description}`;
        
        const priceElement = document.createElement('p');
        priceElement.className = 'helvetica text-[#ff005b] font-bold';
        priceElement.textContent = `IDR ${formatPrice(item.price)} / Person`;
        
        itemElement.appendChild(descriptionElement);
        itemElement.appendChild(priceElement);
        
        container.appendChild(itemElement);
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
        return 'TPE-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
    }

    const transactionIdInput = document.getElementById('transactionid');
    const generatedTransactionId = generateTransactionId();
    transactionIdInput.value = generatedTransactionId;

    registrationForm.addEventListener('submit', function(event) {
    event.preventDefault(); 

    const formData = new FormData();

    const registrationType = document.querySelector('input[name="registrationType"]:checked').value;
    const username = document.getElementById('name').value;
    const mantan = document.getElementById('mantan').value;
    const phone = document.getElementById('phone').value;
    const email = document.getElementById('email').value;
    const size = document.querySelector('input[name="size"]:checked').value;

    formData.append('transactionid', generatedTransactionId);
    formData.append('registrationType', registrationType);
    formData.append('username', username);
    formData.append('mantan', mantan);
    formData.append('phone', phone);
    formData.append('email', email);
    formData.append('size', size);

    if (registrationType === 'couple') {
        const coupleUsername = document.getElementById('coupleName').value;
        const coupleMantan = document.getElementById('coupleMantan').value;
        const coupleSize = document.querySelector('input[name="coupleSize"]:checked').value;
        formData.append('coupleUsername', coupleUsername);
        formData.append('coupleMantan', coupleMantan);
        formData.append('username', username);
        formData.append('mantan', mantan);
        formData.append('phone', phone);
        formData.append('email', email);
        formData.append('coupleSize', coupleSize);
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

            box.classList.remove("expanded");
            registerBtn.classList.remove("hidden");
            setTimeout(function() {
                signupContainer.classList.add("hidden");
            }, 200);
            
            const transactionIdFromLocalStorage = localStorage.getItem('transactionid');

            const successModal = document.getElementById('successModal');
            successModal.classList.remove('hidden');

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

        document.getElementById("uploadButton").addEventListener("click", function() {
        const fileInput = document.getElementById("paymentproof");
        const file = fileInput.files[0];

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
                    successModal.classList.add('hidden');

                    Swal.fire({
                        icon: 'success',
                        title: 'Thank you for being a part of the third-party effect.',
                        text: 'Your payment proof has been received. Please wait for confirmation.',
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