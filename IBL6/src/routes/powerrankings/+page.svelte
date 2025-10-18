<script lang="ts">
	import type { PageData } from './$types';

	let { data }: { data: PageData } = $props();

	// CSS Classes
	const prHeaderClass = 'p-2 text-center text-3xl font-bold';
	const prContainerClass =
		'grid grid-cols-[1fr_8fr] items-center sm:grid-cols-[1fr_10fr_repeat(4,2fr)] sm:grid-rows-auto md:mx-5 lg:mx-[20%]';
	const prTeamContainerClass = '';
	const prTeamRankClass = 'mx-auto text-lg font-semibold col-start-1';
	const prAnchorClass = 'mx-auto';
	const prTeamImageClass = 'object-contain mb-2';
	const prRecordHeaderClass = 'mx-auto hidden sm:block font-bold';
	const prRecordItemClass = 'mx-auto hidden sm:block';
</script>

<div id="power-ranking">
	<h1 class={prHeaderClass}>Power Rankings</h1>
	<div id="power-ranking-container" class={prContainerClass}>
		{#each data.powerRankings as ranking, index}
			{#if index === 0}
				<h2 class="col-start-1 mx-auto text-lg font-semibold">Rank</h2>
				<div class={`col-start-2 mx-auto text-lg font-semibold`}>Team</div>
				<div class={`${prRecordHeaderClass} col-start-3`}>Record</div>
				<div class={`${prRecordHeaderClass} col-start-4`}>Home</div>
				<div class={`${prRecordHeaderClass} col-start-5`}>Away</div>
				<div class={`${prRecordHeaderClass} col-start-6`}>Rating</div>
			{/if}
			<h2 class={prTeamRankClass}>{index + 1}.</h2>
			<a
				href={`https://iblhoops.net/ibl5/modules.php?name=Team&op=team&teamID=${ranking.TeamID}`}
				class={prAnchorClass}
			>
				<div id="team-container" class={prTeamContainerClass}>
					<img
						src={`teambanner/${ranking.TeamID}.jpg`}
						alt={ranking.Team}
						class={prTeamImageClass}
					/>
				</div>
			</a>
			<div id="record-win-loss" class={prRecordItemClass}>
				<span>{ranking.win}</span>-<span>{ranking.loss}</span>
			</div>
			<div id="record-home-win-loss" class={prRecordItemClass}>
				<span>{ranking.home_win}</span>-<span>{ranking.home_loss}</span>
			</div>
			<div id="record-road-win-loss" class={prRecordItemClass}>
				<span>{ranking.road_win}</span>-<span>{ranking.road_loss}</span>
			</div>
			<div id="record-ranking" class={prRecordItemClass}>
				<span>{ranking.ranking}</span>
			</div>
		{/each}
	</div>
</div>
