{{-- Smart URL Shortener — real shortener with trust checks --}}
<div x-data="smartUrlShortener()" class="space-y-6">

    {{-- ─── Create form ─── --}}
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Paste your long URL</label>
            <input
                type="url"
                x-model.trim="url"
                @keydown.enter.prevent="create()"
                placeholder="https://example.com/your-long-url..."
                class="block w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                :disabled="loading"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Custom slug <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <div class="flex gap-2">
                <div class="flex-1 flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-primary-500">
                    <span class="pl-3 text-sm text-gray-400 select-none whitespace-nowrap">urlcv.com/l/</span>
                    <input
                        type="text"
                        x-model.trim="slug"
                        placeholder="your-slug"
                        class="flex-1 border-0 px-1 py-2.5 text-sm font-mono focus:ring-0"
                        :disabled="loading"
                    />
                </div>
                <button type="button" @click="autoSlug()" class="shrink-0 px-3 py-2.5 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium hover:bg-gray-200 transition-colors" :disabled="loading">
                    Random
                </button>
            </div>
            <p class="mt-1 text-xs text-gray-400">Lowercase letters, numbers, and hyphens. 3–32 characters.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Title <span class="text-gray-400 font-normal">(optional — for your reference)</span>
            </label>
            <input
                type="text"
                x-model.trim="title"
                placeholder="e.g. Q2 campaign link"
                class="block w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                :disabled="loading"
            />
        </div>

        {{-- Honeypot --}}
        <div class="absolute -left-[9999px]" aria-hidden="true">
            <input type="text" x-model="honeypot" tabindex="-1" autocomplete="off" name="website_url" />
        </div>

        {{-- Error --}}
        <div x-show="error" x-cloak class="flex items-start gap-2 p-3 bg-rose-50 border border-rose-200 rounded-lg text-sm text-rose-700">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
            <span x-text="error"></span>
        </div>

        {{-- Submit --}}
        <button
            type="button"
            @click="create()"
            :disabled="loading || !url.trim()"
            class="w-full py-3 px-4 rounded-xl font-semibold text-sm text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
            <span x-show="!loading">Shorten URL</span>
            <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Creating…
            </span>
        </button>
    </div>

    {{-- ─── Result (shown after creation) ─── --}}
    <template x-if="result">
        <div class="space-y-5">

            {{-- Short URL card --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Your short link</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold"
                              :class="result.trust_score >= 70 ? 'text-emerald-700' : result.trust_score >= 40 ? 'text-amber-700' : 'text-rose-700'"
                              x-text="result.trust_score >= 70 ? 'Trusted' : result.trust_score >= 40 ? 'Caution' : 'Risky'"></span>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold text-white"
                              :class="result.trust_score >= 70 ? 'bg-emerald-500' : result.trust_score >= 40 ? 'bg-amber-500' : 'bg-rose-500'"
                              x-text="result.trust_score"></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2.5">
                    <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <span class="font-mono text-sm font-semibold text-gray-900 truncate" x-text="result.short_url.replace('https://','').replace('http://','')"></span>
                    <button type="button" @click="copyResult()" class="ml-auto shrink-0 px-2.5 py-1 rounded-md bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100 transition-colors" x-text="copied ? 'Copied!' : 'Copy'"></button>
                </div>

                <div class="flex gap-2">
                    <a :href="result.short_url" target="_blank" class="flex-1 text-center py-2 px-3 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                        Open preview →
                    </a>
                    <button type="button" @click="createAnother()" class="flex-1 text-center py-2 px-3 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                        Shorten another
                    </button>
                </div>
            </div>

            {{-- QR code --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-start gap-4">
                    <div id="sus-qr-target" class="shrink-0 w-24 h-24 bg-gray-50 rounded-lg border border-gray-100 flex items-center justify-center overflow-hidden"></div>
                    <div class="min-w-0 space-y-1">
                        <p class="text-sm font-semibold text-gray-900">QR Code</p>
                        <p class="text-xs text-gray-500">Scan to open the preview page for this short link. Works with any phone camera.</p>
                        <p class="text-xs font-mono text-gray-400 truncate" x-text="result.short_url.replace('https://','').replace('http://','')"></p>
                    </div>
                </div>
            </div>

            {{-- Safety checks --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Safety checks</span>
                </div>
                <template x-for="c in result.checks" :key="c.label">
                    <div class="flex items-center gap-2.5 px-4 py-2 border-b border-gray-50 last:border-b-0">
                        <span class="shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px]"
                              :class="c.status === 'pass' ? 'bg-emerald-100 text-emerald-600' : c.status === 'warn' ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600'">
                            <span x-show="c.status === 'pass'">&#10003;</span>
                            <span x-show="c.status === 'warn'">!</span>
                            <span x-show="c.status === 'fail'">&#10007;</span>
                        </span>
                        <span class="text-xs text-gray-700" x-text="c.label"></span>
                        <span class="text-xs text-gray-400" x-text="'— ' + c.detail"></span>
                    </div>
                </template>
            </div>

            {{-- Stats (polls) --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4" x-init="pollStats()">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Link stats</span>
                    <span class="text-[10px] text-gray-400">Auto-refreshes</span>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-900" x-text="stats.click_count"></div>
                        <div class="text-[11px] text-gray-500">Clicks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-900" x-text="stats.unique_clicks"></div>
                        <div class="text-[11px] text-gray-500">Unique</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-900" x-text="stats.redirects"></div>
                        <div class="text-[11px] text-gray-500">Redirects</div>
                    </div>
                </div>
            </div>

        </div>
    </template>

    {{-- Tip --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-xs text-blue-800">
        <strong>How it works:</strong> Every short link shows a preview page first so visitors can see the destination before clicking through. This protects against phishing and hidden redirects.
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
function smartUrlShortener() {
    return {
        url: '',
        slug: '',
        title: '',
        honeypot: '',
        loading: false,
        error: '',
        result: null,
        copied: false,
        stats: { click_count: 0, unique_clicks: 0, redirects: 0 },
        statsInterval: null,

        autoSlug() {
            const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            let s = '';
            for (let i = 0; i < 6; i++) s += chars[Math.floor(Math.random() * chars.length)];
            this.slug = s;
        },

        async create() {
            if (!this.url.trim()) return;
            this.error = '';
            this.loading = true;

            try {
                const res = await fetch('/tools/smart-url-shortener/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        url: this.url,
                        slug: this.slug || null,
                        title: this.title || null,
                        website_url: this.honeypot,
                    }),
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    this.error = data.message || data.errors?.url?.[0] || 'Something went wrong. Please try again.';
                    return;
                }

                this.result = data;
                this.$nextTick(() => this.renderQr());

            } catch (e) {
                this.error = 'Network error. Please check your connection and try again.';
            } finally {
                this.loading = false;
            }
        },

        copyResult() {
            if (!this.result) return;
            navigator.clipboard.writeText(this.result.short_url);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        createAnother() {
            if (this.statsInterval) clearInterval(this.statsInterval);
            this.url = '';
            this.slug = '';
            this.title = '';
            this.result = null;
            this.error = '';
            this.stats = { click_count: 0, unique_clicks: 0, redirects: 0 };
        },

        renderQr() {
            if (!this.result) return;
            const el = document.getElementById('sus-qr-target');
            if (!el || typeof QRCode === 'undefined') return;
            el.innerHTML = '';
            new QRCode(el, {
                text: this.result.short_url,
                width: 96,
                height: 96,
                colorDark: '#111827',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        },

        pollStats() {
            if (!this.result) return;
            const slug = this.result.slug;
            const fetchStats = async () => {
                try {
                    const res = await fetch('/tools/smart-url-shortener/stats/' + slug);
                    if (res.ok) this.stats = await res.json();
                } catch {}
            };
            fetchStats();
            this.statsInterval = setInterval(fetchStats, 10000);
        },
    };
}
</script>
@endpush
