@extends('statamic::layout')
@section('title', $title)

@section('content')
    <div class="min-h-screen bg-zinc-950 text-zinc-200 font-mono">
        <!-- Top Bar -->
        <div class="border-b border-cyan-500/30 bg-black/80 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-screen-2xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-gradient-to-br from-cyan-400 to-purple-500 flex items-center justify-center shadow-[0_0_15px_-3px] shadow-cyan-400">
                            <span class="text-black text-xl font-bold tracking-tighter">SC</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tighter text-white flex items-center gap-2">
                                STATCOMM
                                <span class="text-cyan-400 text-sm font-mono tracking-[4px] opacity-75">LOG_MONITOR_v0.8</span>
                            </h1>
                            <p class="text-[10px] text-zinc-500 -mt-1">NEURAL COMMENT AUDIT SYSTEM</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-6 text-xs uppercase tracking-widest">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="text-emerald-400">LIVE FEED ACTIVE</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-screen-2xl mx-auto px-6 py-8">
            <!-- Back Link -->
            <div class="mb-8">
                <a href="{{ cp_route('statcomm.index') }}" 
                   class="inline-flex items-center gap-2 text-xs text-zinc-400 hover:text-cyan-400 transition-colors font-mono tracking-widest group">
                    <span class="group-hover:-translate-x-0.5 transition-transform">←</span>
                    RETURN_TO_CORE_MONITOR
                </a>
            </div>

            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold tracking-tighter text-white flex items-center gap-4">
                        <span class="text-cyan-400">//</span> 
                        MODIFY TRANSMISSION PACKAGE
                    </h2>
                    <p class="text-zinc-400 mt-1">Editing neural comment trace • ID #{{ $comment->id() }}</p>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-zinc-900/70 border border-zinc-700/80 rounded-3xl overflow-hidden shadow-2xl shadow-black/80 backdrop-blur-xl">
                <div class="bg-gradient-to-r from-zinc-950 to-zinc-900 px-8 py-5 border-b border-zinc-700">
                    <h3 class="uppercase text-xs tracking-[2px] text-zinc-400 font-medium">PAYLOAD EDITOR</h3>
                </div>

                <form action="{{ cp_route('statcomm.update', $comment->id()) }}" method="POST" class="p-8 space-y-8">
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-zinc-400 block mb-3">
                            USER IDENTITY STRING
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $comment->get('name')) }}" 
                            class="w-full bg-zinc-950 border border-zinc-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 rounded-2xl px-6 py-4 text-white placeholder-zinc-500 transition-all outline-none"
                            autocomplete="off"
                        />
                        @error('name')
                            <p class="text-xs text-red-400 font-mono mt-2 flex items-center gap-2">
                                ⚠ // ERR: {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Comment Field -->
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-zinc-400 block mb-3">
                            COMMENT BODY PAYLOAD
                        </label>
                        <textarea 
                            name="comment" 
                            rows="8" 
                            class="w-full bg-zinc-950 border border-zinc-700 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 rounded-3xl px-6 py-5 text-white placeholder-zinc-500 resize-y font-sans leading-relaxed transition-all outline-none"
                        >{{ old('comment', $comment->get('comment')) }}</textarea>
                        @error('comment')
                            <p class="text-xs text-red-400 font-mono mt-2 flex items-center gap-2">
                                ⚠ // ERR: {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t border-zinc-800 flex items-center gap-4">
                        <button 
                            type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-cyan-500 to-purple-500 hover:from-cyan-400 hover:to-purple-400 text-black font-medium rounded-2xl transition-all hover:shadow-xl hover:shadow-cyan-500/30 flex items-center gap-2 text-sm uppercase tracking-widest">
                            <span>COMMIT CHANGES</span>
                            <span class="text-lg">⚡</span>
                        </button>
                        
                        <a href="{{ cp_route('statcomm.index') }}" 
                           class="px-8 py-4 border border-zinc-700 hover:border-zinc-400 text-zinc-400 hover:text-white rounded-2xl transition-all text-sm uppercase tracking-widest">
                            CANCEL TRANSMISSION
                        </a>
                    </div>
                </form>
            </div>

            <div class="mt-8 text-center text-[10px] text-zinc-600">
                CHANGES WILL BE PROPAGATED ACROSS THE NETWORK • INTEGRITY CHECK REQUIRED
            </div>
        </div>
    </div>
@endsection