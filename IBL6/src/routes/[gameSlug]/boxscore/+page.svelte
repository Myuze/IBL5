<script lang="ts">
	import type { PageData } from './$types';
	import { onMount } from 'svelte';
	import GameSummary from '$lib/components/GameSummary.svelte';
	import type { GameSummary as GameSummaryType } from '$lib/types/GameSummary';
	import PlayerCard, { type PlayerCardProps } from '$lib/components/PlayerCard.svelte';
	import SlideButtonSelector from '$lib/components/SlideButtonSelector.svelte';
	import StatsHorizontal from '$lib/components/StatsHorizontal.svelte';
	import { getAllStatKeys } from '$lib/types/StatFields';
	import StatsTotals from '$lib/components/StatsTotals.svelte';
	import { type IblPlayer } from '@prisma/client';

	let { data }: { data: PageData } = $props();

	const headers = getAllStatKeys();

	// Use real data from server
	const game = $derived(data.game);
	const awayPlayers = $derived(data.awayPlayers || []);
	const homePlayers = $derived(data.homePlayers || []);

	const awayTeam = $derived(game?.awayTeam);
	const homeTeam = $derived(game?.homeTeam);

	const homeTeamName = $derived(homeTeam?.name || 'Home Team');
	const awayTeamName = $derived(awayTeam?.name || 'Away Team');
	const homeTeamScore = $derived(game?.homeScore || 0);
	const awayTeamScore = $derived(game?.awayScore || 0);

	// Selected team state
	let selectedTeamName = $state('');

	// Selected player for PlayerCard
	let selectedPlayer: PlayerCardProps | null = $state(null);
	let selectedStat: string | null = $state(null);
	let isCardOpen = $state(false);

	// Sorting state
	let sortColumn = $state<string>('pts');
	let sortDirection = $state<'asc' | 'desc'>('desc');

	let gameSummary: GameSummaryType = $derived({
		homeTeamName: homeTeamName,
		homeTeamLogoUrl: homeTeam?.teamid
			? `/teamlogo/new${homeTeam.teamid}.png`
			: 'https://placecats.com/50/50',
		awayTeamName: awayTeamName,
		awayTeamLogoUrl: awayTeam?.teamid
			? `/teamlogo/new${awayTeam.teamid}.png`
			: 'https://placecats.com/50/50',
		homeQuarterScores: game?.homeQuarterScores || [0, 0, 0, 0],
		awayQuarterScores: game?.awayQuarterScores || [0, 0, 0, 0]
	});

	// Filter and sort players based on selected team and sort criteria
	const filteredPlayers = $derived.by(() => {
		if (!selectedTeamName) return [];

		// Get base players for selected team
		const basePlayers = selectedTeamName === homeTeamName ? homePlayers : awayPlayers;

		// Sort the players
		const sortedPlayers = [...basePlayers].sort((a, b) => {
			const key = sortColumn;
			let aVal = a[key as keyof typeof a];
			let bVal = b[key as keyof typeof b];

			// Handle different data types
			if (typeof aVal === 'string' && typeof bVal === 'string') {
				// String comparison (for name, position)
				aVal = aVal.toLowerCase();
				bVal = bVal.toLowerCase();
				const comparison = aVal.localeCompare(bVal);
				return sortDirection === 'asc' ? comparison : -comparison;
			} else {
				// Numeric comparison (for stats)
				const numA = Number(aVal) || 0;
				const numB = Number(bVal) || 0;
				const comparison = numA - numB;
				return sortDirection === 'asc' ? comparison : -comparison;
			}
		});

		return sortedPlayers;
	});

	function handleTeamSelection(teamName: string) {
		selectedTeamName = teamName;
		console.log('Selected team:', selectedTeamName);
		console.log('Players for this team:', filteredPlayers.length);
	}

	// Add handlers for sort changes
	function handleSortChange(column: string, direction: 'asc' | 'desc') {
		sortColumn = column;
		sortDirection = direction;
	}

	function handlePlayerStatClick(player: PlayerCardProps, statKey: string) {
		selectedPlayer = player;
		selectedStat = statKey;
		isCardOpen = true;
	}

	function transformToPlayerCardProps(
		player: Partial<IblPlayer>,
		statKey: string
	): PlayerCardProps {
		console.log('Transforming player:', player);
		console.log('Transforming statKey:', statKey);
		console.log('Player pid:', player.pid);
		return {
			player: {
				pid: player.pid || 0,
				name: player.name || 'Unknown Player',
				team: {
					id: player.teamId || 0,
					name: player.teamname || 'Unknown Team',
					logo_url: player.teamId
						? `/teamlogo/new${player.teamId}.png`
						: 'https://placecats.com/50/50'
				},
				showcase_stats: [statKey],
				img_url: `/player/${player.pid}.jpg`
			},
			team: {
				id: player.teamId || 0,
				name: player.teamname || 'Unknown Team',
				logo_url: player.teamId
					? `/teamlogo/new${player.teamId}.png`
					: 'https://placecats.com/50/50'
			}
		};
	}

	// CSS classes for table styling
	const nameColumnClass =
		'sticky left-0 z-20 bg-base-100 group-hover:bg-base-200 font-medium px-3 py-1 border-r border-base-300 shadow-lg min-w-40 max-w-40';

	// Set initial team selection when component mounts
	onMount(() => {
		if (homeTeamName) {
			selectedTeamName = homeTeamName;
		}
	});
</script>

<!-- Show game data with real player stats -->
{#if !game}
	<div class="flex items-center justify-center p-12">
		<div class="text-center">
			<span class="loading mb-4 loading-lg loading-spinner"></span>
			<p class="text-lg">Loading game data...</p>
		</div>
	</div>
{:else}
	<!-- Game header -->
	<div class="relative mb-6 overflow-hidden rounded-lg text-white shadow-lg">
		<!-- Background gradient -->
		<div
			class="absolute inset-0"
			style="background: 
                    radial-gradient(circle at top left, {awayTeam?.color1
				? `#${awayTeam.color1}`
				: '#EF4444'} 10%, transparent 75%),
                    radial-gradient(circle at bottom left, {awayTeam?.color2
				? `#${awayTeam.color2}`
				: '#DC2626'} 25%, transparent 50%),
                    radial-gradient(circle at top right, {homeTeam?.color1
				? `#${homeTeam.color1}`
				: '#3B82F6'} 10%, transparent 75%),
                    radial-gradient(circle at bottom right, {homeTeam?.color2
				? `#${homeTeam.color2}`
				: '#2563EB'} 25%, transparent 50%),
                    linear-gradient(135deg, 
                        {awayTeam?.color1 ? `#${awayTeam.color1}` : '#EF4444'} 0%,
                        {awayTeam?.color2 ? `#${awayTeam.color2}` : '#DC2626'} 35%,
                        {homeTeam?.color1 ? `#${homeTeam.color1}` : '#3B82F6'} 65%,
                        {homeTeam?.color2 ? `#${homeTeam.color2}` : '#2563EB'} 100%)"
		></div>

		<div class="relative z-10 p-6">
			<div class="flex items-center justify-around">
				<!-- Away Team -->
				<div class="flex items-center space-x-4">
					<div class="text-center">
						<div class="mb-2 flex h-16 w-16 items-center justify-center rounded-full bg-white">
							{#if awayTeam?.teamid}
								<img
									src={`/teamlogo/new${awayTeam.teamid}.png`}
									alt="{awayTeamName} Logo"
									class="h-12 w-12 object-contain"
								/>
							{:else}
								<span class="text-2xl font-bold text-gray-800">{awayTeam?.name?.[0] || 'A'}</span>
							{/if}
						</div>
						<div class="text-sm opacity-90">{awayTeamName}</div>
						<div class="text-xs opacity-70">{awayPlayers.length} players</div>
					</div>
					<div class="text-4xl font-bold">{awayTeamScore}</div>
				</div>

				<!-- VS and Game Info -->
				<div class="text-center">
					<div class="mb-1 text-sm opacity-75">
						{new Date(game.date).toLocaleDateString()}
					</div>
					<div class="text-2xl font-bold">VS</div>
					<div class="mt-1 text-sm opacity-75">
						Game {game.gameOfThatDay}
					</div>
				</div>

				<!-- Home Team -->
				<div class="flex items-center space-x-4">
					<div class="text-4xl font-bold">{homeTeamScore}</div>
					<div class="text-center">
						<div class="mb-2 flex h-16 w-16 items-center justify-center rounded-full bg-white">
							{#if homeTeam?.teamid}
								<img
									src={`/teamlogo/new${homeTeam.teamid}.png`}
									alt="{homeTeamName} Logo"
									class="h-12 w-12 object-contain"
								/>
							{:else}
								<span class="text-2xl font-bold text-gray-800">{homeTeam?.name?.[0] || 'H'}</span>
							{/if}
						</div>
						<div class="text-sm opacity-90">{homeTeamName}</div>
						<div class="text-xs opacity-70">{homePlayers.length} players</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Game Summary -->
	<div class="p-4">
		<GameSummary summary={gameSummary} />
	</div>
	{#if selectedPlayer}
		<PlayerCard player={selectedPlayer['player']} team={selectedPlayer['team']} />
	{/if}

	<!-- Team Selector -->
	<div class="flex justify-center p-4">
		<SlideButtonSelector
			options={[awayTeamName, homeTeamName]}
			selected={selectedTeamName || homeTeamName}
			onSelectionChange={handleTeamSelection}
		/>
	</div>

	<!-- Player Stats Table -->
	{#if !selectedTeamName}
		<div class="flex flex-col items-center justify-center p-12 text-center">
			<div class="mb-4 text-6xl">👆</div>
			<h3 class="mb-2 text-lg font-semibold">Select a Team</h3>
			<p class="text-base-content/60">Choose a team above to view player statistics.</p>
		</div>
	{:else if filteredPlayers.length === 0}
		<div class="flex flex-col items-center justify-center p-12 text-center">
			<div class="mb-4 text-6xl">🏀</div>
			<h3 class="mb-2 text-lg font-semibold">No Players Found</h3>
			<p class="text-base-content/60">
				No box score data found for {selectedTeamName}.
			</p>
		</div>
	{:else}
		<div class="mb-4">
			<h2 class="text-center text-xl font-bold">
				{selectedTeamName} Box Score ({filteredPlayers.length} players)
			</h2>
			<p class="mt-1 text-center text-sm text-base-content/60">
				Sorted by {sortColumn} ({sortDirection === 'asc' ? 'ascending' : 'descending'})
			</p>
		</div>
		<div class="overflow-x-auto rounded-lg border border-base-300 shadow-sm">
			<table class="table-pin-rows table min-w-full table-zebra table-xs">
				{#key `${sortColumn}-${sortDirection}`}
					<thead>
						<StatsHorizontal {headers} {sortColumn} {sortDirection} onSort={handleSortChange} />
					</thead>
				{/key}
				<tbody>
					{#each filteredPlayers as player, rowIndex (player.pid || rowIndex)}
						<tr class="group transition-colors hover:bg-base-200/50">
							{#each headers as statKey}
								{#if statKey === 'pos'}
									<td
										onclick={(e) => {
											e.preventDefault();
											handlePlayerStatClick(transformToPlayerCardProps(player, statKey), statKey);
										}}
										class="px-2 py-1 text-center text-xs font-medium">{player.pos}</td
									>
								{:else if statKey === 'name'}
									<td
										onclick={(e) => {
											e.preventDefault();
											handlePlayerStatClick(transformToPlayerCardProps(player, statKey), statKey);
										}}
										class={nameColumnClass}
									>
										<div class="text-sm sm:truncate">
											{player[statKey as keyof typeof player] || 'N/A'}
										</div>
									</td>
								{:else if statKey === 'pts'}
									<td
										onclick={(e) => {
											e.preventDefault();
											handlePlayerStatClick(transformToPlayerCardProps(player, statKey), statKey);
										}}
										class="px-2 py-1 text-center text-sm font-bold text-primary"
									>
										{player[statKey as keyof typeof player] ?? 0}
									</td>
								{:else}
									<td
										onclick={(e) => {
											e.preventDefault();
											console.log('Player object:', player);
											console.log('Stat key:', statKey);
											handlePlayerStatClick(transformToPlayerCardProps(player, statKey), statKey);
										}}
										class="px-2 py-1 text-center text-sm"
									>
										{player[statKey as keyof typeof player] ?? 0}
									</td>
								{/if}
							{/each}
						</tr>
					{/each}
					<StatsTotals players={filteredPlayers} {headers} />
				</tbody>
				{#key `${sortColumn}-${sortDirection}`}
					<tfoot>
						<StatsHorizontal {headers} {sortColumn} {sortDirection} onSort={handleSortChange} />
					</tfoot>
				{/key}
			</table>
		</div>
	{/if}
{/if}
