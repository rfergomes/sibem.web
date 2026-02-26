<!-- Mobile Sidebar (Fixed Overlay) -->
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
     class="fixed inset-0 bg-gray-900/80 z-30 md:hidden backdrop-blur-sm" style="display: none;" aria-hidden="true">
</div>

<div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 z-40 w-64 h-full bg-ccb-blue border-r border-gray-800 shrink-0 md:hidden flex flex-col"
     style="display: none;">

     <x-sidebar-content />

</div>

<!-- Desktop Sidebar (Static Flex Item) -->
<!-- Always visible on desktop if sidebarOpen is true (handled by Alpine in parent, but we can force it or just use the same variable) -->
<!-- Note: If sidebarOpen is toggled on desktop, this hides/shows. If you want it always on, remove x-show. Assuming toggleable. -->
<div x-show="sidebarOpen"
     class="hidden md:flex flex-col w-64 bg-ccb-blue border-r border-gray-800 shrink-0 h-full transition-all duration-300"
     style="display: none;">

     <x-sidebar-content />

</div>
