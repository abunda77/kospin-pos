<?php

/**
 * CONTOH PENGGUNAAN QRIS STATIC
 * 
 * File ini berisi contoh-contoh penggunaan model QrisStatic
 * dalam aplikasi Laravel/Filament
 */

namespace App\Examples;

use App\Models\QrisStatic;
use App\Helpers\QrisHelper;

class QrisStaticUsageExample
{
    /**
     * Contoh 1: Mendapatkan semua QRIS yang aktif
     */
    public function getActiveQris()
    {
        $activeQris = QrisStatic::active()->get();
        
        foreach ($activeQris as $qris) {
            echo "Nama: {$qris->name}\n";
            echo "Merchant: {$qris->merchant_name}\n";
            echo "String: {$qris->qris_string}\n";
            echo "---\n";
        }
        
        return $activeQris;
    }

    /**
     * Contoh 2: Mendapatkan QRIS pertama yang aktif
     */
    public function getFirstActiveQris()
    {
        $qris = QrisStatic::active()->first();
        
        if ($qris) {
            return [
                'name' => $qris->name,
                'merchant' => $qris->merchant_name,
                'qris_string' => $qris->qris_string,
                'image_url' => $qris->qris_image_url,
            ];
        }
        
        return null;
    }

    /**
     * Contoh 3: Membuat QRIS baru secara programmatic
     */
    public function createQris()
    {
        $qris = QrisStatic::create([
            'name' => 'QRIS Toko Utama',
            'qris_string' => '00020101021126...', // String QRIS lengkap
            'merchant_name' => 'Toko ABC',
            'description' => 'QRIS untuk pembayaran di toko utama',
            'is_active' => true,
        ]);
        
        return $qris;
    }

    /**
     * Contoh 4: Update status QRIS
     */
    public function toggleQrisStatus($qrisId)
    {
        $qris = QrisStatic::find($qrisId);
        
        if ($qris) {
            $qris->is_active = !$qris->is_active;
            $qris->save();
            
            return "QRIS {$qris->name} sekarang " . ($qris->is_active ? 'aktif' : 'tidak aktif');
        }
        
        return 'QRIS tidak ditemukan';
    }

    /**
     * Contoh 5: Validasi string QRIS
     */
    public function validateQrisString($qrisString)
    {
        $isValid = QrisHelper::isValidQris($qrisString);
        
        if ($isValid) {
            $merchantName = QrisHelper::parseMerchantName($qrisString);
            return [
                'valid' => true,
                'merchant' => $merchantName,
            ];
        }
        
        return [
            'valid' => false,
            'message' => 'String QRIS tidak valid',
        ];
    }

    /**
     * Contoh 6: Ekstrak QRIS dari gambar
     */
    public function extractQrisFromImage($imagePath)
    {
        $qrisString = QrisHelper::readQrisFromImage($imagePath);
        
        if ($qrisString) {
            $merchantName = QrisHelper::parseMerchantName($qrisString);
            
            return [
                'success' => true,
                'qris_string' => $qrisString,
                'merchant_name' => $merchantName,
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Gagal mengekstrak QRIS dari gambar',
        ];
    }

    /**
     * Contoh 7: Mendapatkan QRIS untuk pembayaran
     * (Untuk digunakan di POS atau checkout)
     */
    public function getQrisForPayment()
    {
        // Ambil QRIS aktif pertama
        $qris = QrisStatic::active()->first();
        
        if (!$qris) {
            return [
                'error' => 'Tidak ada QRIS aktif',
            ];
        }
        
        return [
            'qris_string' => $qris->qris_string,
            'merchant_name' => $qris->merchant_name,
            'qr_image_url' => $qris->qris_image_url,
        ];
    }

    /**
     * Contoh 8: Search QRIS berdasarkan merchant
     */
    public function searchByMerchant($merchantName)
    {
        $qrisList = QrisStatic::where('merchant_name', 'like', "%{$merchantName}%")
            ->active()
            ->get();
        
        return $qrisList;
    }

    /**
     * Contoh 9: Mendapatkan statistik QRIS
     */
    public function getQrisStatistics()
    {
        return [
            'total' => QrisStatic::count(),
            'active' => QrisStatic::active()->count(),
            'inactive' => QrisStatic::where('is_active', false)->count(),
            'with_image' => QrisStatic::whereNotNull('qris_image')->count(),
        ];
    }

    /**
     * Contoh 10: Soft delete QRIS (jika menggunakan SoftDeletes)
     * Catatan: Model saat ini tidak menggunakan SoftDeletes
     */
    public function deleteQris($qrisId)
    {
        $qris = QrisStatic::find($qrisId);
        
        if ($qris) {
            // Alternatif: Set is_active = false instead of delete
            $qris->is_active = false;
            $qris->save();
            
            return "QRIS {$qris->name} telah dinonaktifkan";
        }
        
        return 'QRIS tidak ditemukan';
    }
}
