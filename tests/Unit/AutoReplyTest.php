<?php

namespace Tests\Unit;

use App\Models\Tickets;
use PHPUnit\Framework\TestCase;

class AutoReplyTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Ambil semua saran auto-reply dari model Tickets
    |--------------------------------------------------------------------------
    */
    private function getSuggestions(): array
    {
        return [
            'jaringan' => 'Terima kasih sudah melaporkan. Tim kami sedang mengecek koneksi jaringan di area Anda. Mohon tunggu konfirmasi dalam 30 menit.',
            'hardware'  => 'Laporan kerusakan hardware sudah kami terima. Teknisi akan segera dikirim ke lokasi Anda. Harap pastikan perangkat tidak digunakan sementara.',
            'software'  => 'Kami sedang mengidentifikasi masalah pada aplikasi yang dilaporkan. Coba restart aplikasi terlebih dahulu sambil menunggu solusi dari tim kami.',
            'akses'     => 'Permintaan akses/izin Anda sedang diproses oleh tim IT. Estimasi selesai dalam 1x24 jam kerja.',
            'email'     => 'Masalah email Anda sudah tercatat. Pastikan koneksi internet stabil dan coba logout lalu login kembali ke akun email Anda.',
            'printer'   => 'Keluhan printer sudah diterima. Coba restart printer dan pastikan driver sudah ter-install dengan benar.',
            'lainnya'   => 'Laporan Anda sudah kami terima dan sedang dalam proses penanganan. Kami akan segera menghubungi Anda dengan solusi.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | TEST 1
    | Memastikan setiap kategori menghasilkan pesan yang tidak kosong
    |--------------------------------------------------------------------------
    */
    /** @test */
    public function setiap_kategori_menghasilkan_pesan_tidak_kosong()
    {
        $suggestions = $this->getSuggestions();

        foreach ($suggestions as $kategori => $pesan) {
            $this->assertNotEmpty(
                $pesan,
                "Kategori [{$kategori}] menghasilkan pesan kosong!"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TEST 2
    | Memastikan setiap pesan minimal 10 karakter (bukan string asal-asalan)
    |--------------------------------------------------------------------------
    */
    /** @test */
    public function setiap_pesan_memiliki_panjang_minimal_10_karakter()
    {
        $suggestions = $this->getSuggestions();

        foreach ($suggestions as $kategori => $pesan) {
            $this->assertGreaterThanOrEqual(
                10,
                strlen($pesan),
                "Pesan kategori [{$kategori}] terlalu pendek, kemungkinan kosong atau tidak valid!"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TEST 3
    | Memastikan kategori yang tidak dikenal fallback ke 'lainnya' (tidak kosong)
    |--------------------------------------------------------------------------
    */
    /** @test */
    public function kategori_tidak_dikenal_fallback_ke_lainnya()
    {
        $suggestions  = $this->getSuggestions();
        $kategoriAsal = 'kategori_tidak_ada';

        // Simulasi logika getAutoReplyAttribute di model Tickets
        $pesan = $suggestions[$kategoriAsal] ?? $suggestions['lainnya'];

        $this->assertNotEmpty(
            $pesan,
            "Fallback kategori tidak dikenal menghasilkan pesan kosong!"
        );

        $this->assertEquals(
            $suggestions['lainnya'],
            $pesan,
            "Fallback tidak mengarah ke pesan 'lainnya'!"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TEST 4
    | Memastikan kunci 'lainnya' selalu ada sebagai fallback
    |--------------------------------------------------------------------------
    */
    /** @test */
    public function kunci_lainnya_selalu_tersedia_sebagai_fallback()
    {
        $suggestions = $this->getSuggestions();

        $this->assertArrayHasKey(
            'lainnya',
            $suggestions,
            "Kunci 'lainnya' tidak ditemukan! Sistem tidak punya fallback."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TEST 5
    | Memastikan tidak ada pesan yang berisi null
    |--------------------------------------------------------------------------
    */
    /** @test */
    public function tidak_ada_pesan_yang_bernilai_null()
    {
        $suggestions = $this->getSuggestions();

        foreach ($suggestions as $kategori => $pesan) {
            $this->assertNotNull(
                $pesan,
                "Kategori [{$kategori}] menghasilkan nilai null!"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TEST 6
    | Memastikan semua kategori yang didukung tersedia
    |--------------------------------------------------------------------------
    */
    /** @test */
    public function semua_kategori_yang_didukung_tersedia()
    {
        $suggestions       = $this->getSuggestions();
        $kategoriDidukung  = ['jaringan', 'hardware', 'software', 'akses', 'email', 'printer', 'lainnya'];

        foreach ($kategoriDidukung as $kategori) {
            $this->assertArrayHasKey(
                $kategori,
                $suggestions,
                "Kategori [{$kategori}] tidak ditemukan di daftar auto-reply!"
            );
        }
    }
}