<script lang="ts">
	import { page } from '$app/state';
</script>

<h1>{page.status}: {page.error?.message}</h1>
<p>{page.error}</p>
<p>The standings page is currently under construction. Please check back later!</p>
<p>
	Go back to the <a href="/">homepage</a> or visit the <a href="/powerrankings">power rankings</a>.
</p>
