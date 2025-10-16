<script lang="ts">
	import type { PageData } from './$types';

	let { data }: { data: PageData } = $props();

	console.log(data);
	const prContainerClass = 'grid grid-cols-[1fr_8fr] justify-items-center items-center';
	const prTeamContainerClass = '';
	const prAnchorClass = '';
	const prTeamImageClass = 'w-[90%] object-contain mb-2 md:max-w-[50%]';
	const prRecordSectionClass = 'hidden';
	const prRecordHeaderClass = '';
	const prRecordItemClass = '';
</script>

<h1 class="p-2 text-center text-3xl font-bold">Power Rankings</h1>
<div class={prContainerClass}>
	{#each data.powerRankings as ranking, index}
		<h2 class="items-center pr-2 text-lg font-semibold">{index + 1}.</h2>
		<a
			href={`https://iblhoops.net/ibl5/modules.php?name=Team&op=team&teamID=${ranking.TeamID}`}
			class={prAnchorClass}
		>
			<!-- <div id="team-container" class={prTeamContainerClass}> -->
			<img
				src={`https://iblhoops.net/ibl5/images/logo/${ranking.TeamID}.jpg`}
				alt={ranking.Team}
				class={prTeamImageClass}
			/>
			<!-- </div> -->
		</a>
		<div id="record-container" class={prRecordSectionClass}>
			<div id="record-item" class={prRecordItemClass}>
				<span>{ranking.win}</span>-<span>{ranking.loss}</span>
			</div>
			<div id="record-item" class={prRecordItemClass}>
				<span>{ranking.home_win}</span>-<span>{ranking.home_loss}</span>
			</div>
			<div id="record-item" class={prRecordItemClass}>
				<span>{ranking.road_win}</span>-<span>{ranking.road_loss}</span>
			</div>
			<div id="record-item" class={prRecordItemClass}>
				<span>{ranking.ranking}</span>
			</div>
		</div>
	{/each}
</div>
