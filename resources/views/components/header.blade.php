<header
    class="h-16 bg-ccb-blue flex items-center justify-between px-6 md:px-8 shrink-0 shadow-lg text-white z-40 relative gap-4">
    <!-- Left: Toggle & Mobile Info -->
    <div class="flex items-center gap-4 flex-1 min-w-0">
        <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-gray-300 focus:outline-none shrink-0">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <!-- Mobile Context (Visible only on small screens) -->
        <div class="md:hidden flex flex-col leading-tight min-w-0">
            <span class="text-[10px] opacity-75 uppercase font-bold tracking-wider">ADM</span>
            <span
                class="text-xs font-bold truncate max-w-[150px]">{{ Session::get('current_local_name', 'Local') }}</span>
        </div>
    </div>

    <!-- Center: Organization Name -->
    <div class="hidden xl:flex justify-center items-center pointer-events-none mx-auto whitespace-nowrap px-4 shrink-0">
        <span class="font-serif text-xl tracking-wide font-bold text-white shadow-sm uppercase">Congregação
            Cristã no Brasil</span>
    </div>

    <!-- Right: Admin Info + Icons -->
    <div class="flex items-center justify-end gap-6 flex-1 min-w-0 shrink-0">
        <!-- Admin Info (Desktop) -->
        <div class="hidden lg:flex flex-col items-end leading-tight text-right">
            <div class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-blue-200 uppercase">
                <span>{{ Session::get('current_regional_name', 'Regional') }}</span>
                <span class="text-blue-400">|</span>
                <span>{{ Session::get('current_local_name', 'Localidade') }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-[10px] opacity-75 bg-black/20 px-2 py-0.5 rounded-full mt-0.5">
                <div
                    class="w-1.5 h-1.5 rounded-full {{ Session::has('current_tenant_id') ? 'bg-green-400' : 'bg-gray-400' }}">
                </div>
                <span class="font-mono">{{ Config::get('database.connections.tenant.database', 'sibem_adm') }}</span>
            </div>
        </div>

        <div class="h-8 w-[1px] bg-white/10 hidden lg:block"></div>

        <div class="flex items-center gap-4">
            <!-- Notification Dropdown -->
            @section('scripts')
                <audio id="notificationSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"
                    preload="auto"></audio>
            @endsection
            <div class="relative" x-data="{
                notifOpen: false,
                unreadCount: 0,
                previousUnreadCount: 0,
                notifications: [],
                loading: false,
                soundEnabled: {{ ($user->notification_settings['sound_enabled'] ?? true) ? 'true' : 'false' }},
                pushEnabled: {{ ($user->notification_settings['browser_push'] ?? false) ? 'true' : 'false' }},
                async fetchUnreadCount() {
                    try {
                        const response = await fetch('{{ route('notifications.unreadCount') }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!response.ok) return;
                        const data = await response.json();
                        
                        // Play sound if count increased
                        if (data.count > this.unreadCount && this.soundEnabled) {
                            this.playNotificationSound();
                        }
                        
                        this.unreadCount = data.count;
                    } catch (error) {
                        console.error('Error fetching unread count:', error);
                    }
                },
                async fetchNotifications() {
                    if (this.loading) return;
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('notifications.index') }}?limit=10', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!response.ok) return;
                        const data = await response.json();
                        this.notifications = data.notifications;
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                async markAsRead(notificationId) {
                    try {
                        const response = await fetch(`/api/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                        if (response.ok) {
                            await this.fetchNotifications();
                            await this.fetchUnreadCount();
                        }
                    } catch (error) {
                        console.error('Error marking as read:', error);
                    }
                },
                async markAllAsRead() {
                    try {
                        const response = await fetch('{{ route('notifications.markAllAsRead') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                        if (response.ok) {
                            await this.fetchNotifications();
                            await this.fetchUnreadCount();
                        }
                    } catch (error) {
                        console.error('Error marking all as read:', error);
                    }
                },
                async goToNotification(notif) {
                    if (!notif.is_read) {
                        await this.markAsRead(notif.id);
                    }
                    window.location.href = notif.link;
                },
                playNotificationSound() {
                    const audio = document.getElementById('notificationSound');
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play().catch(e => console.log('Audio play blocked by browser policy'));
                    }
                },
                async init() {
                    this.fetchUnreadCount();
                    // Poll every 30 seconds
                    setInterval(() => this.fetchUnreadCount(), 30000);

                    // WebPush Registration
                    if ('serviceWorker' in navigator && 'PushManager' in window) {
                        this.registerServiceWorker();
                    }
                },
                async registerServiceWorker() {
                    try {
                        const registration = await navigator.serviceWorker.register('/sw.js');
                        console.log('Service Worker registered');
                        
                        // Check if push is enabled and subscribe if needed
                        if (this.pushEnabled) {
                            this.subscribe(registration);
                        }
                    } catch (error) {
                        console.error('Service Worker registration failed:', error);
                    }
                },
                async subscribe(registration) {
                    const publicKey = document.querySelector('meta[name=vapid-public-key]').content;
                    if (!publicKey) return;

                    try {
                        const subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: this.urlBase64ToUint8Array(publicKey)
                        });

                        await fetch('{{ route('notifications.subscribe') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(subscription)
                        });
                        console.log('User subscribed to push notifications');
                    } catch (error) {
                        console.error('Failed to subscribe user:', error);
                    }
                },
                urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding)
                        .replace(/\-/g, '+')
                        .replace(/_/g, '/');

                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);

                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                }
            }" @click.away="notifOpen = false" x-init="init()" @test-notification.window="
                previousUnreadCount = unreadCount;
                unreadCount++;
                if(soundEnabled) playNotificationSound();
                Toast.fire({ icon: 'info', title: 'Teste de Notificação', text: 'Seus alertas estão funcionando!' });
                
                // If push enabled and we have registration, show testing push
                if(pushEnabled && 'serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then(reg => {
                        reg.showNotification('SIBEM - Teste', {
                            body: 'Suas notificações push estão configuradas corretamente!',
                            icon: '/favicon.ico'
                        });
                    });
                }
            ">
                <button @click="notifOpen = !notifOpen; if(notifOpen) fetchNotifications()"
                    class="relative text-white/80 hover:text-white transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount"
                        class="absolute top-1 right-1 min-w-[20px] h-5 px-1 flex items-center justify-center text-[10px] font-bold rounded-full bg-red-500 ring-2 ring-ccb-blue"></span>
                </button>

                <!-- Dropdown Panel -->
                <div x-show="notifOpen" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute -right-12 sm:right-0 mt-2 w-[calc(100vw-2.5rem)] sm:w-96 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 origin-top-right z-50 max-h-[500px] flex flex-col">

                    <!-- Header -->
                    <div
                        class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50 rounded-t-lg">
                        <h3 class="text-sm font-bold text-gray-900">Notificações</h3>
                        <button @click="markAllAsRead()" x-show="unreadCount > 0"
                            class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                            Marcar todas como lidas
                        </button>
                    </div>

                    <!-- Notifications List -->
                    <div class="overflow-y-auto flex-1">
                        <template x-if="loading">
                            <div class="p-8 text-center text-gray-500">
                                <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                        </template>

                        <template x-if="!loading && notifications.length === 0">
                            <div class="p-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                <p class="text-sm font-medium">Nenhuma notificação</p>
                            </div>
                        </template>

                        <template x-for="notif in notifications" :key="notif.id">
                            <div class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-blue-50/50': !notif.is_read }">
                                <a href="#" @click.prevent="goToNotification(notif)" class="block px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 rounded-full"
                                                :class="notif.is_read ? 'bg-gray-300' : 'bg-blue-500'"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900" x-text="notif.title"></p>
                                            <p class="text-xs text-gray-600 mt-0.5" x-text="notif.message"></p>
                                            <p class="text-[10px] text-gray-400 mt-1" x-text="notif.time_ago"></p>
                                        </div>
                                        <button @click.prevent="markAsRead(notif.id)" x-show="!notif.is_read"
                                            class="flex-shrink-0 text-blue-600 hover:text-blue-800 p-1"
                                            title="Marcar como lida">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="relative" x-data="{ userOpen: false }">
                <button @click="userOpen = !userOpen" class="flex items-center gap-2 focus:outline-none">
                    <div
                        class="w-10 h-10 rounded-full bg-white text-ccb-blue flex items-center justify-center font-bold border-2 border-white/20 hover:border-white/40 transition-colors text-lg">
                        {{ substr(auth()->user()->nome ?? 'U', 0, 1) }}
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="userOpen" @click.away="userOpen = false"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-1 text-gray-700 ring-1 ring-black ring-opacity-5 origin-top-right z-50">

                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->nome ?? 'Usuário' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 text-gray-700">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Meu Perfil
                        </a>

                        @if(auth()->check() && (auth()->user()->perfil === 'admin_sistema' || auth()->user()->perfil === 'admin_regional'))
                            <button @click="$dispatch('open-switch-modal'); userOpen = false"
                                class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 text-blue-600">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Trocar Administração
                            </button>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>