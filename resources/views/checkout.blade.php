@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Form Pengiriman</h2>

            <!-- Pilihan Tipe Customer -->
            <div class="mb-6">
                <div class="flex space-x-4">
                    <button type="button"
                            id="btn-non-member"
                            class="px-4 py-2 rounded customer-type-btn bg-green-500 text-white">
                        Non Anggota
                    </button>
                    <button type="button"
                            id="btn-member"
                            class="px-4 py-2 rounded customer-type-btn border border-gray-300">
                        Anggota
                    </button>
                </div>
            </div>

            <!-- Form Cek NIK Anggota -->
            <div id="member-check-form" style="display: none;" class="mb-6">
                <div class="flex space-x-2">
                    <input type="text"
                           id="check-nik"
                           class="flex-1 border rounded px-3 py-2"
                           placeholder="Masukkan NIK Anggota">
                    <button type="button"
                            onclick="checkMember()"
                            class="bg-blue-500 text-white px-4 py-2 rounded">
                        Cek NIK
                    </button>
                </div>
            </div>

            <!-- Form Non Anggota -->
            <form id="non-member-form" action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method_id" value="{{ $paymentMethod->id }}">
                <input type="hidden" name="is_member" value="0">

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">No. WhatsApp</label>
                    <input type="text" name="whatsapp" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">
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
                    <label class="block text-gray-700 mb-2">NIK</label>
                    <input type="text" name="nik" id="member-nik" class="w-full border rounded px-3 py-2" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="member-name" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">No. WhatsApp</label>
                    <input type="text" name="whatsapp" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">
                    Proses Pesanan
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>

            <!-- Metode Pembayaran -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Metode Pembayaran</h3>
                <div class="flex items-center">
                    <span>{{ $paymentMethod->name }}</span>
                </div>
            </div>

            <!-- Daftar Produk -->
            @foreach($cart as $item)
            <div class="flex justify-between py-2 border-b">
                <span>{{ $item['name'] }} ({{ $item['quantity'] }}x)</span>
                <span>Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="flex justify-between py-4 font-bold">
                <span>Total</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
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
        alert('Silakan masukkan NIK terlebih dahulu');
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
            } else {
                document.getElementById('member_id').value = '';
                document.getElementById('member-name').value = '';
                document.getElementById('member-name').readOnly = false;
                alert('NIK tidak ditemukan. Silakan lengkapi data Anda.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memeriksa NIK');
        })
        .finally(() => {
            checkButton.disabled = false;
            checkButton.innerHTML = originalText;
        });
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
</style>
@endsection
