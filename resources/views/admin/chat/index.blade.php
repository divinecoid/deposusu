@extends('layouts.admin')

@section('header', 'Live Chat Console')

@section('content')
<div x-data="chatConsole()" class="h-[calc(100vh-8rem)] flex flex-col lg:flex-row gap-6">

    <!-- Left Sidebar: Chat Categories & Lists -->
    <div class="w-full lg:w-80 bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col overflow-hidden h-full">
        <!-- Tabs Header -->
        <div class="flex border-b border-slate-100 pb-3 mb-4 gap-1">
            <template x-for="tab in ['unread', 'active', 'history']">
                <button type="button" 
                        @click="activeList = tab" 
                        class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold text-center capitalize transition"
                        :class="activeList === tab ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-600'">
                    <span x-text="tab"></span>
                    <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full" 
                          :class="activeList === tab ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                          x-text="getBadgeCount(tab)"></span>
                </button>
            </template>
        </div>

        <!-- Chat List -->
        <div class="flex-1 overflow-y-auto space-y-2 pr-1">
            <template x-for="chat in getChatsByTab()" :key="chat.id">
                <div @click="selectChat(chat)" 
                     class="p-3.5 rounded-xl border cursor-pointer transition flex items-center justify-between"
                     :class="selectedChat && selectedChat.id === chat.id ? 'bg-indigo-50/50 border-indigo-200 shadow-sm' : 'bg-slate-50/40 hover:bg-slate-50 border-slate-100'">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0" 
                             x-text="chat.customer_name[0]"></div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-slate-800 text-sm truncate" x-text="chat.customer_name"></h4>
                            <p class="text-xs text-slate-400 truncate" x-text="chat.last_message || 'Tidak ada pesan'"></p>
                        </div>
                    </div>
                    <!-- Indicator dot -->
                    <template x-if="chat.status === 'unread'">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0 ml-2"></span>
                    </template>
                </div>
            </template>
            <template x-if="getChatsByTab().length === 0">
                <div class="h-full flex flex-col items-center justify-center text-slate-400 text-xs py-8">
                    <span>Tidak ada percakapan.</span>
                </div>
            </template>
        </div>
    </div>

    <!-- Center: Interactive Chat Area -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full">
        <!-- Chat Header -->
        <template x-if="selectedChat">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-sm" x-text="selectedChat.customer_name[0]"></div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm" x-text="selectedChat.customer_name"></h4>
                        <p class="text-xs text-slate-400" x-text="selectedChat.customer_email"></p>
                    </div>
                </div>
                <template x-if="selectedChat.agent_name">
                    <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Ditangani oleh <span x-text="selectedChat.agent_name"></span>
                    </span>
                </template>
            </div>
        </template>

        <!-- Message Box -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/30" x-ref="messagesBox">
            <template x-if="!selectedChat">
                <div class="h-full flex flex-col items-center justify-center text-slate-400">
                    <svg class="w-16 h-16 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-sm">Silakan pilih chat untuk memulai percakapan</span>
                </div>
            </template>

            <template x-if="selectedChat">
                <div class="space-y-4">
                    <template x-for="msg in selectedChat.messages" :key="msg.id">
                        <div class="flex" :class="msg.sender === 'agent' ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[70%] rounded-2xl px-4 py-2.5 text-sm"
                                 :class="msg.sender === 'agent' 
                                    ? (msg.message.startsWith('SISTEM:') ? 'bg-amber-100 text-amber-800 rounded-tr-none font-mono text-xs border border-amber-200' : 'bg-indigo-600 text-white rounded-tr-none shadow-sm') 
                                    : 'bg-white text-slate-700 rounded-tl-none border border-slate-100 shadow-xs'">
                                <p x-text="msg.message"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Chat Input Bar -->
        <template x-if="selectedChat">
            <div class="p-4 border-t border-slate-100 bg-white">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-3">
                    <input type="text" x-model="newMessageText" placeholder="Ketik pesan balasan disini..." class="flex-1 rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition flex items-center justify-center">
                        Kirim
                    </button>
                </form>
            </div>
        </template>
    </div>

    <!-- Right Sidebar: Customer Detail & Quick Actions -->
    <div class="w-full lg:w-80 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col overflow-hidden h-full">
        <h3 class="text-sm font-bold text-slate-800 mb-5 uppercase tracking-wider text-slate-500">Customer Detail</h3>

        <template x-if="!selectedChat">
            <div class="flex-1 flex items-center justify-center text-slate-400 text-xs">
                Belum ada chat yang dipilih.
            </div>
        </template>

        <template x-if="selectedChat && customerDetail">
            <div class="flex-1 flex flex-col justify-between">
                <div class="space-y-6">
                    <!-- Profile Card -->
                    <div class="text-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-black text-2xl mb-2" x-text="customerDetail.name[0]"></div>
                        <h4 class="font-extrabold text-slate-800 text-base" x-text="customerDetail.name"></h4>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-bold" 
                              :class="customerDetail.membership === 'Gold' ? 'bg-amber-100 text-amber-800 border border-amber-200'
                                    : customerDetail.membership === 'VIP' ? 'bg-purple-100 text-purple-800 border border-purple-200'
                                    : customerDetail.membership === 'Silver' ? 'bg-slate-100 text-slate-600 border border-slate-200'
                                    : 'bg-orange-50 text-orange-700 border border-orange-200'"
                              x-text="'Member: ' + customerDetail.membership"></span>
                    </div>

                    <!-- Last Order Card -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Last Order</h4>
                        <template x-if="!lastOrder">
                            <p class="text-xs text-slate-400 italic">Belum ada riwayat pesanan.</p>
                        </template>
                        <template x-if="lastOrder">
                            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-mono font-bold text-slate-700" x-text="lastOrder.order_number"></span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize"
                                          :class="lastOrder.status === 'pending' ? 'bg-yellow-100 text-yellow-800'
                                                : lastOrder.status === 'done' ? 'bg-green-100 text-green-800'
                                                : lastOrder.status === 'cancelled' ? 'bg-red-100 text-red-800'
                                                : 'bg-blue-100 text-blue-800'"
                                          x-text="lastOrder.status_label"></span>
                                </div>
                                <div class="flex items-baseline justify-between pt-1">
                                    <span class="text-xs text-slate-500">Total Belanja:</span>
                                    <span class="font-extrabold text-indigo-600 text-sm" x-text="formatRupiah(lastOrder.total_amount)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Quick Actions Panel -->
                <div class="space-y-2.5 pt-6 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Quick Action</h4>
                    
                    <button type="button" 
                            @click="triggerAction('check_order')" 
                            class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border border-slate-200 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Check Order
                    </button>

                    <button type="button" 
                            @click="triggerAction('refund')" 
                            class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition border border-rose-100 flex items-center justify-center gap-1.5"
                            :disabled="!lastOrder || lastOrder.status === 'cancelled' || lastOrder.status === 'rejected'"
                            :class="{ 'opacity-50 cursor-not-allowed': !lastOrder || lastOrder.status === 'cancelled' || lastOrder.status === 'rejected' }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Refund Order
                    </button>

                    <button type="button" 
                            @click="triggerAction('repeat_order')" 
                            class="w-full py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition border border-emerald-100 flex items-center justify-center gap-1.5"
                            :disabled="!lastOrder"
                            :class="{ 'opacity-50 cursor-not-allowed': !lastOrder }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5"></path></svg>
                        Repeat Order
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function chatConsole() {
        return {
            unreadChats: @json($unreadChats),
            activeChats: @json($activeChats),
            historyChats: @json($historyChats),
            activeList: 'unread',
            selectedChat: null,
            customerDetail: null,
            lastOrder: null,
            newMessageText: '',
            loading: false,

            getBadgeCount(tab) {
                if (tab === 'unread') return this.unreadChats.length;
                if (tab === 'active') return this.activeChats.length;
                return this.historyChats.length;
            },

            getChatsByTab() {
                if (this.activeList === 'unread') return this.unreadChats;
                if (this.activeList === 'active') return this.activeChats;
                return this.historyChats;
            },

            selectChat(chat) {
                this.selectedChat = chat;
                this.customerDetail = null;
                this.lastOrder = null;
                this.newMessageText = '';

                // Fetch customer details and last order
                this.fetchCustomerData(chat.customer_email);

                // Auto-scroll chat box
                this.scrollToBottom();
            },

            async fetchCustomerData(email) {
                if (!email) return;
                try {
                    let response = await fetch(`{{ route('admin.live-chat.customer-details') }}?email=\${encodeURIComponent(email)}`);
                    let result = await response.json();
                    if (result.success) {
                        this.customerDetail = result.customer;
                        this.lastOrder = result.last_order;
                    }
                } catch (e) {
                    console.error('Error fetching customer data:', e);
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    if (this.$refs.messagesBox) {
                        this.$refs.messagesBox.scrollTop = this.$refs.messagesBox.scrollHeight;
                    }
                });
            },

            async sendMessage() {
                if (!this.newMessageText.trim() || !this.selectedChat) return;

                let text = this.newMessageText;
                this.newMessageText = '';

                let data = {
                    chat_id: this.selectedChat.id,
                    message: text
                };

                try {
                    let response = await fetch("{{ route('admin.live-chat.send') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    });

                    let result = await response.json();

                    if (result.success) {
                        // Append locally
                        this.selectedChat.messages.push(result.message);
                        this.selectedChat.last_message = text;

                        // Handle chat status transitions (if transitioned from unread to active)
                        if (this.selectedChat.status === 'unread') {
                            this.selectedChat.status = 'active';
                            this.selectedChat.agent_name = result.agent_name;

                            // Move chat from unread to active list
                            this.unreadChats = this.unreadChats.filter(c => c.id !== this.selectedChat.id);
                            // Avoid duplication
                            if (!this.activeChats.find(c => c.id === this.selectedChat.id)) {
                                this.activeChats.unshift(this.selectedChat);
                            }
                        }

                        this.scrollToBottom();
                    } else {
                        alert('Gagal mengirim pesan.');
                    }
                } catch (e) {
                    console.error('Error sending message:', e);
                    alert('Gagal mengirim pesan.');
                }
            },

            async triggerAction(actionName) {
                if (!this.selectedChat || !this.customerDetail) return;
                
                if (actionName === 'refund' || actionName === 'repeat_order') {
                    let confirmText = actionName === 'refund' 
                        ? 'Apakah Anda yakin ingin memproses Refund untuk pesanan terakhir customer ini?'
                        : 'Apakah Anda yakin ingin menduplikasi (Repeat Order) pesanan terakhir customer ini?';
                    if (!confirm(confirmText)) return;
                }

                let data = {
                    chat_id: this.selectedChat.id,
                    action: actionName,
                    email: this.customerDetail.email
                };

                try {
                    let response = await fetch("{{ route('admin.live-chat.action') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    });

                    let result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        
                        // Append system/agent log message if returned
                        if (result.system_message) {
                            // Fetch updated message transcript or push dummy message
                            this.selectedChat.messages.push({
                                id: Date.now(),
                                sender: 'agent',
                                message: result.system_message
                            });
                            this.selectedChat.last_message = result.system_message;
                            this.scrollToBottom();
                        }

                        // Refresh customer details to update order status
                        this.fetchCustomerData(this.customerDetail.email);
                    } else {
                        alert('Gagal mengeksekusi aksi: ' + result.message);
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan saat memproses aksi.');
                }
            },

            formatRupiah(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }
        };
    }
</script>
@endsection
