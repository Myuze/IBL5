<script lang="ts">
	import type { StatFields, g } from '$lib/types/StatFields';
	import type { IblPlayer, Team } from '@prisma/client';
	import { elasticIn } from 'svelte/easing';
	import { fade, fly } from 'svelte/transition';

	export interface PlayerCardProps {
		player: PlayerProps;
		team?: TeamProps;
		onCardDefaultClickAction?: () => void;
		onCardPlayerPortraitClickAction?: () => IblPlayer | undefined;
		onCardStatClickAction?: (stat_name: string) => StatFields | undefined;
	}

	interface PlayerProps {
		pid: number;
		name: string;
		team: TeamProps;
		showcase_stats: string[];
		img_url?: string;
	}

	interface TeamProps {
		id: number;
		name: string;
		logo_url?: string;
	}

	let {
		player,
		team,
		onCardDefaultClickAction,
		onCardPlayerPortraitClickAction,
		onCardStatClickAction
	}: PlayerCardProps = $props();

	console.log('PlayerCard player:', $inspect(player));
	console.log('PlayerCard team:', $inspect(team));

	const playerImgUrlBase = $derived(`/player/${player.pid}.jpg`);
	const teamLogoUrlBase = $derived(`/teamlogo/new${team?.id}.png`);
</script>

<div
	in:fade={{ duration: 500 }}
	out:fly={{ easing: elasticIn }}
	class="card mx-auto carousel-item w-96 items-center border-2 border-rose-300 bg-base-100 p-5 shadow-sm"
	style:background="linear-gradient(135deg, #000000 5%, #036bfc, #d703fc 90%);"
>
	<figure class="join min-h-50 w-full rounded-box">
		<img class="join-item h-40 object-cover" src={playerImgUrlBase} alt={player.name} />
		<img class="object-fit join-item h-40" src={teamLogoUrlBase} alt={team?.name} />
	</figure>
	<div class="card-body grid grid-cols-2 gap-2">
		<h2 class="card-title">{player.name}</h2>
		{#each player.showcase_stats as stat_name}
			<p>{stat_name}</p>
			<!-- <p>{stat_name}: {player[stat_name]}</p> -->
			<!-- <p>PTS: {player.pts}</p>
		<p>REB: {player.reb}</p>
		<p>AST: {player.ast}</p> -->
		{/each}
	</div>
</div>
