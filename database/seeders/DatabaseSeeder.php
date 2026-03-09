<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\{User, Karyawan, Shift, QrCode, Absensi, Izin, Cuti, LokasiKantor};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin
        User::updateOrCreate(['email' => 'admin@gmail.com'], [
            'nama' => 'Admin Utama',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // // 2. Buat Lokasi Kantor
        // $lokasi = LokasiKantor::firstOrCreate(['nama_lokasi' => 'Kantor Pusat'], [
        //     'latitude' => -6.200000,
        //     'longitude' => 106.816666,
        //     'radius' => 500,
        //     'is_active' => true,
        // ]);

        // // 3. Buat Shift Reguler
        // $shiftPagi = Shift::firstOrCreate(['nama_shift' => 'Shift Pagi'], [
        //     'jam_masuk' => '08:00:00',
        //     'jam_keluar' => '17:00:00',
        //     'toleransi_menit' => 15,
        //     'is_active' => true,
        // ]);


        // // 5. Buat 10 User & Karyawan
        // $karyawans = [];
        // for ($i = 1; $i <= 10; $i++) {
        //     $user = User::factory()->create([
        //         'nama' => 'Karyawan ' . $i,
        //         'email' => 'karyawan' . $i . '@gmail.com',
        //         'role' => 'karyawan',
        //         'password' => Hash::make('password'),
        //     ]);

        //     $karyawan = Karyawan::create([
        //         'user_id' => $user->id,
        //         'nip' => 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'jabatan' => 'Staff IT ' . $i,
        //         'no_hp' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
        //         'alamat' => 'Jl. Karyawan ' . $i,
        //         'status' => 'aktif',
        //     ]);

        //     // Assign Shift ke pivot
        //     DB::table('karyawan_shift')->insert([
        //         'karyawan_id' => $karyawan->id,
        //         'shift_id' => $shiftPagi->id,
        //         'tanggal_mulai' => Carbon::now()->subYear()->toDateString(),
        //         'tanggal_selesai' => null,
        //     ]);

        //     $karyawans[] = $karyawan;
        // }

        // // 6. Generate Riwayat Selama 1 Tahun
        // $startDate = Carbon::now()->subYear();
        // $endDate = Carbon::now();
        // $faker = \Faker\Factory::create('id_ID');

        // foreach ($karyawans as $karyawan) {
        //     $currentDate = clone $startDate;

        //     // Generate beberapa blok Cuti/Izin Acak per karyawan setahun (misal 3 blok cuti/izin)
        //     $cutiIzinBlocks = [];
        //     for ($b = 0; $b < 3; $b++) {
        //         $blockStart = (clone $startDate)->addDays(rand(10, 300));
        //         $blockEnd = (clone $blockStart)->addDays(rand(1, 3));
        //         $type = rand(0, 1) ? 'cuti' : 'izin';
        //         $cutiIzinBlocks[] = ['start' => $blockStart, 'end' => $blockEnd, 'type' => $type];

        //         if ($type == 'cuti') {
        //             Cuti::create([
        //                 'karyawan_id' => $karyawan->id,
        //                 'tanggal_mulai' => $blockStart->toDateString(),
        //                 'tanggal_selesai' => $blockEnd->toDateString(),
        //                 'alasan' => 'Cuti Tahunan',
        //                 'status' => 'approved',
        //             ]);
        //         } else {
        //             Izin::create([
        //                 'karyawan_id' => $karyawan->id,
        //                 'tanggal_mulai' => $blockStart->toDateString(),
        //                 'tanggal_selesai' => $blockEnd->toDateString(),
        //                 'alasan' => 'Sakit / Izin Pribadi',
        //                 'status' => 'approved',
        //             ]);
        //         }
        //     }

        //     // Loop harian
        //     while ($currentDate <= $endDate) {
        //         // Lewati akhir pekan (Minggu libur misal)
        //         if ($currentDate->dayOfWeek == Carbon::SUNDAY || $currentDate->dayOfWeek == Carbon::SATURDAY) {
        //             $currentDate->addDay();
        //             continue;
        //         }

        //         $dateString = $currentDate->toDateString();

        //         // Cek apakah tanggal ini masuk dalam blok cuti/izin
        //         $onLeave = false;
        //         $leaveType = '';
        //         foreach ($cutiIzinBlocks as $block) {
        //             if ($currentDate->between($block['start'], $block['end'])) {
        //                 $onLeave = true;
        //                 $leaveType = $block['type'];
        //                 break;
        //             }
        //         }

        //         if ($onLeave) {
        //             // Cuti/Izin status_masuk logic
        //             Absensi::create([
        //                 'karyawan_id' => $karyawan->id,
        //                 'shift_id' => null, // bisa d null jika tidak dipatuhi
        //                 'qr_code_id' => null,
        //                 'lokasi_kantor_id' => null,
        //                 'tanggal' => $dateString,
        //                 'status_masuk' => $leaveType, // 'cuti' atau 'izin'
        //             ]);
        //         } else {
        //             // Logic Hadir normal (90%) atau Terlambat (5%) atau Alpha (5%)
        //             $rand = rand(1, 100);

        //             if ($rand <= 5) {
        //                 // Alpha (tidak ada data absensi, atau bisa insert status_masuk alpha jg dbngntung skema)
        //                 // Biasanya skip, biar dianggap alpha.
        //             } else {
        //                 // Hadir / Terlambat
        //                 $isLate = ($rand > 5 && $rand <= 10);

        //                 $masukHour = $isLate ? 8 : 7;
        //                 $masukMinute = $isLate ? rand(16, 59) : rand(30, 59); // Telat lewat 8.15

        //                 $keluarHour = 17;
        //                 $keluarMinute = rand(0, 30);

        //                 $jamMasuk = sprintf("%02d:%02d:00", $masukHour, $masukMinute);
        //                 $jamKeluar = sprintf("%02d:%02d:00", $keluarHour, $keluarMinute);

        //                 Absensi::create([
        //                     'karyawan_id' => $karyawan->id,
        //                     'shift_id' => $shiftPagi->id,
        //                     'lokasi_kantor_id' => $lokasi->id,
        //                     'tanggal' => $dateString,
        //                     'jam_masuk' => $jamMasuk,
        //                     'jam_keluar' => $jamKeluar,
        //                     'latitude_masuk' => $lokasi->latitude + (rand(-100, 100) / 1000000), // Sekitaran
        //                     'longitude_masuk' => $lokasi->longitude + (rand(-100, 100) / 1000000),
        //                     'latitude_keluar' => $lokasi->latitude + (rand(-100, 100) / 1000000),
        //                     'longitude_keluar' => $lokasi->longitude + (rand(-100, 100) / 1000000),
        //                     'status_masuk' => $isLate ? 'terlambat' : 'hadir',
        //                     'status_keluar' => 'pulang',
        //                 ]);
        //             }
        //         }

        //         $currentDate->addDay();
        //     }
        // }
    }
}
