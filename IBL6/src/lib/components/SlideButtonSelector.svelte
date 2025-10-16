<script lang="ts">
  export let options: string[] = ['Team A', 'Team B'];
  export let selected: string = options[0];
  export let onSelectionChange: ((option: string) => void) | undefined = undefined;

  function selectOption(option: string) {
    selected = option;
    onSelectionChange?.(option);
  }
</script>

<div class="relative bg-base-200 p-1 rounded-lg flex lg:w-1/4 w-full">
  <!-- Sliding background -->
  <div 
    class="absolute bg-primary rounded transition-all duration-300 ease-in-out"
    style="width: calc(100% / {options.length}); left: {options.indexOf(selected) * (100 / options.length)}%; height: calc(100% - 8px); top: 4px;"
  ></div>
  
  <!-- Buttons -->
  {#each options as option}
    <button 
      class="relative z-10 px-4 py-2 text-sm font-medium transition-colors duration-200 flex-1 rounded {selected === option ? 'text-primary-content' : 'text-base-content hover:text-primary'}"
      on:click={() => selectOption(option)}
    >
      {option}
    </button>
  {/each}
</div>