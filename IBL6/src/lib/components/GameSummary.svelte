<script lang="ts">
    import type { GameSummary as GameSummaryType } from '$lib/types/GameSummary';
    
    interface Props {
        summary: GameSummaryType;
    }
    
    let { summary }: Props = $props();
    
    const homeTotal = $derived(summary.homeQuarterScores.reduce((total, score) => total + score, 0));
    const awayTotal = $derived(summary.awayQuarterScores.reduce((total, score) => total + score, 0));
    const hasOT = $derived(summary.homeQuarterScores.length > 4);
    const numColumns = $derived(summary.homeQuarterScores.length + 2); // quarters + logo + final
</script>

<div id='team-summary-container' class="flex-col sm:text-xs text-center mb-6 bg-base-100">
    <h1 id='team-summary-title' class="text-lg font-semibold pb-2">Game Summary</h1>
    <div class="text-2xl md:max-w-[50%] md:mx-auto font-bold mb-4 rounded-2xl bg-base-200 shadow-md">
        <div class="grid gap-2 place-items-center" style="grid-template-columns: repeat({numColumns}, minmax(0, 1fr)); grid-template-rows: 1fr 2fr 2fr;">
            <!-- Quarter headers - start from column 2 -->
            <div></div> <!-- Empty first column for team logos -->
            {#each summary.homeQuarterScores as _, index}
                {#if index < 4}
                    <div id="header-q{index + 1}" class="font-semibold text-sm md:text-xl">Q{index + 1}</div>
                {:else}
                    <div id="header-ot{index - 3}" class="font-semibold text-sm md:text-xl">OT{index > 4 ? index - 3 : ''}</div>
                {/if}
            {/each}
            <div id="header-final" class="font-semibold text-sm md:text-xl">Final</div>
            
            <!-- Home team -->
            <div id="home-team" class="text-sm md:text-2xl">
                <img src="{summary.homeTeamLogoUrl}" alt="{summary.homeTeamName}" />
            </div>
            {#each summary.homeQuarterScores as score}
                <div class="text-sm md:text-2xl">{score}</div>
            {/each}
            <div id="home-team-final" class="font-bold text-xl md:text-3xl">{homeTotal}</div>
            
            <!-- Away team -->
            <div id="away-team" class="text-sm md:text-2xl">
                <img src="{summary.awayTeamLogoUrl}" alt="{summary.awayTeamName}" />
            </div>
            {#each summary.awayQuarterScores as score}
                <div class="text-sm md:text-2xl">{score}</div>
            {/each}
            <div id="away-team-final" class="font-bold text-xl md:text-3xl">{awayTotal}</div>
        </div>
    </div>
</div>

