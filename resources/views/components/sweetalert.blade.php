@if (session('success'))
<script>
    swal({
        title: "Berhasil",
        text: "{{ session('success') }}",
        type: "success",
        timer: 3000,
        showConfirmButton: true
    });
</script>
@endif

@if (session('error'))
<script>
    swal({
        title: "Gagal",
        text: "{{ session('error') }}",
        type: "error",
        showConfirmButton: true
    });
</script>
@endif

@if (session('warning'))
<script>
    swal({
        title: "Perhatian",
        text: "{{ session('warning') }}",
        type: "warning",
        showConfirmButton: true
    });
</script>
@endif
