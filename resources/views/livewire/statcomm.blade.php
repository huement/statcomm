@php
    // Inspect the blueprint state to determine if fields require data input
    $formBlueprint = \Statamic\Facades\Form::find('blog_comments')?->blueprint();
    $isEmailRequired = $formBlueprint?->field('email')?->isRequired() ?? true;
@endphp

<div class="w-full mx-auto px-4 lg:px-0 mt-16 mb-12 font-mono text-zinc-100">
    @if (session()->has('success'))
        <div
            class="p-4 mb-6 text-xs uppercase tracking-widest border border-green-500/30 bg-green-950/20 text-green-400 [clip-path:polygon(0_0,100%_0,100%_calc(100%-8px),calc(100%-8px)_100%,0_100%)]">
            // SUCCESS_PROTOCOL_CLEARED: {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="p-4 mb-6 text-xs uppercase tracking-widest border border-red-500/30 bg-red-950/20 text-red-400 [clip-path:polygon(0_0,100%_0,100%_calc(100%-8px),calc(100%-8px)_100%,0_100%)]">
            // ERROR_INTAKE_HALTED: {{ session('error') }}
        </div>
    @endif

    <div class="border-l-4 border-cyber-yellow pl-3 flex items-center justify-between mb-6">
        <h4
            class="text-lg md:text-xl font-black uppercase tracking-widest text-white drop-shadow-[0_0_8px_rgba(255,0,85,0.2)]">
            Add Comment</h4>
        <div class="hidden sm:flex space-x-1.5 opacity-30">
            <span class="w-2 h-2 bg-cyber-green"></span>
            <span class="w-2 h-2 bg-cyber-cyan"></span>
        </div>
    </div>

    <div
        class="bg-zinc-900/50 border border-zinc-800 p-6 md:p-8 backdrop-blur-md relative shadow-2xl mb-16 [clip-path:polygon(0_0,100%_0,100%_calc(100%-15px),calc(100%-15px)_100%,0_100%)]">
        <div class="absolute top-0 right-0 w-3 h-3 border-t-2 border-r-2 border-zinc-700"></div>
        <div class="absolute bottom-0 left-0 w-3 h-3 border-b-2 border-l-2 border-cyber-cyan"></div>

        <form class="space-y-5" wire:submit.prevent="submit">
            <div class="hidden">
                <input type="text" wire:model="honeypot_field" tabindex="-1" autocomplete="off" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 items-center">
                <label class="col-span-12 sm:col-span-2 text-xs uppercase tracking-widest text-cyan-400 font-bold"
                    for="name">
                    Name
                </label>
                <div class="col-span-12 sm:col-span-10">
                    <input
                        class="block w-full bg-zinc-950 border-2 border-zinc-800 py-2.5 px-4 text-zinc-100 placeholder-zinc-700 focus:outline-none focus:border-cyber-cyan focus:shadow-[0_0_15px_rgba(34,211,238,0.2)] transition-all duration-300 text-sm rounded-none @error('name') border-red-500/50 @enderror"
                        id="name" type="text" wire:model.blur="name" placeholder="IDENT_STRING..." />
                    @error('name')
                        <span class="text-[10px] text-red-400 uppercase tracking-wider mt-1 block">// ERR:
                            {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 items-center">
                <label class="col-span-12 sm:col-span-2 text-xs uppercase tracking-widest text-cyan-400 font-bold"
                    for="email">
                    Email
                    @if ($isEmailRequired)
                        <span class="text-red-400">*</span>
                    @endif
                </label>
                <div class="col-span-12 sm:col-span-10">
                    <input
                        class="block w-full bg-zinc-950 border-2 border-zinc-800 py-2.5 px-4 text-zinc-100 placeholder-zinc-700 focus:outline-none focus:border-cyber-cyan focus:shadow-[0_0_15px_rgba(34,211,238,0.2)] transition-all duration-300 text-sm rounded-none @error('email') border-red-500/50 @enderror"
                        id="email" type="email" wire:model.blur="email" placeholder="NET_ADDRESS..." />
                    @error('email')
                        <span class="text-[10px] text-red-400 uppercase tracking-wider mt-1 block">// ERR:
                            {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 items-start">
                <label
                    class="col-span-12 sm:col-span-2 text-xs uppercase tracking-widest text-cyan-400 font-bold pt-2.5"
                    for="comment">
                    Comment
                </label>
                <div class="col-span-12 sm:col-span-10">
                    <textarea
                        class="block w-full bg-zinc-950 border-2 border-zinc-800 py-2.5 px-4 text-zinc-100 placeholder-zinc-700 focus:outline-none focus:border-cyber-cyan focus:shadow-[0_0_15px_rgba(34,211,238,0.2)] transition-all duration-300 text-sm resize-y rounded-none @error('comment') border-red-500/50 @enderror"
                        id="comment" wire:model.blur="comment" placeholder="INITIALIZE INPUT DATA STREAM..." rows="4"></textarea>
                    @error('comment')
                        <span class="text-[10px] text-red-400 uppercase tracking-wider mt-1 block">// ERR:
                            {{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-center pt-6 border-t border-zinc-800/40 mt-6">
                <button
                    class="w-full sm:w-auto relative inline-flex items-center justify-center gap-2 px-8 py-4 font-mono font-bold uppercase tracking-widest text-zinc-950 bg-gradient-to-r from-cyan-400 to-cyan-300 hover:from-fuchsia-500 hover:to-fuchsia-400 transition-all duration-300 rounded-none shadow-[0_0_15px_rgba(34,211,238,0.3)] hover:shadow-[0_0_20px_rgba(217,70,239,0.5)] cursor-pointer disabled:opacity-40 [clip-path:polygon(0_0,100%_0,100%_calc(100%-6px),calc(100%-6px)_100%,0_100%)] group"
                    type="submit" wire:loading.attr="disabled">
                    <span class="inline-flex items-center gap-2 group-hover:translate-x-0.5 transition-transform"
                        wire:loading.remove>
                        @svg('icons/emo-happy', 'w-4 h-4 fill-current shrink-0 text-zinc-950')
                        EXECUTE_SUBMIT_PROTOCOL ⚡
                    </span>
                    <span class="inline-flex items-center gap-2" wire:loading>
                        TRANSMITTING_DATA_PACKETS...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div class="w-full mt-16">
        <div class="border-l-4 border-cyber-yellow pl-3 flex items-center justify-between mb-6">
            <h4
                class="text-lg md:text-xl font-black uppercase tracking-widest text-white drop-shadow-[0_0_8px_rgba(255,0,85,0.2)]">
                All Comments</h4>
            <div class="hidden sm:flex space-x-1.5 opacity-30">
                <span class="w-2 h-2 bg-cyber-green"></span>
            </div>
        </div>

        <div class="space-y-4">
            @forelse ($comments as $comment)
                <div
                    class="bg-zinc-900/40 border border-zinc-800/80 p-3 sm:p-4 relative group hover:border-cyber-cyan hover:shadow-[0_0_15px_rgba(0,240,255,0.15)] transition-all duration-300 [clip-path:polygon(0_0,100%_0,100%_calc(100%-10px),calc(100%-10px)_100%,0_100%)]">
                    <div
                        class="absolute top-0 right-0 w-6 h-[1px] bg-zinc-700 group-hover:bg-cyber-cyan transition-colors">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-[1px] h-6 bg-zinc-700 group-hover:bg-cyber-cyan transition-colors">
                    </div>

                    <div class="flex items-start gap-3 sm:gap-4">
                        <div class="shrink-0">
                            <div
                                class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center font-mono text-xs sm:text-sm font-black text-cyber-pink tracking-tighter shadow-md select-none group-hover:text-cyber-cyan group-hover:border-cyber-cyan/50 transition-colors">
                                {{ (new \App\Helpers\ContentRenderer())->generateInitials($comment->get('name')) }}
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-y-0.5 sm:gap-x-3 mb-1.5">
                                <h6
                                    class="font-sans font-black text-sm text-zinc-100 uppercase tracking-tight truncate max-w-[180px] sm:max-w-none">
                                    {{ $comment->get('name') }}
                                </h6>
                                <span
                                    class="text-[9px] sm:text-[10px] text-cyber-cyan font-mono font-bold tracking-wider">
                                    // COMM_TIMESTAMP: {{ $comment->date()->format('M j, Y') }}
                                </span>
                            </div>

                            <div
                                class="comment-content font-sans text-xs sm:text-sm text-zinc-300 leading-relaxed tracking-normal break-words whitespace-pre-line">
                                {{ $comment->get('comment') }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="border border-dashed border-zinc-800 bg-zinc-900/10 text-center py-10 px-4 [clip-path:polygon(0_0,100%_0,100%_calc(100%-12px),calc(100%-12px)_100%,0_100%)]">
                    @svg('icons/emo-happy', 'w-10 h-10 text-cyber-yellow mx-auto mb-3 animate-pulse')
                    <h4 class="text-sm font-black text-white uppercase tracking-wider">// NO_COMMENTS_IN_BUFFER</h4>
                    <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1 max-w-sm mx-auto">
                        Establish initialization protocol by creating the baseline entry trace.
                    </p>
                </div>
            @endforelse

            @if ($comments->hasPages())
                <div class="mt-8 pt-4 border-t border-zinc-900/60 statcomm-pagination-nav">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="w-full mt-6">
        <div
            class="bg-zinc-950 border border-zinc-900/60 p-3 sm:p-4 text-[10px] sm:text-[11px] text-zinc-500 uppercase tracking-wider leading-relaxed flex items-start gap-2.5 sm:gap-3">
            <span
                class="inline-flex items-center justify-center w-2 h-2 rounded-full bg-cyber-yellow mt-1 animate-pulse shrink-0"></span>
            <p class="min-w-0 flex-1">
                System Moderation Interceptor is <strong class="text-cyber-yellow font-bold">ON</strong> for this
                communication
                hub. All user transmission vectors must clear access protocol filtration approvals before broadcasting
                logs live
                to public network arrays.
            </p>
        </div>
    </div>
</div>
