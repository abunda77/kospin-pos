@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 md:py-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-6 md:mb-8">Checkout</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Form Pengiriman</h2>

            <!-- Pilihan Tipe Customer -->
            <div class="mb-4 md:mb-6">
                <div class="flex space-x-3 md:space-x-4">
                    <button type="button"
                            id="btn-non-member"
                            class="px-3 md:px-4 py-2 text-sm md:text-base rounded customer-type-btn bg-green-500 text-white">
                        Non Anggota
                    </button>
                    <button type="button"
                            id="btn-member"
                            class="px-3 md:px-4 py-2 text-sm md:text-base rounded customer-type-btn border border-gray-300">
                        Anggota
                    </button>
                </div>
            </div>

            <!-- Form Cek NIK Anggota -->
            <div id="member-check-form" style="display: none;" class="mb-4 md:mb-6">
                <div class="flex space-x-2">
                    <input type="text"
                           id="check-nik"
                           class="flex-1 border rounded px-3 py-2 text-sm md:text-base"
                           placeholder="Masukkan NIK/No.WA">
                    <button type="button"
                            onclick="checkMember()"
                            class="bg-blue-500 text-white px-3 md:px-4 py-2 rounded text-sm md:text-base">
                        Cek NIK / No.WA
                    </button>
                </div>
                <div id="notification" class="mt-3 p-3 rounded-lg text-sm md:text-base hidden"></div>
            </div>

            <!-- Form Non Anggota -->
            <form id="non-member-form" action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method_id" value="{{ $paymentMethod->id }}">
                <input type="hidden" name="is_member" value="0">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm md:text-base mb-2">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2 text-sm md:text-base" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm md:text-base mb-2">No. WhatsApp</label>
                    <input type="text" name="whatsapp" class="w-full border rounded px-3 py-2 text-sm md:text-base" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm md:text-base mb-2">Alamat Lengkap</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2 text-sm md:text-base" rows="3" required></textarea>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 md:py-3 px-4 rounded hover:bg-green-600 text-sm md:text-base">
                    Proses Pesanan
                </button>
            </form>

            <!-- Form Anggota -->
            <form id="member-form" action="{{ route('checkout.process') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="payment_method_id" value="{{ $paymentMethod->id }}">
                <input type="hidden" name="is_member" value="1">
                <input type="hidden" name="member_id" id="member_id">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm md:text-base mb-2">NIK / No. Whatsapp</label>
                    <input type="text" name="nik" id="member-nik" class="w-full border rounded px-3 py-2 text-sm md:text-base" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm md:text-base mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="member-name" class="w-full border rounded px-3 py-2 text-sm md:text-base">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm md:text-base mb-2">No. WhatsApp</label>
                    <input type="text" name="whatsapp" class="w-full border rounded px-3 py-2 text-sm md:text-base" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm md:text-base mb-2">Alamat Lengkap</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2 text-sm md:text-base" rows="3" required></textarea>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 md:py-3 px-4 rounded hover:bg-green-600 text-sm md:text-base">
                    Proses Pesanan
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Ringkasan Pesanan</h2>

            <!-- Metode Pembayaran -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2 text-sm md:text-base">Metode Pembayaran</h3>
                <div class="flex items-center text-sm md:text-base">
                    <span>{{ $paymentMethod->name }}</span>
                </div>
            </div>

            <!-- Daftar Produk -->
            @foreach($cart as $item)
            <div class="flex justify-between py-2 border-b text-sm md:text-base">
                <span>{{ $item['name'] }} ({{ $item['quantity'] }}x)</span>
                <span>Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="flex justify-between py-4 font-bold text-sm md:text-base">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            @if(session()->has('voucher'))
            <div class="flex justify-between py-2 text-sm md:text-base">
                <span>Voucher ({{ session('voucher')['kode_voucher'] }})</span>
                <span class="text-green-600">- Rp {{ number_format(session('voucher')['discount'], 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between py-4 font-bold text-lg md:text-base">
                <span>Total</span>
                <span class="text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Loaded');

    // Set initial state
    setCustomerType('non-member');

    // Add click event listeners to buttons
    document.getElementById('btn-non-member').addEventListener('click', function() {
        console.log('Non Member button clicked');
        setCustomerType('non-member');
    });

    document.getElementById('btn-member').addEventListener('click', function() {
        console.log('Member button clicked');
        setCustomerType('member');
    });
});

function setCustomerType(type) {
    console.log('Setting customer type to:', type);

    const nonMemberBtn = document.getElementById('btn-non-member');
    const memberBtn = document.getElementById('btn-member');
    const memberCheckForm = document.getElementById('member-check-form');
    const nonMemberForm = document.getElementById('non-member-form');
    const memberForm = document.getElementById('member-form');

    // Reset button styles
    nonMemberBtn.className = 'px-4 py-2 rounded customer-type-btn border border-gray-300';
    memberBtn.className = 'px-4 py-2 rounded customer-type-btn border border-gray-300';

    // Hide all forms
    memberCheckForm.style.display = 'none';
    nonMemberForm.style.display = 'none';
    memberForm.style.display = 'none';

    if (type === 'member') {
        memberBtn.className = 'px-4 py-2 rounded customer-type-btn bg-green-500 text-white';
        memberCheckForm.style.display = 'block';
        memberForm.style.display = 'block';
    } else {
        nonMemberBtn.className = 'px-4 py-2 rounded customer-type-btn bg-green-500 text-white';
        nonMemberForm.style.display = 'block';
    }
}

function checkMember() {
    const nik = document.getElementById('check-nik').value;
    if (!nik) {
        showNotification('Silakan masukkan NIK terlebih dahulu', 'danger');
        return;
    }

    const checkButton = event.target;
    const originalText = checkButton.innerHTML;
    checkButton.disabled = true;
    checkButton.innerHTML = 'Mencari...';

    fetch(`/check-member/${nik}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('member-nik').value = nik;

            if (data.exists) {
                document.getElementById('member_id').value = data.member.id;
                document.getElementById('member-name').value = data.member.nama_lengkap;
                document.getElementById('member-name').readOnly = true;
                showNotification('Data anggota ditemukan!', 'success');
            } else {
                document.getElementById('member_id').value = '';
                document.getElementById('member-name').value = '';
                document.getElementById('member-name').readOnly = false;
                showNotification('NIK tidak ditemukan. Silakan lengkapi data Anda.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat memeriksa NIK', 'danger');
        })
        .finally(() => {
            checkButton.disabled = false;
            checkButton.innerHTML = originalText;
        });
}

function showNotification(message, type) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');

    if (type === 'success') {
        notification.classList.add('bg-green-100', 'text-green-800');
    } else if (type === 'danger') {
        notification.classList.add('bg-red-100', 'text-red-800');
    }

    setTimeout(() => {
        notification.classList.add('hidden');
    }, 5000);
}
</script>
@endpush

<style>
.customer-type-btn {
    transition: all 0.3s ease;
}

.customer-type-btn:hover {
    opacity: 0.9;
}

/* Tambahan style untuk form yang tersembunyi */
.hidden {
    display: none !important;
}

/* Tambahkan animasi untuk notifikasi */
#notification {
    transition: all 0.3s ease;
}

#notification.hidden {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
@endsection
