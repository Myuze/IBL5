<script lang="ts">
	import type { PageData } from './$types';

	let { data }: { data: PageData } = $props();

	console.log(data);
	const prContainerClass = 'flex flex-col overflow-x-auto w-full';
	const prGridClass = '';
	const prTeamContainerClass = 'flex flex-row';
	const prAnchorClass = 'block';
	const prTeamImageClass = 'col-start-2 w-[90%] object-contain mb-2';
	const prRecordSectionClass =
		'hidden grid grid-cols-4 md:grid-cols-4 gap-2 md:gap-4 lg:gap-6 xl:gap-8 2xl:gap-10';
	const prRecordItemClass = 'flex flex-col items-center justify-center';
</script>

<div class={prContainerClass}>
	<h1 class="p-2 text-center text-3xl font-bold">Power Rankings</h1>
	{#each data.powerRankings as ranking, index}
		<a
			href={`https://iblhoops.net/ibl5/modules.php?name=Team&op=team&teamID=${ranking.TeamID}`}
			class={prAnchorClass}
		>
			<div id="team-container" class={prTeamContainerClass}>
				<h2 class="m-auto text-xl font-semibold">{index + 1}.</h2>
				<img
					src={`https://iblhoops.net/ibl5/images/logo/${ranking.TeamID}.jpg`}
					alt={ranking.Team}
					class={prTeamImageClass}
				/>
			</div>
		</a>
	{/each}
</div>
