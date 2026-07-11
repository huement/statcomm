@extends('statamic::layout')
@section('title', $title)

@section('content')
    <div class="min-h-screen bg-zinc-950 font-mono text-zinc-200">
        <!-- Top Bar -->
        <div
            style="
                background: rgba(0, 0, 0, 0.954);
                border-bottom: 1px solid rgba(84, 212, 255, 0.511);
            "
        >
            <div
                class="mx-auto flex max-w-screen-2xl items-center justify-between px-6 py-4"
            >
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded bg-gradient-to-br from-cyan-400 to-purple-500 shadow-[0_0_15px_-3px] shadow-cyan-400"
                        >
                            <span
                                class="text-xl font-bold tracking-tighter text-black"
                            >
                                SC
                            </span>
                        </div>
                        <div>
                            <h1
                                class="flex items-center gap-2 text-2xl font-bold tracking-tighter text-white"
                            >
                                STATCOMM
                                <span
                                    class="font-mono text-sm tracking-[4px] text-cyan-400 opacity-75"
                                >
                                    LOG_MONITOR_v0.8
                                </span>
                            </h1>
                            <p class="-mt-1 text-[10px] text-zinc-500">
                                NEURAL COMMENT AUDIT SYSTEM
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center gap-6 text-xs tracking-widest uppercase"
                >
                    <div class="flex items-center gap-1.5">
                        <div
                            class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"
                        ></div>
                        <span class="text-emerald-400">LIVE FEED ACTIVE</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-screen-2xl px-6 py-8">
            <!-- Back Link -->
            <div class="mb-8">
                <a
                    href="{{ cp_route('statcomm.index') }}"
                    class="group inline-flex items-center gap-2 font-mono text-xs tracking-widest text-zinc-400 transition-colors hover:text-cyan-400"
                    style="
                        background: rgba(68, 68, 68, 0.486);
                        border: 1px solid rgba(255, 255, 255, 0.232);
                        padding: 0.5rem 1rem;
                        border-radius: 0.5rem;
                    "
                >
                    <span
                        class="transition-transform group-hover:-translate-x-0.5"
                    >
                        ←
                    </span>
                    RETURN_TO_CORE_MONITOR
                </a>
            </div>

            <div class="mb-8 flex items-end justify-between">
                <div>
                    <h2
                        class="flex items-center gap-4 text-3xl font-bold tracking-tighter text-white"
                    >
                        <span class="text-cyan-400">//</span>
                        MODIFY TRANSMISSION PACKAGE
                    </h2>
                    <p class="mt-1 text-zinc-400">
                        Editing neural comment trace • ID #{{ $comment->id() }}
                    </p>
                </div>
            </div>

            <!-- Main Card -->
            <div
                class="overflow-hidden rounded-3xl border border-zinc-700/80 bg-zinc-900/70 shadow-2xl shadow-black/80 backdrop-blur-xl"
            >
                <div
                    class="border-b border-zinc-700 bg-gradient-to-r from-zinc-950 to-zinc-900 px-8 py-5"
                >
                    <h3
                        class="text-xs font-medium tracking-[2px] text-zinc-400 uppercase"
                    >
                        PAYLOAD EDITOR
                    </h3>
                </div>

                <form
                    action="{{ cp_route('statcomm.update', $comment->id()) }}"
                    method="POST"
                    class="space-y-8 p-8"
                >
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label
                            class="mb-3 block font-mono text-xs tracking-wider text-zinc-400 uppercase"
                        >
                            USER IDENTITY STRING
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $comment->get('name')) }}"
                            class="w-full rounded-2xl border border-zinc-700 bg-zinc-950 px-6 py-4 text-white placeholder-zinc-500 transition-all outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50"
                            autocomplete="off"
                        />
                        @error('name')
                            <p
                                class="mt-2 flex items-center gap-2 font-mono text-xs text-red-400"
                            >
                                ⚠ // ERR: {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Comment Field -->
                    <div>
                        <label
                            class="mb-3 block font-mono text-xs tracking-wider text-zinc-400 uppercase"
                        >
                            COMMENT BODY PAYLOAD
                        </label>
                        <textarea
                            name="comment"
                            rows="8"
                            class="w-full resize-y rounded-3xl border border-zinc-700 bg-zinc-950 px-6 py-5 font-sans leading-relaxed text-white placeholder-zinc-500 transition-all outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50"
                        >
{{ old('comment', $comment->get('comment')) }}</textarea
                        >
                        @error('comment')
                            <p
                                class="mt-2 flex items-center gap-2 font-mono text-xs text-red-400"
                            >
                                ⚠ // ERR: {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center gap-4 border-t border-zinc-800 pt-6"
                    >
                        <button
                            type="submit"
                            class="bg-success flex cursor-pointer items-center gap-2 px-8 py-4 text-sm font-medium tracking-widest text-black uppercase transition-all select-none hover:border-gray-700 hover:bg-green-500 hover:shadow-xl active:scale-95"
                            style="
                                background-color: rgba(104, 247, 154, 0.901);
                                border: 1px solid #ffffff54;
                                color: #000000;
                                border-radius: 12px;
                            "
                        >
                            <span>COMMIT CHANGES</span>
                            <span class="text-lg">⚡</span>
                        </button>

                        <a
                            href="{{ cp_route('statcomm.index') }}"
                            class="ml-4 rounded-2xl border border-zinc-700 px-8 py-4 text-sm tracking-widest text-zinc-400 uppercase transition-all hover:border-zinc-400 hover:text-white"
                        >
                            CANCEL TRANSMISSION
                        </a>
                    </div>
                </form>
            </div>

            <div class="mt-8 text-center text-[10px] text-zinc-600">
                CHANGES WILL BE PROPAGATED ACROSS THE NETWORK • INTEGRITY CHECK
                REQUIRED
            </div>
        </div>
    </div>
@endsection
