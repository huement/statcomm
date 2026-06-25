<div
    class="w-full bg-[#09090e]/80 border border-zinc-900 p-4 font-mono select-none relative [clip-path:polygon(0_0,100%_0,100%_calc(100%-10px),calc(100%-10px)_100%,0_100%)]">
    <div class="absolute top-0 left-0 w-12 h-[1px] bg-cyan-400/60"></div>

    <div class="flex items-center justify-between border-b border-zinc-900 pb-3 mb-4">
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-pulse shadow-[0_0_6px_#22d3ee]"></span>
            @if (isset($heading) && trim($heading) !== "")
                <h4 class="text-xs font-black tracking-[2px] text-white uppercase">{{ $heading }}</h4>
            @else
                <h4 class="text-xs font-black tracking-[2px] text-white uppercase">RECENT_COMM_FEED</h4>
            @endif
        </div>
        <span class="text-[9px] text-zinc-600">SYS_BUFF_v0.8</span>
    </div>

    <div class="space-y-3.5">
        @forelse($recentComments as $item)
            <div
                class="group relative border-l-2 border-zinc-800 hover:border-fuchsia-500 pl-3 transition-colors duration-150">
                <div class="flex items-center justify-between text-[9px] tracking-wider text-zinc-500 mb-1">
                    <span class="font-bold text-zinc-300 uppercase group-hover:text-cyan-400 transition-colors">
                        // {{ Str::limit($item["name"], 14) }}
                    </span>

                    @if ($showDate)
                        <span>{{ $item["date"]->diffForHumans(["short" => true]) }}</span>
                    @endif
                </div>

                <p class="font-sans text-xs text-zinc-400 leading-snug mb-1.5 break-words line-clamp-2">
                    "{{ $item["comment"] }}"
                </p>

                <a href="{{ $item["post_url"] }}"
                    class="inline-flex items-center gap-1 text-[9px] text-fuchsia-400/80 hover:text-fuchsia-400 uppercase tracking-widest font-bold group/link transition-colors">
                    <span>SRC_NODE:</span>
                    <span
                        class="text-zinc-500 underline decoration-zinc-800 group-hover/link:text-zinc-300 truncate max-w-[140px]">
                        {{ $item["post_title"] }}
                    </span>
                    <span
                        class="opacity-0 group-hover/link:opacity-100 group-hover/link:translate-x-0.5 transition-all">➔</span>
                </a>
            </div>
        @empty
            <div
                class="py-6 text-center text-zinc-600 text-[10px] uppercase tracking-widest border border-dashed border-zinc-900/60">
                // DATA_STREAM_EMPTY
            </div>
        @endforelse
    </div>
</div>
