<script lang='ts'>
    interface Props {
        headers: string[];
        players: [];
    }

    let { headers, players }: Props = $props();

    function computeTotals(statKey: string): number {
        return players.reduce((total, player) => total + (player[statKey] || 0), 0);
    }
</script>

<tr>
    {#each headers as header, index}
        {#if header === 'pos' || header === 'Pos' }
            <th></th>
        {:else if header === 'name' || header === 'Name' }
            <th class="sticky left-0 z-20 bg-base-200 font-bold text-center text-secondary">TOTALS</th>
        {:else}
            <th class="bg-base-200 font-bold text-center text-secondary" key={index}>{computeTotals(header)}</th>
        {/if}
    {/each}
</tr>

