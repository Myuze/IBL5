<script lang="ts">
	import type { GameSummary as GameSummaryType } from '$lib/types/GameSummary';
	import { fade } from 'svelte/transition';

	interface Props {
		summary: GameSummaryType;
	}

	let { summary }: Props = $props();

	const homeTotal = $derived(summary.homeQuarterScores.reduce((total, score) => total + score, 0));
	const awayTotal = $derived(summary.awayQuarterScores.reduce((total, score) => total + score, 0));
	const hasOT = $derived(summary.homeQuarterScores.length > 4);
	const numColumns = $derived(summary.homeQuarterScores.length + 2); // quarters + logo + final
</script>

<div
	transition:fade={{ duration: 500 }}
	id="team-summary-container"
	class="mb-6 flex-col bg-base-100 text-center sm:text-xs"
>
	<h1 id="team-summary-title" class="pb-2 text-lg font-semibold">Game Summary</h1>
	<div class="mb-4 rounded-2xl bg-base-200 text-2xl font-bold shadow-md md:mx-auto md:max-w-[50%]">
		<div
			id="game-summary-grid"
			class="grid place-items-center gap-2"
			style="grid-template-columns: repeat({numColumns}, minmax(0, 1fr)); grid-template-rows: 1fr 2fr 2fr;"
		>
			<!-- Quarter headers - start from column 2 -->
			<div></div>
			<!-- Empty first column for team logos -->
			{#each summary.homeQuarterScores as _, index}
				{#if index < 4}
					<div id="header-q{index + 1}" class="text-sm font-semibold md:text-xl">Q{index + 1}</div>
				{:else}
					<div id="header-ot{index - 3}" class="text-sm font-semibold md:text-xl">
						OT{index > 4 ? index - 3 : ''}
					</div>
				{/if}
			{/each}
			<div id="header-final" class="text-sm font-semibold md:text-xl">Final</div>

			<!-- Home team -->
			<div id="home-team" class="text-sm md:text-2xl">
				<img src={summary.homeTeamLogoUrl} alt={summary.homeTeamName} />
			</div>
			{#each summary.homeQuarterScores as score, index}
				<div id="home-q{index + 1}" class="text-sm md:text-2xl">{score}</div>
			{/each}
			<div id="home-team-final" class="text-xl font-bold md:text-3xl">{homeTotal}</div>

			<!-- Away team -->
			<div id="away-team" class="text-sm md:text-2xl">
				<img src={summary.awayTeamLogoUrl} alt={summary.awayTeamName} />
			</div>
			{#each summary.awayQuarterScores as score, index}
				<div id="away-q{index + 1}" class="text-sm md:text-2xl">{score}</div>
			{/each}
			<div id="away-team-final" class="text-xl font-bold md:text-3xl">{awayTotal}</div>
		</div>
	</div>
</div>
