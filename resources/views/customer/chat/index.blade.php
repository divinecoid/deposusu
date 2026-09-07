@extends('layouts.customer')

@section('title', 'Deposusu Care - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Deposusu Care</h1>
            <p class="text-sm text-slate-500 mt-1">Ada pertanyaan atau kendala? Chat dengan tim kami di sini.</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 flex flex-col h-[65vh]">
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($chat->messages as $message)
                    <div class="flex {{ $message->sender === 'customer' ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                        <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm {{ $message->sender === 'customer' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-800' }}">
                            <p>{{ $message->message }}</p>
                            <p class="text-[10px] mt-1 {{ $message->sender === 'customer' ? 'text-brand-100' : 'text-slate-400' }}">{{ $message->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p id="chat-empty-state" class="text-center text-sm text-slate-400 py-10">Mulai percakapan dengan tim Deposusu Care.</p>
                @endforelse
            </div>

            <form id="chat-form" class="border-t border-slate-100 p-3 flex items-center gap-2">
                @csrf
                <input type="text" id="chat-input" placeholder="Tulis pesan..." autocomplete="off"
                    class="flex-1 h-11 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:outline-none transition-colors">
                <button type="submit" class="h-11 w-11 flex items-center justify-center rounded-xl bg-brand-600 text-white hover:bg-brand-700 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const messagesEl = document.getElementById('chat-messages');
        const emptyState = document.getElementById('chat-empty-state');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('chat-input');
        let lastId = messagesEl.querySelector('[data-message-id]:last-child')?.dataset.messageId || 0;

        function scrollToBottom() {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }
        scrollToBottom();

        function appendMessage(message) {
            emptyState?.remove();
            const wrapper = document.createElement('div');
            wrapper.className = `flex ${message.sender === 'customer' ? 'justify-end' : 'justify-start'}`;
            wrapper.dataset.messageId = message.id;
            const bubble = document.createElement('div');
            bubble.className = `max-w-[75%] rounded-2xl px-4 py-2.5 text-sm ${message.sender === 'customer' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-800'}`;
            const time = new Date(message.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            bubble.innerHTML = `<p></p><p class="text-[10px] mt-1 ${message.sender === 'customer' ? 'text-brand-100' : 'text-slate-400'}"></p>`;
            bubble.querySelector('p').textContent = message.message;
            bubble.querySelectorAll('p')[1].textContent = time;
            wrapper.appendChild(bubble);
            messagesEl.appendChild(wrapper);
            lastId = message.id;
            scrollToBottom();
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;
            input.value = '';

            try {
                const res = await fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ message: text }),
                });
                const data = await res.json();
                if (data.success) appendMessage(data.message);
            } catch (err) {
                console.error(err);
            }
        });

        async function poll() {
            try {
                const res = await fetch(`{{ route('chat.poll') }}?after_id=${lastId}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (data.success) {
                    data.messages.forEach(appendMessage);
                }
            } catch (err) {
                console.error(err);
            }
        }

        setInterval(poll, 4000);
    })();
</script>
@endpush
@endsection
