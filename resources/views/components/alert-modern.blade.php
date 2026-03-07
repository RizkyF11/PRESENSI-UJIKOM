@if (session('success'))
<div id="toast-success" class="fixed top-4 right-4 z-[9999] flex items-center p-4 mb-4 w-full max-w-xs text-gray-500 bg-white rounded-2xl shadow-lg border-l-4 border-teal-500 transform transition-all duration-500 translate-x-full opacity-0" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-teal-500 bg-teal-100 rounded-lg">
        <span class="iconify" data-icon="heroicons:check-circle" data-width="24"></span>
    </div>
    <div class="ms-3 text-sm font-normal text-gray-800">{{ session('success') }}</div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-transparent text-gray-400 hover:text-gray-900 rounded-lg outline-none shadow-none focus:outline-none focus:shadow-none inline-flex items-center justify-center h-8 w-8" style="background: none; border: none; box-shadow: none; outline: none;" data-dismiss-target="#toast-success" aria-label="Close" onclick="closeToast('toast-success')">
        <span class="sr-only">Close</span>
        <span class="iconify" data-icon="heroicons:x-mark" data-width="20"></span>
    </button>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toast = document.getElementById('toast-success');
        if (toast) {
            // Animate In
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Auto close after 3 seconds
            setTimeout(() => {
                closeToast('toast-success');
            }, 3000);
        }
    });
</script>
@endif

@if (session('error'))
<div id="toast-error" class="fixed top-4 right-4 z-[9999] flex items-center p-4 mb-4 w-full max-w-xs text-gray-500 bg-white rounded-2xl shadow-lg border-l-4 border-red-500 transform transition-all duration-500 translate-x-full opacity-0" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
        <span class="iconify" data-icon="heroicons:exclamation-circle" data-width="24"></span>
    </div>
    <div class="ms-3 text-sm font-normal text-gray-800">{{ session('error') }}</div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-transparent text-gray-400 hover:text-gray-900 rounded-lg outline-none shadow-none focus:outline-none focus:shadow-none inline-flex items-center justify-center h-8 w-8" style="background: none; border: none; box-shadow: none; outline: none;" data-dismiss-target="#toast-error" aria-label="Close" onclick="closeToast('toast-error')">
        <span class="sr-only">Close</span>
        <span class="iconify" data-icon="heroicons:x-mark" data-width="20"></span>
    </button>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toast = document.getElementById('toast-error');
        if (toast) {
            // Animate In
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Auto close after 3 seconds
            setTimeout(() => {
                closeToast('toast-error');
            }, 3000);
        }
    });
</script>
@endif

@if (session('warning'))
<div id="toast-warning" class="fixed top-4 right-4 z-[9999] flex items-center p-4 mb-4 w-full max-w-xs text-gray-500 bg-white rounded-2xl shadow-lg border-l-4 border-amber-500 transform transition-all duration-500 translate-x-full opacity-0" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-amber-500 bg-amber-100 rounded-lg">
        <span class="iconify" data-icon="heroicons:exclamation-triangle" data-width="24"></span>
    </div>
    <div class="ms-3 text-sm font-normal text-gray-800">{{ session('warning') }}</div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-transparent text-gray-400 hover:text-gray-900 rounded-lg outline-none shadow-none focus:outline-none focus:shadow-none inline-flex items-center justify-center h-8 w-8" style="background: none; border: none; box-shadow: none; outline: none;" data-dismiss-target="#toast-warning" aria-label="Close" onclick="closeToast('toast-warning')">
        <span class="sr-only">Close</span>
        <span class="iconify" data-icon="heroicons:x-mark" data-width="20"></span>
    </button>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toast = document.getElementById('toast-warning');
        if (toast) {
            // Animate In
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Auto close after 3 seconds
            setTimeout(() => {
                closeToast('toast-warning');
            }, 3000);
        }
    });
</script>
@endif

<script>
    // Global function untuk menutup modal toast
    function closeToast(id) {
        const toast = document.getElementById(id);
        if (toast) {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 500); // Sesuaikan dengan durasi transition Tailwind CSS
        }
    }
</script>