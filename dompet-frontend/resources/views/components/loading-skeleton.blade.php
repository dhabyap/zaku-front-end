@props(['count' => 1])

<div>
    @for ($i = 0; $i < $count; $i++)
        <div class="animate-pulse bg-cream border-4 border-ink shadow-bs h-24 w-full mb-6 flex items-center p-4 gap-4">
            <!-- Icon/Image Skeleton -->
            <div class="h-12 w-12 bg-ink/5 border-2 border-ink/10"></div>
            
            <!-- Text Content Skeleton -->
            <div class="flex-grow space-y-3">
                <div class="h-4 bg-ink/5 w-2/3"></div>
                <div class="h-3 bg-ink/5 w-1/3"></div>
            </div>
            
            <!-- End Element Skeleton -->
            <div class="h-4 bg-ink/5 w-16"></div>
        </div>
    @endfor
</div>

<style>
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .6;
        }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
