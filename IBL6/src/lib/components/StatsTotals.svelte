<script lang='ts'>
	import { getStatByKey } from '$lib/types/StatFields';

    
    interface Props {
        headers: string[];
        players: any[];
    }

    let { headers, players }: Props = $props();

    function computeTotals(statKey: string): number {
        const statField = getStatByKey(statKey);
        if (!statField) throw new Error(`Stat field not found for key: ${statKey}`);

        switch (statField.aggregation) {
            case 'sum':
                return players.reduce((total, player) => total + (player[statKey] || 0), 0);
            case 'avg':
                const total = players.reduce((sum, player) => sum + (player[statKey] || 0), 0);
                return players.length > 0 ? parseFloat((total / players.length).toFixed(1)) : 0;
            case 'max':
                return players.reduce((max, player) => Math.max(max, player[statKey] || 0), 0);
            case 'min':
                return players.reduce((min, player) => Math.min(min, player[statKey] || Infinity), Infinity);
            default:
                throw new Error(`Unknown aggregation type: ${statField.aggregation}`);
        }
    }
</script>

<tr>
    {#each headers as header}
        {#if header === 'pos' || header === 'Pos' }
            <th></th>
        {:else if header === 'name' || header === 'Name' }
            <th class="sticky left-0 z-20 bg-base-200 font-bold text-center text-secondary">TOTALS</th>
        {:else}
            <th class="bg-base-200 font-bold text-center text-secondary">{computeTotals(header)}</th>
        {/if}
    {/each}
</tr>

