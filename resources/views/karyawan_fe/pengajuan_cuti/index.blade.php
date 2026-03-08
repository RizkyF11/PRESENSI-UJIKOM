@extends('layouts.karyawan')

@section('header-left')
<div class="flex items-center w-full">
    <!-- Tombol kembali yang langsung kembali ke dashboard karyawan -->
    <a href="{{ route('karyawan.dashboard') }}" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-bold" style="text-decoration: none;">
        <div class="bg-teal-50 w-8 h-8 rounded-full flex items-center justify-center">
            <span class="iconify" data-icon="heroicons:arrow-left" data-width="20"></span>
        </div>
    </a>
    <h1 class="text-[16px] font-bold text-gray-800 ml-auto mr-auto pl-4 mb-0">Pengajuan Cuti</h1>
    <div class="w-10"></div> <!-- Placeholder space -->
</div>
@endsection

@section('content')
<div class="container-fluid mb-4 px-2 mt-2">
    <div class="mb-4">
        <!-- Button trigger modal untuk Tambah Cuti (tetap sama desainnya dengan Edit) -->
        <button type="button" class="btn w-100 font-weight-bold d-flex align-items-center justify-content-center gap-2 py-3 shadow-sm border-0" data-toggle="modal" data-target="#modalTambahCuti" style="background-color: #4DB6AC; color: white; border-radius: 12px; font-size: 14px;">
            <span class="iconify" data-icon="heroicons:plus-circle" data-width="22"></span>
            Buat Pengajuan Cuti Baru
        </button>
    </div>

    <!-- Title Riwayat -->
    <h6 class="font-weight-bold text-gray-800 mb-3 px-1" style="font-size: 14px;">Riwayat Pengajuan Cuti</h6>

    <!-- List Riwayat Cuti -->
    <div class="list-group">
        @forelse ($cuti as $item)
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 16px;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-weight-bold text-gray-800" style="font-size: 13px;">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }} <span class="text-gray-400 font-normal">s/d</span> {{ \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                    @if($item->status == 'pending')
                    <span class="badge text-warning px-2 py-1" style="border-radius: 6px; font-size: 11px; font-weight: 600; background-color: #FEF3C7; color: #D97706;">Pending</span>
                    @elseif($item->status == 'approved')
                    <span class="badge text-success px-2 py-1" style="border-radius: 6px; font-size: 11px; font-weight: 600; background-color: #D1FAE5; color: #059669;">Approved</span>
                    @else
                    <span class="badge text-danger px-2 py-1" style="border-radius: 6px; font-size: 11px; font-weight: 600; background-color: #FEE2E2; color: #DC2626;">Rejected</span>
                    @endif
                </div>

                <p class="text-gray-500 mb-3" style="font-size: 12px; line-height: 1.5;">{{ Str::limit($item->alasan, 80) }}</p>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-gray-400" style="font-size: 11px;"><i class="fa fa-clock-o mr-1"></i> Diajukan: {{ $item->created_at->diffForHumans() }}</span>

                    @if($item->status == 'pending')
                    <div class="d-flex gap-2">
                        <!-- Tombol Panggil Modal Edit -->
                        <button type="button" class="btn btn-sm text-teal-600 bg-teal-50 d-flex align-items-center justify-content-center border-0" data-toggle="modal" data-target="#modalEditCuti{{ $item->id }}" style="width: 32px; height: 32px; border-radius: 8px;">
                            <span class="iconify" data-icon="heroicons:pencil-square" data-width="18"></span>
                        </button>
                        <form action="{{ route('karyawan.cuti.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-red-500 bg-red-50 d-flex align-items-center justify-content-center border-0" style="width: 32px; height: 32px; border-radius: 8px;">
                                <span class="iconify" data-icon="heroicons:trash" data-width="18"></span>
                            </button>
                        </form>
                    </div>

                    <!-- Modal Edit Cuti (Konsisten secara Visual dengan Modal Tambah) -->
                    <div class="modal fade" id="modalEditCuti{{ $item->id }}" tabindex="-1" aria-labelledby="modalEditCutiLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mx-4 sm:mx-auto">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <h5 class="modal-title font-weight-bold text-gray-800" id="modalEditCutiLabel{{ $item->id }}" style="font-size: 18px;">Ubah Pengajuan Cuti</h5>
                                    <button type="button" class="btn-close m-0 border-0 outline-none shadow-none focus:outline-none focus:shadow-none" style="font-size: 28px; opacity: 0.6; background: none; box-shadow: none; outline: none; line-height: 1;" data-dismiss="modal" aria-label="Close" style="font-size: 20px; opacity: 0.5; background: none; box-shadow: none; outline: none;">
                                        &times;
                                    </button>
                                </div>
                                <form action="{{ route('karyawan.cuti.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body px-4 pt-4 pb-4">
                                        <div class="form-group mb-4">
                                            <label class="form-label font-weight-bold text-gray-700" style="font-size: 13px;">Mulai Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal_mulai" value="{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('Y-m-d') }}" id="edit_start_{{ $item->id }}" class="form-control bg-gray-50 border-0" required style="border-radius: 12px; padding: 12px 16px; font-size: 14px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);">
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="form-label font-weight-bold text-gray-700" style="font-size: 13px;">Sampai Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal_selesai" value="{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('Y-m-d') }}" id="edit_end_{{ $item->id }}" class="form-control bg-gray-50 border-0" required style="border-radius: 12px; padding: 12px 16px; font-size: 14px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);">
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="form-label font-weight-bold text-gray-700" style="font-size: 13px;">Alasan Cuti <span class="text-danger">*</span></label>
                                            <textarea class="form-control bg-gray-50 border-0" name="alasan" rows="4" required style="border-radius: 12px; padding: 14px 16px; font-size: 14px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);">{{ $item->alasan }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                        <div class="d-flex w-100 gap-3">
                                            <button type="button" class="btn w-50 py-3 font-weight-bold text-gray-600 shadow-none" data-dismiss="modal" style="border-radius: 12px; font-size: 14px; background-color: #F3F4F6; border: none; outline: none; box-shadow: none;">Batal</button>
                                            <button type="submit" class="btn w-50 py-3 font-weight-bold shadow-none" style="background-color: #4DB6AC; color: white; border-radius: 12px; font-size: 14px; border: none; outline: none; box-shadow: none;">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Modal Edit Cuti -->
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <div class="text-gray-300 mb-3 flex items-center justify-center">
                <span class="iconify" data-icon="heroicons:folder-open" data-width="60"></span>
            </div>
            <p class="text-gray-500 font-medium text-sm">Belum ada riwayat pengajuan cuti.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $cuti->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Modal Tambah Cuti (Konsisten secara Visual dengan Modal Edit) -->
<div class="modal fade" id="modalTambahCuti" tabindex="-1" aria-labelledby="modalTambahCutiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-4 sm:mx-auto">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold text-gray-800" id="modalTambahCutiLabel" style="font-size: 18px;">Pengajuan Cuti Baru</h5>
                <button type="button" class="btn-close m-0 border-0 outline-none shadow-none focus:outline-none focus:shadow-none" style="font-size: 28px; opacity: 0.6; background: none; box-shadow: none; outline: none; line-height: 1;" data-dismiss="modal" aria-label="Close" style="font-size: 20px; opacity: 0.5; background: none; box-shadow: none; outline: none;">
                    &times;
                </button>
            </div>
            <form action="{{ route('karyawan.cuti.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-4 pb-4">
                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold text-gray-700" style="font-size: 13px;">Mulai Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control bg-gray-50 border-0" required style="border-radius: 12px; padding: 12px 16px; font-size: 14px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold text-gray-700" style="font-size: 13px;">Sampai Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai" class="form-control bg-gray-50 border-0" required style="border-radius: 12px; padding: 12px 16px; font-size: 14px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);">
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label font-weight-bold text-gray-700" style="font-size: 13px;">Alasan Cuti <span class="text-danger">*</span></label>
                        <textarea class="form-control bg-gray-50 border-0" name="alasan" rows="4" placeholder="Tuliskan alasan spesifik Anda dengan jelas..." required style="border-radius: 12px; padding: 14px 16px; font-size: 14px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.03);"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <div class="d-flex w-100 gap-3">
                        <button type="button" class="btn w-50 py-3 font-weight-bold text-gray-600 shadow-none" data-dismiss="modal" style="border-radius: 12px; font-size: 14px; background-color: #F3F4F6; border: none; outline: none; box-shadow: none;">Batal</button>
                        <button type="submit" class="btn w-50 py-3 font-weight-bold shadow-none" style="background-color: #4DB6AC; color: white; border-radius: 12px; font-size: 14px; border: none; outline: none; box-shadow: none;">Kirim Cuti</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal Tambah Cuti -->

<!-- Menggunakan modal Bootstrap script -->
@push('scripts')
<script>
    // Memastikan format tanggal default minimal adalah hari ini untuk tanggal mulai dan selesai
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const startDateInput = document.querySelector('input[name="tanggal_mulai"]');
        const endDateInput = document.querySelector('input[name="tanggal_selesai"]');

        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        });
    });
</script>
@endpush
@endsection