<?php

namespace Database\Seeders;

use App\Models\AssessmentCategory;
use App\Models\AssessmentQuestion;
use Illuminate\Database\Seeder;

class AssessmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Data kategori beserta pertanyaannya
        // Struktur: ['kategori' => [...pertanyaan]]
        $data = [
            [
                'kategori' => [
                    'nama'        => 'Kedisiplinan',
                    'description' => 'Penilaian terhadap ketaatan dan kepatuhan karyawan terhadap aturan perusahaan',
                    'urutan'      => 1,
                    'is_active'   => true,
                ],
                'pertanyaan' => [
                    ['question' => 'Apakah karyawan selalu hadir tepat waktu?',                          'urutan' => 1],
                    ['question' => 'Apakah karyawan mematuhi peraturan dan tata tertib perusahaan?',     'urutan' => 2],
                    ['question' => 'Apakah karyawan menyelesaikan tugas sesuai dengan deadline?',        'urutan' => 3],
                    ['question' => 'Apakah karyawan menjaga kehadiran dan menghindari absensi berlebih?','urutan' => 4],
                    ['question' => 'Apakah karyawan berpakaian sesuai dengan standar perusahaan?',       'urutan' => 5],
                ],
            ],
            [
                'kategori' => [
                    'nama'        => 'Kerja Sama Tim',
                    'description' => 'Penilaian terhadap kemampuan karyawan dalam bekerja sama dengan rekan kerja',
                    'urutan'      => 2,
                    'is_active'   => true,
                ],
                'pertanyaan' => [
                    ['question' => 'Apakah karyawan mampu bekerja sama dengan rekan satu tim?',              'urutan' => 1],
                    ['question' => 'Apakah karyawan aktif berpartisipasi dalam kegiatan tim?',               'urutan' => 2],
                    ['question' => 'Apakah karyawan bersedia membantu rekan kerja yang membutuhkan bantuan?','urutan' => 3],
                    ['question' => 'Apakah karyawan terbuka terhadap masukan dan kritik dari rekan kerja?',  'urutan' => 4],
                    ['question' => 'Apakah karyawan mendukung terciptanya suasana kerja yang kondusif?',     'urutan' => 5],
                ],
            ],
            [
                'kategori' => [
                    'nama'        => 'Tanggung Jawab',
                    'description' => 'Penilaian terhadap rasa tanggung jawab karyawan atas pekerjaan dan tugasnya',
                    'urutan'      => 3,
                    'is_active'   => true,
                ],
                'pertanyaan' => [
                    ['question' => 'Apakah karyawan menyelesaikan setiap tugas yang diberikan dengan baik?', 'urutan' => 1],
                    ['question' => 'Apakah karyawan bertanggung jawab atas kesalahan yang dilakukan?',       'urutan' => 2],
                    ['question' => 'Apakah karyawan dapat diandalkan dalam kondisi mendesak?',               'urutan' => 3],
                    ['question' => 'Apakah karyawan menjaga aset dan fasilitas perusahaan dengan baik?',    'urutan' => 4],
                    ['question' => 'Apakah karyawan berinisiatif menyelesaikan masalah tanpa harus diperintah?', 'urutan' => 5],
                ],
            ],
            [
                'kategori' => [
                    'nama'        => 'Komunikasi',
                    'description' => 'Penilaian terhadap kemampuan komunikasi karyawan di lingkungan kerja',
                    'urutan'      => 4,
                    'is_active'   => true,
                ],
                'pertanyaan' => [
                    ['question' => 'Apakah karyawan mampu menyampaikan informasi dengan jelas dan tepat?',  'urutan' => 1],
                    ['question' => 'Apakah karyawan mendengarkan dengan baik saat orang lain berbicara?',   'urutan' => 2],
                    ['question' => 'Apakah karyawan aktif melaporkan perkembangan pekerjaan kepada atasan?','urutan' => 3],
                    ['question' => 'Apakah karyawan mampu menyampaikan pendapat secara sopan dan konstruktif?', 'urutan' => 4],
                    ['question' => 'Apakah karyawan responsif terhadap komunikasi dari rekan maupun atasan?','urutan' => 5],
                ],
            ],
            [
                'kategori' => [
                    'nama'        => 'Kualitas Kerja',
                    'description' => 'Penilaian terhadap hasil dan kualitas pekerjaan yang dihasilkan karyawan',
                    'urutan'      => 5,
                    'is_active'   => true,
                ],
                'pertanyaan' => [
                    ['question' => 'Apakah hasil kerja karyawan memenuhi standar kualitas yang ditetapkan?',    'urutan' => 1],
                    ['question' => 'Apakah karyawan teliti dan minim melakukan kesalahan dalam bekerja?',       'urutan' => 2],
                    ['question' => 'Apakah karyawan mampu bekerja secara efisien dan produktif?',              'urutan' => 3],
                    ['question' => 'Apakah karyawan terus berupaya meningkatkan kualitas hasil kerjanya?',     'urutan' => 4],
                    ['question' => 'Apakah karyawan mampu menangani beberapa pekerjaan sekaligus dengan baik?','urutan' => 5],
                ],
            ],
        ];

        foreach ($data as $item) {
            // Buat kategori
            $category = AssessmentCategory::create($item['kategori']);

            // Buat pertanyaan untuk kategori ini
            foreach ($item['pertanyaan'] as $pertanyaan) {
                AssessmentQuestion::create([
                    'category_id' => $category->id,
                    'question'    => $pertanyaan['question'],
                    'urutan'      => $pertanyaan['urutan'],
                    'is_active'   => true,
                ]);
            }
        }
    }
}