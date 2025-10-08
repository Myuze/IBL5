<script lang="ts">
	import { getStatDisplayName } from '$lib/types/StatFields';

    interface Props {
        headers: string[];
        sortColumn: string;
        sortDirection: 'asc' | 'desc';
        onSort: (column: string, direction: 'asc' | 'desc') => void;
    }

    let { headers, sortColumn, sortDirection, onSort }: Props = $props();

    function getSortIcon(header: string): string {
        const column = header;
        if (sortColumn !== column) return '⇅';
        return sortDirection === 'asc' ? '↑' : '↓';
    }

    function isActiveSortColumn(header: string): boolean {
        const column = header;
        const isActive = sortColumn === column;
        return isActive;
    }

    function handleSort(header: string) {
        const column = header;
        
        let newDirection: 'asc' | 'desc';
        if (sortColumn === column) {
            newDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            newDirection = ['name', 'pos'].includes(column) ? 'asc' : 'desc';
        }
        
        // Call the parent's callback
        onSort(column, newDirection);
    }
</script>

<tr>
    {#each headers as header, index}
        <th 
            class="cursor-pointer select-none hover:bg-base-200 transition-colors px-2 py-3 min-w-10 text-center
                   {isActiveSortColumn(header) ? 'bg-primary/20 text-primary font-bold' : ''}
                   {header === 'name' ? 'sticky opacity-100 left-0 z-30 border-r border-base-300 shadow-lg min-w-40' : ''}
                   {header === 'name' && !isActiveSortColumn(header) ? 'bg-base-100' : ''}"
            onclick={() => handleSort(header)}
            title="Click to sort by {header}"
        >
            <div class="flex items-center gap-1 justify-center">
                <span class="font-semibold">
                    {getStatDisplayName(header)}
                </span>
                <span class="text-xs opacity-60">
                    {getSortIcon(header)}
                </span>
            </div>
        </th>
    {/each}
</tr>
