<script lang="ts">
	import type { PageData } from './$types';

	let { data }: { data: PageData } = $props();

	console.log(data);
	const prContainerClass = 'flex flex-col overflow-x-auto w-full';
	const prTeamContainerClass = 'flex flex-row flex-nowrap items-center justify-center';
	const prAnchorClass = '';
	const prTeamImageClass = 'col-start-2 w-[90%] object-contain mb-2 md:max-w-[50%]';
	const prRecordSectionClass = 'hidden basis-xs lg:grid cols-start-3 grid-cols-4 md:grid-cols-4';
	const prRecordHeaderClass = 'flex flex-row items-center justify-center font-bold';
	const prRecordItemClass = 'flex flex-row items-center justify-center';
</script>

<div class={prContainerClass}>
	<h1 class="p-2 text-center text-3xl font-bold">Power Rankings</h1>
	{#each data.powerRankings as ranking, index}
		<a
			href={`https://iblhoops.net/ibl5/modules.php?name=Team&op=team&teamID=${ranking.TeamID}`}
			class={prAnchorClass}
		>
			<div id="team-container" class={prTeamContainerClass}>
				<h2 class="items-center pr-2 text-xl font-semibold">{index + 1}.</h2>
				<img
					src={`https://iblhoops.net/ibl5/images/logo/${ranking.TeamID}.jpg`}
					alt={ranking.Team}
					class={prTeamImageClass}
				/>
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
			</div>
		</a>
	{/each}
</div>
