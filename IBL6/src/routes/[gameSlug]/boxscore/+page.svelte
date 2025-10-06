<script lang='ts'>
    import type { PageData } from './$types';
    import { onMount } from 'svelte';
    import GameSummary from '$lib/components/GameSummary.svelte';
    import type { GameSummary as GameSummaryType } from '$lib/types/GameSummary';
    import PlayerCard from '$lib/components/PlayerCard.svelte';
    import SlideButtonSelector from '$lib/components/SlideButtonSelector.svelte';
    import StatsHorizontal from '$lib/components/StatsHorizontal.svelte';

    const headers = [
        'Pos', 'Name', 'pts', 'min', 'fgm', 'fga', 'ftm', 'fta', 
        '3pm', '3pa', 'orb', 'reb', 'ast', 'stl', 'blk', 'tov', 'pf'
    ];

    let { data }: { data: PageData } = $props();

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
    
    const homeTeamColor = $derived(homeTeam?.color1 ? `#${homeTeam.color1}` : '#3B82F6');
    const awayTeamColor = $derived(awayTeam?.color1 ? `#${awayTeam.color1}` : '#EF4444');

    // Selected team state
    let selectedTeamName = $state('');
    
    // Sorting state
    let sortColumn = $state<string>('min');
    let sortDirection = $state<'asc' | 'desc'>('desc');

    // Map header display names to actual property names
    const columnMap: Record<string, string> = {
        'Pos': 'pos',
        'Name': 'name', 
        'pts': 'pts',
        'min': 'min',
        'fgm': 'fgm',
        'fga': 'fga', 
        'ftm': 'ftm',
        'fta': 'fta',
        '3pm': '3pm',
        '3pa': '3pa',
        'orb': 'orb',
        'reb': 'reb',
        'ast': 'ast',
        'stl': 'stl',
        'blk': 'blk',
        'tov': 'tov',
        'pf': 'pf'
    };

    let gameSummary: GameSummaryType = $derived({
        homeTeamName: homeTeamName,
        homeTeamLogoUrl: homeTeam?.teamid ? `/teamlogo/new${homeTeam.teamid}.png` : 'https://placecats.com/50/50',
        awayTeamName: awayTeamName,
        awayTeamLogoUrl: awayTeam?.teamid ? `/teamlogo/new${awayTeam.teamid}.png` : 'https://placecats.com/50/50',
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
            const key = columnMap[sortColumn] || sortColumn;
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
   
    // Set initial team selection when component mounts
    onMount(() => {
        if (homeTeamName) {
            selectedTeamName = homeTeamName;
        }
    });
</script>

<!-- Show game data with real player stats -->
{#if !game}
    <div class="flex justify-center items-center p-12">
        <div class="text-center">
            <span class="loading loading-spinner loading-lg mb-4"></span>
            <p class="text-lg">Loading game data...</p>
        </div>
    </div>
{:else}
    <!-- Game header -->
    <div class="bg-gradient-to-r text-white p-6 rounded-lg shadow-lg mb-6"
         style="background: linear-gradient(to right, {awayTeamColor}aa, {homeTeamColor}aa)">
        <div class="flex justify-around items-center">
            <!-- Away Team -->
            <div class="flex items-center space-x-4">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2">
                        {#if awayTeam?.teamid}
                            <img src={`/teamlogo/new${awayTeam.teamid}.png`} alt="{awayTeamName} Logo" class="w-12 h-12 object-contain" />
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
                <div class="text-sm opacity-75 mb-1">
                    {new Date(game.date).toLocaleDateString()}
                </div>
                <div class="text-2xl font-bold">VS</div>
                <div class="text-sm opacity-75 mt-1">
                    Game {game.gameOfThatDay}
                </div>
            </div>
            
            <!-- Home Team -->
            <div class="flex items-center space-x-4">
                <div class="text-4xl font-bold">{homeTeamScore}</div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2">
                        {#if homeTeam?.teamid}
                            <img src={`/teamlogo/new${homeTeam.teamid}.png`} alt="{homeTeamName} Logo" class="w-12 h-12 object-contain" />
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

    <!-- Game Summary -->
    <div class="p-4">
        <GameSummary summary={gameSummary} />
    </div>

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
            <div class="text-6xl mb-4">👆</div>
            <h3 class="text-lg font-semibold mb-2">Select a Team</h3>
            <p class="text-base-content/60">Choose a team above to view player statistics.</p>
        </div>
    {:else if filteredPlayers.length === 0}
        <div class="flex flex-col items-center justify-center p-12 text-center">
            <div class="text-6xl mb-4">🏀</div>
            <h3 class="text-lg font-semibold mb-2">No Players Found</h3>
            <p class="text-base-content/60">
                No box score data found for {selectedTeamName}.
            </p>
        </div>
    {:else}
        <div class="mb-4">
            <h2 class="text-xl font-bold text-center">
                {selectedTeamName} Box Score ({filteredPlayers.length} players)
            </h2>
            <p class="text-sm text-center text-base-content/60 mt-1">
                Sorted by {sortColumn} ({sortDirection === 'asc' ? 'ascending' : 'descending'})
            </p>
        </div>
        <div class="overflow-x-auto border border-base-300 rounded-lg shadow-sm">
            <table class="table table-zebra table-pin-rows table-xs min-w-full">
                {#key `${sortColumn}-${sortDirection}`}
                    <thead>
                        <StatsHorizontal 
                            {headers} 
                            {sortColumn} 
                            {sortDirection}
                            onSort={handleSortChange}
                        />
                    </thead>
                {/key}
                <tbody>
                    {#each filteredPlayers as player, rowIndex (player.id || rowIndex)}
                        <tr class="group hover:bg-base-200/50 transition-colors">
                            <td class="px-2 py-1 text-center text-xs font-medium">{player.pos}</td>
                            <td class="sticky left-0 z-20 bg-base-100 group-hover:bg-base-200 font-medium px-3 py-1 border-r border-base-300 shadow-lg min-w-32 max-w-32">
                                <div class="truncate text-sm">
                                    {player.name}
                                </div>
                            </td>
                            <td class="px-2 py-1 text-center text-sm font-bold text-primary">{player.pts}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.min}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.fgm}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.fga}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.ftm}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.fta}</td>
                            <td class="px-2 py-1 text-center text-sm">{player['3pm']}</td>
                            <td class="px-2 py-1 text-center text-sm">{player['3pa']}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.orb}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.reb}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.ast}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.stl}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.blk}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.tov}</td>
                            <td class="px-2 py-1 text-center text-sm">{player.pf}</td>
                        </tr>
                    {/each}
                </tbody>
                {#key `${sortColumn}-${sortDirection}`}
                    <tfoot>
                        <StatsHorizontal 
                            {headers} 
                            {sortColumn} 
                            {sortDirection}
                            onSort={handleSortChange}
                        />
                    </tfoot>
                {/key}
            </table>
        </div>
    {/if}
{/if}
