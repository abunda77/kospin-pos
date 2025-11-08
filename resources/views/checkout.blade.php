@extends('layouts.app')

@section('content')
<div class="container py-8 mx-auto">
    <div class="p-6 mx-auto max-w-4xl bg-white rounded-lg shadow-md">
        <h1 class="mb-6 text-2xl font-bold">Checkout</h1>

        @if ($errors->any())
            <div class="px-4 py-3 mb-4 text-red-700 bg-red-100 rounded border border-red-400" role="alert">
                <strong class="font-bold">Terjadi kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="px-4 py-3 mb-4 text-red-700 bg-red-100 rounded border border-red-400">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Informasi Pelanggan -->
            <div>
                <h2 class="mb-4 text-lg font-semibold">Informasi Pelanggan</h2>

                <!-- Pilihan Tipe Customer -->
                <div class="mb-4 md:mb-6">
                    <div class="flex space-x-3 md:space-x-4">
                        <button type="button"
                                id="btn-non-member"
                                class="px-3 py-2 text-sm text-white bg-green-500 rounded md:px-4 md:text-base customer-type-btn">
                            Non Anggota
                        </button>
                        <button type="button"
                                id="btn-member"
                                class="px-3 py-2 text-sm rounded border border-gray-300 md:px-4 md:text-base customer-type-btn">
                            Anggota
                        </button>
                    </div>
                </div>

                <!-- Form Cek NIK Anggota -->
                <div id="member-check-form" style="display: none;" class="mb-4 md:mb-6">
                    <div class="flex space-x-2">
                        <input type="text"
                               id="check-nik"
                               class="flex-1 px-3 py-2 text-sm rounded border md:text-base"
                               placeholder="Masukkan NIK/No.WA">
                        <button type="button"
                                onclick="checkMember()"
                                class="px-3 py-2 text-sm text-white bg-blue-500 rounded md:px-4 md:text-base">
                            Cek
                        </button>
                    </div>
                    <div id="notification" class="hidden p-3 mt-3 text-sm rounded-lg md:text-base"></div>
                </div>

                <form action="{{ route('checkout.process-payment') }}" method="POST" id="payment-form">
                    @csrf
                    <input type="hidden" name="is_member" id="is_member_input" value="0">
                    <!-- Form Non Anggota -->
                    <div id="non-member-form">
                        <div class="mb-4">
                            <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="px-3 py-2 w-full rounded-md border border-gray-300" required>
                        </div>
                        <div class="mb-4">
                            <label for="whatsapp" class="block mb-1 text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="px-3 py-2 w-full rounded-md border border-gray-300" required>
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block mb-1 text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="address" id="address" rows="3" class="px-3 py-2 w-full rounded-md border border-gray-300" required></textarea>
                        </div>
                    </div>

                    <!-- Form Anggota -->
                    <div id="member-form" style="display: none;">
                        <input type="hidden" name="member_id" id="member_id">

                        <div class="mb-4">
                            <label class="block mb-1 text-sm font-medium text-gray-700">NIK / No. Whatsapp</label>
                            <input type="text" name="nik" id="member-nik" class="px-3 py-2 w-full rounded-md border border-gray-300" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" id="member-name" class="px-3 py-2 w-full rounded-md border border-gray-300" readonly>
                        </div>
                        <div class="mb-4">
                            <label for="whatsapp" class="block mb-1 text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp" id="member-whatsapp" class="px-3 py-2 w-full rounded-md border border-gray-300" required>
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block mb-1 text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="address" id="member-address" rows="3" class="px-3 py-2 w-full rounded-md border border-gray-300" required></textarea>
                        </div>
                    </div>
            </div>

            <!-- Ringkasan Pesanan -->
            <div>
                <h2 class="mb-4 text-lg font-semibold">Ringkasan Pesanan</h2>
                <div class="p-4 mb-4 rounded-md border border-gray-200">
                    @php
                        $cart = session()->get('cart', []);
                        $subtotal = collect($cart)->sum(function ($item) {
                            return $item['quantity'] * $item['unit_price'];
                        });
                        $voucher = session()->get('voucher');
                        $discount = $voucher ? $voucher['discount'] : 0;
                        $total = $subtotal - $discount;
                    @endphp

                    <div class="mb-4 space-y-2">
                        @foreach($cart as $id => $item)
                            <div class="flex justify-between">
                                <span>{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                                <span>Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-2 mb-2 border-t border-gray-200">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 font-semibold border-t border-gray-200">
                        <div class="flex justify-between">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <h2 class="mb-4 text-lg font-semibold">Metode Pembayaran</h2>
                <div class="mb-4">
                    <div class="grid grid-cols-1 gap-3">
                        @php
                            $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->get();
                            $bgColors = ['bg-gray-50', 'bg-blue-50', 'bg-green-50', 'bg-yellow-50', 'bg-red-50', 'bg-indigo-50'];
                        @endphp

                        @foreach($paymentMethods as $method)
                            <div class="p-3 rounded-md border border-gray-200 cursor-pointer hover:border-blue-500 payment-method-option {{ $bgColors[$loop->index % count($bgColors)] }}" 
                                 data-id="{{ $method->id }}" 
                                 data-gateway="{{ $method->gateway }}"
                                 data-name="{{ strtolower($method->name) }}">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="payment_method_id" value="{{ $method->id }}" class="mr-2" required>
                                    <div class="flex items-center">
                                        @if($method->image)
                                            <img src="{{ $method->image_url }}" alt="{{ $method->name }}" class="mr-2 w-auto h-8">
                                        @endif
                                        <span>{{ $method->name }}</span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- QRIS Dynamic Display -->
                <div id="qris-display" class="hidden p-4 mt-4 rounded-md border border-blue-200 bg-blue-50">
                    <h3 class="mb-3 font-medium text-center">Scan QRIS untuk Pembayaran</h3>
                    <div class="flex flex-col items-center">
                        <div id="qris-loading" class="flex flex-col items-center">
                            <svg class="w-12 h-12 mb-2 animate-spin text-blue-600" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Generating QRIS...</p>
                        </div>
                        <div id="qris-content" class="hidden flex flex-col items-center">
                            <img id="qris-image" src="" alt="QRIS Code" class="mb-3 w-64 h-64 border-2 border-gray-300 rounded">
                            <p class="mb-2 text-lg font-semibold">Total: Rp <span id="qris-amount">{{ number_format($total, 0, ',', '.') }}</span></p>
                            <p class="text-sm text-gray-600">Scan QR code dengan aplikasi pembayaran Anda</p>
                        </div>
                        <div id="qris-error" class="hidden text-center text-red-600">
                            <p class="mb-2">Gagal generate QRIS</p>
                            <button type="button" onclick="retryQrisGeneration()" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="qris_dynamic_id" id="qris_dynamic_id">
                </div>

                <!-- Form Pembayaran Dinamis -->
                <div id="payment-details" class="hidden p-4 mt-4 rounded-md border border-gray-200">
                    <!-- Form untuk Kartu Kredit -->
                    <div id="credit-card-form" class="hidden payment-type-form">
                        <h3 class="mb-3 font-medium">Detail Kartu Kredit</h3>
                        <div class="mb-3">
                            <label for="card_number" class="block mb-1 text-sm font-medium text-gray-700">Nomor Kartu</label>
                            <input type="text" name="card_number" id="card_number" class="px-3 py-2 w-full rounded-md border border-gray-300" placeholder="4111 1111 1111 1111">
                        </div>

                        <div class="grid grid-cols-3 gap-3 mb-3">
                            <div>
                                <label for="card_exp_month" class="block mb-1 text-sm font-medium text-gray-700">Bulan</label>
                                <select name="card_exp_month" id="card_exp_month" class="px-3 py-2 w-full rounded-md border border-gray-300">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label for="card_exp_year" class="block mb-1 text-sm font-medium text-gray-700">Tahun</label>
                                <select name="card_exp_year" id="card_exp_year" class="px-3 py-2 w-full rounded-md border border-gray-300">
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label for="card_cvv" class="block mb-1 text-sm font-medium text-gray-700">CVV</label>
                                <input type="text" name="card_cvv" id="card_cvv" class="px-3 py-2 w-full rounded-md border border-gray-300" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <!-- Form untuk Bank Transfer -->
                    <div id="bank-transfer-form" class="hidden payment-type-form">
                        <h3 class="mb-3 font-medium">Bank Transfer</h3>
                        <div class="mb-3">
                            <label for="bank" class="block mb-1 text-sm font-medium text-gray-700">Pilih Bank</label>
                            <select name="bank" id="bank" class="px-3 py-2 w-full rounded-md border border-gray-300">
                                <option value="bca">BCA</option>
                                <option value="bni">BNI</option>
                                <option value="bri">BRI</option>
                            </select>
                        </div>
                    </div>

                    <!-- Form untuk GoPay -->
                    <div id="gopay-form" class="hidden payment-type-form">
                        <h3 class="mb-3 font-medium">GoPay</h3>
                        <p class="text-sm text-gray-600">Anda akan diarahkan ke halaman GoPay untuk menyelesaikan pembayaran.</p>
                    </div>
                </div>

                <button type="submit"
                        class="px-4 py-2 mt-4 w-full font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 touch-manipulation">
                    <span id="btn-text">Bayar Sekarang</span>
                    <span id="btn-loading" style="display: none;" class="flex justify-center items-center">
                        <svg class="inline mr-2 w-4 h-4 animate-spin" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses Pembayaran...
                    </span>
                </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fungsi untuk mengaktifkan/menonaktifkan input form, dideklarasikan secara global
    function toggleFormState(form, isActive) {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            // Khusus untuk input NIK/No.WA di form member, jangan disable jika read-only
            if (input.id === 'member-nik' && input.hasAttribute('readonly')) {
                return;
            }
            input.disabled = !isActive;
        });
    }

    let nonMemberForm, memberForm; // Declare globally

    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodOptions = document.querySelectorAll('.payment-method-option');
        const paymentDetails = document.getElementById('payment-details');
        const paymentTypeForms = document.querySelectorAll('.payment-type-form');

        // Member / Non-member toggle
        const btnNonMember = document.getElementById('btn-non-member');
        const btnMember = document.getElementById('btn-member');
        const memberCheckForm = document.getElementById('member-check-form');
        nonMemberForm = document.getElementById('non-member-form');
        memberForm = document.getElementById('member-form');

        // Initial state: Non-member form is active
        toggleFormState(nonMemberForm, true);
        toggleFormState(memberForm, false);
        // Pastikan input cek NIK dinonaktifkan pada awalnya
        document.getElementById('check-nik').disabled = true;

        btnNonMember.addEventListener('click', function() {
            btnNonMember.classList.add('bg-green-500', 'text-white');
            btnNonMember.classList.remove('border-gray-300');
            btnMember.classList.remove('bg-green-500', 'text-white');
            btnMember.classList.add('border-gray-300');

            memberCheckForm.style.display = 'none';
            memberForm.style.display = 'none';
            nonMemberForm.style.display = 'block';

            // Activate non-member form, deactivate member form
            toggleFormState(nonMemberForm, true);
            toggleFormState(memberForm, false);
            document.getElementById('check-nik').disabled = true;

            document.getElementById('is_member_input').value = '0';
        });

        btnMember.addEventListener('click', function() {
            btnMember.classList.add('bg-green-500', 'text-white');
            btnMember.classList.remove('border-gray-300');
            btnNonMember.classList.remove('bg-green-500', 'text-white');
            btnNonMember.classList.add('border-gray-300');

            memberCheckForm.style.display = 'block';
            nonMemberForm.style.display = 'none';

            // Deactivate non-member form. Member form is still inactive until check.
            toggleFormState(nonMemberForm, false);
            toggleFormState(memberForm, false); // Keep member form disabled
            document.getElementById('check-nik').disabled = false;

            document.getElementById('is_member_input').value = '1';
        });

        // Credit Card form elements
        const creditCardForm = document.getElementById('credit-card-form');

        // Bank Transfer form elements
        const bankTransferForm = document.getElementById('bank-transfer-form');

        // GoPay form elements
        const gopayForm = document.getElementById('gopay-form');

        // Fungsi untuk menampilkan form pembayaran yang sesuai
        function showPaymentForm(gateway, methodName) {
            console.log('Gateway:', gateway, 'Method:', methodName);

            // Hide all payment type forms first
            paymentTypeForms.forEach(form => {
                form.classList.add('hidden');
            });

            if (gateway === 'midtrans') {
                paymentDetails.classList.remove('hidden');

                // Default to bank transfer if no specific method is detected
                bankTransferForm.classList.remove('hidden');

                // Tambahkan opsi untuk memilih jenis pembayaran Midtrans
                const paymentTypeSelector = document.createElement('div');
                paymentTypeSelector.className = 'mb-4';
                paymentTypeSelector.innerHTML = `
                    <label class="block mb-2 text-sm font-medium text-gray-700">Pilih Jenis Pembayaran</label>
                    <div class="grid grid-cols-1 gap-2">
                        <label class="flex items-center p-2 rounded border cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_type" value="bank_transfer" checked class="mr-2">
                            <span>Bank Transfer</span>
                        </label>
                    </div>
                `;

                // Tambahkan selector ke payment details
                if (!document.getElementById('payment-type-selector')) {
                    paymentDetails.insertBefore(paymentTypeSelector, paymentDetails.firstChild);
                    paymentTypeSelector.id = 'payment-type-selector';

                    // Tambahkan event listener untuk radio buttons
                    const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
                    paymentTypeRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            // Hide all payment type forms
                            paymentTypeForms.forEach(form => {
                                form.classList.add('hidden');
                            });

                            // Show selected payment type form
                            if (this.value === 'credit_card') {
                                creditCardForm.classList.remove('hidden');
                            } else if (this.value === 'bank_transfer') {
                                bankTransferForm.classList.remove('hidden');
                            } else if (this.value === 'gopay') {
                                gopayForm.classList.remove('hidden');
                            }
                        });
                    });

                    // Pastikan formulir default tampil (Bank Transfer)
                    document.querySelector('input[name="payment_type"][value="bank_transfer"]').checked = true;
                    bankTransferForm.classList.remove('hidden');
                }
            } else {
                // For non-gateway payment methods (e.g., cash)
                paymentDetails.classList.add('hidden');

                // Remove payment type selector if exists
                const selector = document.getElementById('payment-type-selector');
                if (selector) {
                    selector.remove();
                }
            }
        }

        // Add click event to payment method options
        paymentMethodOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Select the radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;

                // Highlight selected option
                paymentMethodOptions.forEach(opt => {
                    opt.classList.remove('border-blue-500');
                    opt.classList.add('border-gray-200');
                });
                this.classList.remove('border-gray-200');
                this.classList.add('border-blue-500');

                // Show payment details based on gateway
                const gateway = this.dataset.gateway;
                const methodName = this.querySelector('span').textContent.trim();
                const methodNameLower = this.dataset.name;

                // Check if QRIS payment method
                if (methodNameLower && methodNameLower.includes('qris')) {
                    showQrisPayment();
                } else {
                    hideQrisPayment();
                    showPaymentForm(gateway, methodName);
                }
            });
        });

        // Format credit card number with spaces
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 16) value = value.slice(0, 16);

                // Add spaces every 4 digits
                let formattedValue = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formattedValue += ' ';
                    }
                    formattedValue += value[i];
                }

                e.target.value = formattedValue;
            });
        }

        // Limit CVV to 3-4 digits
        const cardCvvInput = document.getElementById('card_cvv');
        if (cardCvvInput) {
            cardCvvInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 4) value = value.slice(0, 4);
                e.target.value = value;
            });
        }

        // Fix form submission
        const paymentForm = document.getElementById('payment-form');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                console.log('Validating form before submission...');

                // Validasi form sebelum submit
                const activeForm = nonMemberForm.style.display === 'block' ? nonMemberForm : memberForm;
                const requiredFields = activeForm.querySelectorAll('input[required], textarea[required]');

                let isValid = true;
                requiredFields.forEach(field => {
                    // Hanya validasi field yang terlihat (tidak di dalam parent yang display: none)
                    if (field.offsetParent !== null && !field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500');
                        console.log('Validation failed for visible field:', field.name);
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                // Validasi metode pembayaran
                const paymentMethodSelected = document.querySelector('input[name="payment_method_id"]:checked');
                if (!paymentMethodSelected) {
                    isValid = false;
                    alert('Silakan pilih metode pembayaran');
                }

                if (!isValid) {
                    console.log('Form is invalid. Preventing submission.');
                    e.preventDefault(); // Prevent submission ONLY if invalid
                } else {
                    console.log('Form is valid. Showing loading state and submitting.');
                    // Show loading state
                    document.getElementById('btn-text').style.display = 'none';
                    document.getElementById('btn-loading').style.display = 'flex';
                    // Allow the form to submit naturally
                }
            });
        }
    });

    // Fungsi untuk mengecek member berdasarkan NIK
    function checkMember() {
        const nik = document.getElementById('check-nik').value;
        const notification = document.getElementById('notification');

        if (!nik) {
            notification.textContent = 'Silahkan masukkan NIK/No.WA terlebih dahulu';
            notification.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'border-green-400');
            notification.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');
            return;
        }

        // Tampilkan loading
        notification.textContent = 'Sedang memeriksa data...';
        notification.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
        notification.classList.add('bg-blue-100', 'text-blue-700', 'border', 'border-blue-400');

        // Kirim request ke endpoint check-member dengan URL yang benar
        fetch(`/check-member/${nik}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    // Member ditemukan
                    notification.textContent = 'Anggota ditemukan! Data telah diisi otomatis.';
                    notification.classList.remove('bg-red-100', 'text-red-700', 'bg-blue-100', 'text-blue-700');
                    notification.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-400');

                    // Isi form member dengan data dari server
                    document.getElementById('member-nik').value = data.member.nik || nik;
                    document.getElementById('member-name').value = data.member.nama_lengkap || '';
                    document.getElementById('member-whatsapp').value = data.member.no_hp || '';
                    document.getElementById('member-address').value = data.member.alamat || '';
                    document.getElementById('member_id').value = data.member.id || '';

                    // Tampilkan form member
                    document.getElementById('member-form').style.display = 'block';
                    toggleFormState(memberForm, true);
                } else {
                    // Member tidak ditemukan
                    notification.textContent = 'Anggota tidak ditemukan. Silahkan daftar sebagai non-anggota.';
                    notification.classList.remove('bg-green-100', 'text-green-700', 'bg-blue-100', 'text-blue-700');
                    notification.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');

                    // Sembunyikan form member
                    document.getElementById('member-form').style.display = 'none';
                    toggleFormState(memberForm, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                notification.textContent = 'Terjadi kesalahan saat memeriksa data. Silahkan coba lagi.';
                notification.classList.remove('bg-green-100', 'text-green-700', 'bg-blue-100', 'text-blue-700');
                notification.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');

                // Sembunyikan dan nonaktifkan form anggota jika terjadi kesalahan
                if (memberForm) {
                    memberForm.style.display = 'none';
                    toggleFormState(memberForm, false);
                }
            });
    }

    // QRIS Payment Functions
    function showQrisPayment() {
        // Hide other payment forms
        const paymentDetails = document.getElementById('payment-details');
        if (paymentDetails) {
            paymentDetails.classList.add('hidden');
        }

        // Show QRIS display
        const qrisDisplay = document.getElementById('qris-display');
        qrisDisplay.classList.remove('hidden');

        // Generate QRIS
        generateQrisDynamic();
    }

    function hideQrisPayment() {
        const qrisDisplay = document.getElementById('qris-display');
        if (qrisDisplay) {
            qrisDisplay.classList.add('hidden');
        }
    }

    function generateQrisDynamic() {
        const qrisLoading = document.getElementById('qris-loading');
        const qrisContent = document.getElementById('qris-content');
        const qrisError = document.getElementById('qris-error');

        // Show loading
        qrisLoading.classList.remove('hidden');
        qrisContent.classList.add('hidden');
        qrisError.classList.add('hidden');

        // Get total amount from page
        const totalAmount = {{ $total }};

        // Send request to generate QRIS
        fetch('/checkout/generate-qris', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                amount: totalAmount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide loading, show content
                qrisLoading.classList.add('hidden');
                qrisContent.classList.remove('hidden');

                // Set QR image
                document.getElementById('qris-image').src = data.qr_image_url;
                document.getElementById('qris-amount').textContent = data.amount_formatted;
                document.getElementById('qris_dynamic_id').value = data.qris_dynamic_id;
            } else {
                throw new Error(data.message || 'Failed to generate QRIS');
            }
        })
        .catch(error => {
            console.error('QRIS Generation Error:', error);
            qrisLoading.classList.add('hidden');
            qrisError.classList.remove('hidden');
        });
    }

    function retryQrisGeneration() {
        generateQrisDynamic();
    }
</script>
@endpush
@endsection









