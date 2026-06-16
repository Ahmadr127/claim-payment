<div x-data="toastComponent()" 
     @toast.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none w-80 max-w-[90vw]"
     x-cloak>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform transition-all duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform transition-all duration-300"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 flex overflow-hidden"
             :class="toast.type === 'success' ? 'border-green-500' : 'border-red-500'">
            
            <div class="p-4 flex items-start w-full">
                <div class="flex-shrink-0 mt-0.5">
                    <i x-show="toast.type === 'success'" class="fas fa-check-circle text-green-500 text-xl"></i>
                    <i x-show="toast.type === 'error'" class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 leading-tight" x-text="toast.message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="removeToast(toast.id)" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastComponent', () => ({
            toasts: [],
            addToast(detail) {
                const id = Date.now();
                this.toasts.push({
                    id: id,
                    message: detail.message,
                    type: detail.type || 'success',
                    visible: true
                });
                
                setTimeout(() => {
                    this.removeToast(id);
                }, detail.duration || 3000);
            },
            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            }
        }));
    });
    
    window.Toast = {
        success: (message, options = {}) => window.dispatchEvent(new CustomEvent('toast', { detail: { message, type: 'success', ...options } })),
        error: (message, options = {}) => window.dispatchEvent(new CustomEvent('toast', { detail: { message, type: 'error', ...options } }))
    };

    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            window.Toast && Toast.success(@json(session('success')));
        @endif
        @if(session('error'))
            window.Toast && Toast.error(@json(session('error')));
        @endif
        @if($errors->any())
            @foreach($errors->all() as $err)
                window.Toast && Toast.error(@json($err), { duration: 6000 });
            @endforeach
        @endif
    });
</script>
