<script lang="ts">
    import type { TeamSummary as TeamSummaryType } from '$lib/types/TeamSummary';
    
    interface Props {
        summary: TeamSummaryType;
    }
    
    let { summary }: Props = $props();
    
    const homeTotal = $derived(summary.homeQuarterScores.reduce((total, score) => total + score, 0));
    const awayTotal = $derived(summary.awayQuarterScores.reduce((total, score) => total + score, 0));

    $effect(() => {
        console.log('📊 TeamSummary received:', summary);
        console.log('Home Total:', homeTotal);
        console.log('Away Total:', awayTotal);
    });
</script>

<div id='team-summary-container' class="flex-col sm:text-xs text-center mb-6 bg-base-100 shadow-md">
    <h1 id='team-summary-title' class="text-lg font-semibold pb-2">Game Summary</h1>
    <div class="text-2xl font-bold mb-4 rounded-2xl bg-base-200 shadow-md">
        <div class="grid grid-cols-6 grid-rows-[1fr_2fr] gap-2 place-items-center">
            <!-- Quarter headers -->
            <div id="header-q1" class="font-semibold text-sm md:text-xl col-start-2 row-start-1">1</div>
            <div id="header-q2" class="font-semibold text-sm md:text-xl col-start-3 row-start-1">2</div>
            <div id="header-q3" class="font-semibold text-sm md:text-xl col-start-4 row-start-1">3</div>
            <div id="header-q4" class="font-semibold text-sm md:text-xl col-start-5 row-start-1">4</div>
            <div id="header-final" class="font-semibold text-sm md:text-xl col-start-6 row-start-1">Final</div>
            
            <!-- Home team -->
            <div id="home-team" class="text-sm md:text-2xl row-start-2">
                <img src="{summary.homeTeamLogoUrl}" alt="{summary.homeTeamName}" />
            </div>
            {#each summary.homeQuarterScores as score}
                <div class="text-sm md:text-2xl row-start-2">{score}</div>
            {/each}
            <div id="home-team-final" class="font-bold md:text-5xl row-start-2">{homeTotal}</div>
            
            <!-- Away team -->
            <div id="away-team" class="text-sm row-start-3">
                <img src="{summary.awayTeamLogoUrl}" alt="{summary.awayTeamName}" />
            </div>
            {#each summary.awayQuarterScores as score}
                <div class="text-sm md:text-2xl row-start-3">{score}</div>
            {/each}
            <div id="away-team-final" class="font-bold md:text-5xl row-start-3">{awayTotal}</div>
        </div>
    </div>
</div>

