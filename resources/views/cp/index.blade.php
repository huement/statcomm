@extends("statamic::layout")
@section("title", $title)

@section("content")
@php
// Server-side data calculation processing driven by the live comments array stream
$totalLogs = $comments->count();
$avgChars = $totalLogs > 0 ? round($comments->reduce(fn($carry, $c) => $carry + strlen($c->get('comment') ?? ''), 0) / $totalLogs) : 0;
$latestComment = $comments->first();

// Group allocations for visual breakdown metrics
$minimalCount = $comments->filter(fn($c) => strlen($c->get('comment') ?? '') < 20)->count();
  $complexCount = $totalLogs - $minimalCount;
  @endphp

  <div v-pre class="min-h-screen w-full -m-6 p-6 space-y-6" style="background-color: #18181b; color: #d4d4d8; font-family: ui-monospace, monospace; letter-spacing: -0.01em; box-sizing: border-box;">

    {{-- Header Panel --}}
    <header class="rounded-2xl border border-zinc-800/80 shadow-2xl p-6" style="background-color: rgba(14, 14, 17, 0.4); border: 1px solid #1f1f23;">
      <div class="w-full flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center">
          <div style="max-width:240px; height:auto; width:100%; min-width:200px;" class="block select-none">
            <svg id="Layer_Logo" style="fill:#FFF;" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 439.75 112.87">
              <g id="Layer_2-2" data-name="Layer 2">
                <g>
                  <g>
                    <path style="fill: #ffffff;" d="M42.9,8.58H8.58v8.58h34.32v25.74H0v-8.58h34.32v-8.58H0V0h42.9v8.58Z" />
                    <path style="fill: #ffffff;" d="M56.63,0h42.9v8.58h-17.16v34.32h-8.58V8.58h-17.16V0Z" />
                    <path style="fill: #ffffff;" d="M147.59,25.74V8.58h-25.74v34.32h-8.58V0h42.9v42.9h-8.58v-8.58h-17.16v-8.58h17.16Z" />
                    <path style="fill: #ffffff;" d="M169.9,0h42.9v8.58h-17.16v34.32h-8.58V8.58h-17.16V0Z" />
                    <path style="fill: #ffffff;" d="M226.53,0h42.9v8.58h-34.32v25.74h34.32v8.58h-42.9V0Z" />
                    <path style="fill: #ffffff;" d="M326.06,0v42.9h-42.9V0h42.9ZM291.74,8.58v25.74h25.74V8.58h-25.74Z" />
                    <path style="fill: #ffffff;" d="M339.79,0h42.9v42.9h-8.58V8.58h-8.58v34.32h-8.58V8.58h-8.58v34.32h-8.58V0Z" />
                    <path style="fill: #ffffff;" d="M396.42,0h42.9v42.9h-8.58V8.58h-8.58v34.32h-8.58V8.58h-8.58v34.32h-8.58V0Z" />
                  </g>
                  <g>
                    <rect style="fill: #ffffff;" x="73.94" y="74.11" width="19.46" height="19.38" />
                    <path style="fill: #ffffff;" d="M385.27,54.73H0v58.14h439.75v-58.14h-54.48ZM45.4,74.11h-25.94v19.38h25.94v6.46H12.97v-32.3h32.43v6.46ZM99.88,99.95h-32.43v-32.3h32.43v32.3ZM154.37,99.95h-6.49v-25.84h-6.49v25.84h-6.49v-25.84h-6.49v25.84h-6.49v-32.3h32.43v32.3ZM208.85,99.95h-6.49v-25.84h-6.49v25.84h-6.49v-25.84h-6.49v25.84h-6.49v-32.3h32.43v32.3ZM263.33,99.95h-32.43v-6.46h32.43v6.46ZM263.33,87.03h-32.43v-6.46h32.43v6.46ZM263.33,74.11h-32.43v-6.46h32.43v6.46ZM317.81,99.95h-6.49v-25.84h-19.46v25.84h-6.49v-32.3h32.43v32.3ZM372.3,74.11h-12.97v25.84h-6.49v-25.84h-12.97v-6.46h32.43v6.46ZM426.78,74.11h-25.94v6.46h25.94v19.38h-32.43v-6.46h25.94v-6.46h-25.94v-19.38h32.43v6.46Z" />
                  </g>
                </g>
              </g>
            </svg>
          </div>
        </div>

        <div class="flex flex-col items-end space-y-1">
          <span class="font-mono text-xs font-semibold uppercase tracking-wider" style="color: #06b6d4;">Sponsored By:</span>
          <a href="https://huement.com" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-xs font-bold text-zinc-300 transition-all font-mono hover:text-cyan-400 group">
            <span>HUEMENT</span>
            <svg class="w-3.5 h-3.5 text-zinc-500 group-hover:text-cyan-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
          </a>
        </div>
      </div>
    </header>

    {{-- System Response Notice Alerts --}}
    @if (session("success"))
    <div class="p-4 bg-zinc-900 border border-emerald-500/30 text-zinc-200 rounded-xl shadow-xl flex items-center space-x-3 text-xs">
      <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" style="height:10px;width:10px;margin-left:16px"></span>
      <span>// SUCCESS_PROTOCOL_CLEARED: {{ session("success") }}</span>
    </div>
    @endif

    {{-- Metric Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full" style="box-sizing: border-box; margin-bottom: 1.5rem;">

      {{-- Card 1 --}}
      <div style="background-color: rgba(14, 14, 17, 0.4); border: 1px solid #1f1f23; border-radius: 12px; padding: 24px; box-sizing: border-box; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#3f3f46'" onmouseout="this.style.borderColor='#1f1f23'">
        <div class="flex justify-between items-center w-full mb-4">
          <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-8 h-8 rounded" style="background-color: rgba(6, 182, 212, 0.1); color: #06b6d4;">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" /></svg>
            </div>
            <span class="text-xs font-mono" style="color: #71717a; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
              Volumetrics
            </span>
          </div>
        </div>
        <p class="text-xs uppercase tracking-wide mb-2" style="color: #71717a; font-weight: 700;">Total Items</p>
        <div class="flex items-baseline gap-2">
          <span style="font-size: 2.25rem; line-height: 2.25rem; font-weight: 800; color: #06b6d4;">{{ $totalLogs }}</span>
          <span class="text-xs font-mono" style="color: #71717a;">{{ Str::plural('post', $totalLogs) }}</span>
        </div>
      </div>

      {{-- Card 2 --}}
      <div style="background-color: rgba(14, 14, 17, 0.4); border: 1px solid #1f1f23; border-radius: 12px; padding: 24px; box-sizing: border-box; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#3f3f46'" onmouseout="this.style.borderColor='#1f1f23'">
        <div class="flex justify-between items-center w-full mb-4">
          <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-8 h-8 rounded" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </div>
            <span class="text-xs font-mono" style="color: #71717a; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
              Density
            </span>
          </div>
        </div>
        <p class="text-xs uppercase tracking-wide mb-2" style="color: #71717a; font-weight: 700;">Average Characters</p>
        <div class="flex items-baseline gap-2">
          <span style="font-size: 2.25rem; line-height: 2.25rem; font-weight: 800; color: #3b82f6;">{{ $avgChars }}</span>
          <span class="text-xs font-mono" style="color: #71717a;">symbols</span>
        </div>
      </div>

      {{-- Card 3 --}}
      <div style="background-color: rgba(14, 14, 17, 0.4); border: 1px solid #1f1f23; border-radius: 12px; padding: 24px; box-sizing: border-box; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#3f3f46'" onmouseout="this.style.borderColor='#1f1f23'">
        <div class="flex justify-between items-center w-full mb-4">
          <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-8 h-8 rounded" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <span class="text-xs font-mono" style="color: #71717a; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
              Timespan
            </span>
          </div>
        </div>
        <p class="text-xs uppercase tracking-wide mb-2" style="color: #71717a; font-weight: 700;">Latest Comment</p>
        <div class="flex items-baseline gap-2">
          <span style="font-size: 2.25rem; line-height: 2.25rem; font-weight: 800; color: #10b981;">
            {{ $latestComment ? $latestComment->date()->diffForHumans(['short' => true]) : 'NULL' }}
          </span>
        </div>
      </div>

    </div>

    {{-- Main Table Area Card Canvas --}}
    <div class="overflow-hidden shadow-2xl" style="background-color: rgba(14, 14, 17, 0.4); border: 1px solid #1f1f23; border-radius: 1rem;">

      <div class="p-5 border-b border-zinc-800 bg-zinc-950/40 flex flex-col md:flex-row gap-4 items-center justify-between" style="height:auto;min-height:50px;">
        <div class="flex items-center space-x-3">
          <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse" style="height:10px;width:10px;margin-left:16px"></span>
          <span class="text-xs font-bold uppercase tracking-wider text-zinc-300">Active Buffer Log Telemetry</span>
        </div>

        {{-- <div class="flex items-center space-x-3 text-xs">
          <div class="relative opacity-30">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-500">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
              </svg>
            </span>
            <input type="text" disabled placeholder="Search user, email or payload..." class="pl-9 pr-4 py-1.5 rounded-xl text-xs w-56 cursor-not-allowed" style="background-color: #020205; border: 1px solid #27272a; color: #e4e4e7;" />
          </div>
          <div class="px-3 py-1.5 rounded-xl opacity-30 text-xs flex items-center space-x-2 select-none cursor-not-allowed" style="background-color: #020205; border: 1px solid #27272a; color: #e4e4e7;">
            <span>All Sizes</span>
          </div>
        </div> --}}
      </div>

      {{-- <div class="px-6 py-2.5 bg-zinc-900/20 border-b border-zinc-800 flex items-center space-x-2 text-xs text-zinc-500">
        <span>Bulk Control:</span>
        <button type="button" class="text-cyan-400 hover:underline font-medium px-1">Select All</button>
        <span>|</span>
        <button type="button" class="text-zinc-400 hover:underline font-medium px-1">Select None</button>
      </div> --}}

      @if ($comments->isEmpty())
      <div class="p-24 flex flex-col items-center justify-center text-center">
        <svg class="w-8 h-8 text-cyan-500/30 mx-auto mb-3 animate-pulse" style="height:20px;width:20px;margin-left:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
        <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">// NO_ACTIVE_DATA_TRAILS_FOUND_IN_BUFFER</p>
        <p class="text-zinc-600 mt-1 text-[11px]">Establish data trail arrays within your public site vectors to populate logs.</p>
      </div>
      @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr>
              <th class="py-4 px-6 w-12 text-center" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #71717a; text-transform: uppercase; border-bottom: 1px solid #1f1f23;">
                <input type="checkbox" disabled class="rounded bg-zinc-950 border-zinc-800 text-cyan-500 opacity-20 w-4 h-4 cursor-not-allowed" />
              </th>
              <th class="py-4 px-4" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #71717a; text-transform: uppercase; border-bottom: 1px solid #1f1f23;">Timestamp</th>
              <th class="py-4 px-4" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #71717a; text-transform: uppercase; border-bottom: 1px solid #1f1f23;">User Ident</th>
              <th class="py-4 px-4" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #71717a; text-transform: uppercase; border-bottom: 1px solid #1f1f23;">Email Routing</th>
              <th class="py-4 px-4" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #71717a; text-transform: uppercase; border-bottom: 1px solid #1f1f23;">Data Payload</th>
              <th class="py-4 px-4 text-center" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #71717a; text-transform: uppercase; border-bottom: 1px solid #1f1f23;">Character Count</th>
              <th class="py-4 px-6 text-right" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #71717a; text-transform: uppercase; border-bottom: 1px solid #1f1f23;">Mod_Controls</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-800/40 text-xs text-zinc-300">
            @foreach ($comments as $comment)
            @php
            $charLength = strlen($comment->get('comment') ?? '');

            // Inline Status Badge Processing System Matrix
            if ($charLength <= 20) { $badgeStyle='color: #10b981; background-color: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2);' ; } elseif ($charLength <=40) { $badgeStyle='color: #06b6d4; background-color: rgba(6, 182, 212, 0.1); border-color: rgba(6, 182, 212, 0.2);' ; } else { $badgeStyle='color: #f59e0b; background-color: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);' ; } @endphp <tr class="group transition-all duration-150 hover:bg-zinc-950" style="{{ $loop->even ? 'background-color: rgba(10,10,10,0.3)' : 'background-color: rgba(30,30,30,0.3)' }}">
              <td class="py-4 px-6 text-center">
                <input type="checkbox" class="rounded bg-zinc-950 border-zinc-800 text-cyan-500 focus:ring-cyan-500/20 w-4 h-4 cursor-pointer" />
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-zinc-400">
                <span>{{ $comment->date()->format("Y-m-d") }}</span>
                <span class="text-zinc-600 ml-1 text-[11px]">{{ $comment->date()->format("H:i:s") }}</span>
              </td>

              <td class="py-4 px-4 whitespace-nowrap">
                <div class="flex items-center space-x-2.5">
                  <div class="w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700/60 flex items-center justify-center text-xs font-bold select-none" style="color: #06b6d4;">
                    {{ collect(explode(' ', $comment->get('name', '??')))->map(fn($w) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($w, 0, 1)))->take(2)->implode('') }}
                  </div>
                  <span class="font-semibold text-zinc-200 group-hover:text-white transition-colors">
                    {{ $comment->get("name") }}
                  </span>
                </div>
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-zinc-400 font-mono">
                {{ $comment->get("email") }}
              </td>

              <td class="py-4 px-4 max-w-xs md:max-w-md truncate text-zinc-300 group-hover:text-zinc-100 transition-colors font-mono">
                <span class="text-zinc-600 italic select-none">“</span>
                {{ Str::limit($comment->get("comment"), 180) }}
                <span class="text-zinc-600 italic select-none">”</span>
              </td>

              <td class="py-4 px-4 whitespace-nowrap text-center">
                <span class="inline-block text-center whitespace-nowrap" style="font-size: 10px; font-weight: 700; padding: 0.25rem 0.625rem; border-radius: 6px; border: 1px solid transparent; {{ $badgeStyle }}">
                  {{ $charLength }} Chars
                </span>
              </td>

              <td class="py-4 px-6 whitespace-nowrap text-right">
                <div class="inline-flex items-center justify-end space-x-2 opacity-40 group-hover:opacity-100 transition-all duration-200">
                  {{-- ⚡ MODERATION INTERCEPTOR BUTTON --}}
                  @if(! $comment->get('approved'))
                  <form action="{{ cp_route('statcomm.approve', $comment->id()) }}" method="POST" class="inline-block my-0 ml-0 mr-1 p-0">
                    @csrf
                    <button type="submit" title="Authorize for broadcast" class="p-1.5 rounded-lg bg-zinc-800 hover:bg-emerald-500/20 text-emerald-400 border border-zinc-700 hover:border-emerald-500/40 transition-all cursor-pointer">
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                  </form>
                  @else
                  <span class="text-[9px] font-mono text-emerald-500/60 bg-emerald-950/10 border border-emerald-500/20 px-2 py-0.5 uppercase tracking-wider select-none rounded my-0 ml-0 mr-1">
                    Live
                  </span>
                  @endif

                  <a href="{{ cp_route("statcomm.edit", $comment->id()) }}" title="Edit parameters" class="p-1.5 rounded-lg bg-zinc-800 hover:bg-cyan-500/20 border border-zinc-700 hover:border-cyan-500/40 transition-all inline-block" style="color: #06b6d4;">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                  </a>

                  <form action="{{ cp_route("statcomm.destroy", $comment->id()) }}" method="POST" onsubmit="return confirm('WARNING: Execute absolute trace scrub on this packet?')" class="inline-block m-0 p-0">
                    @csrf
                    @method("DELETE")
                    <button type="submit" title="Scrub trace packet" class="p-1.5 rounded-lg bg-zinc-800 hover:bg-red-500/20 text-red-400 border border-zinc-700 hover:border-red-500/40 transition-all cursor-pointer">
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </form>
                </div>
              </td>
              </tr>
              @endforeach
          </tbody>
        </table>
      </div>
      @endif

      {{-- Footer Metrics Area Section --}}
      <div class="p-4 bg-zinc-950/60 border-t border-zinc-800 flex flex-col sm:flex-row gap-4 items-center justify-between text-xs text-zinc-500">
        <div>
          View Total: <strong class="text-zinc-300">{{ $totalLogs }}</strong>
        </div>
        <div class="flex flex-wrap gap-x-2 gap-y-2">
          <span class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Post (&lt;20 Chars): <span style="color: #06b6d4;">{{ $minimalCount }}</span></span>
          </span>
          <span class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            <span>Complex (&gt;=20 Chars): <span style="color: #10b981;">{{ $complexCount }}</span></span>
          </span>
        </div>
      </div>
    </div>

    {{-- System Disclaimer Status Flag Box --}}
    <div class="w-full">
      <div class="p-2 sm:p-1 text-xs sm:text-md text-zinc-500 uppercase tracking-wider leading-relaxed flex items-start gap-2 sm:gap-1" style="background-color: rgba(14, 14, 17, 0.4);">
        <span class="inline-flex items-center justify-center w-2 h-2 rounded-full bg-yellow-400 mt-1 animate-pulse shrink-0"></span>
        <p class="min-w-0 flex-1">
          Statcomm Telemetry Logs are transient in-memory. Sponsor outbound link connects safely via HTTPS.
        </p>
      </div>
    </div>

  </div>
  @endsection
