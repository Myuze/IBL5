<script lang="ts">
	import '../app.css';
	import favicon from '$lib/assets/favicon.svg';
	import logo from '$lib/assets/logo.jpg';

	let { children } = $props();
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
</svelte:head>
<div>
	<div class="relative z-50 navbar bg-base-100 shadow-sm">
		<div class="navbar-start">
			<div class="dropdown">
				<div tabindex="0" role="button" class="btn btn-circle btn-ghost">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						class="h-5 w-5"
						fill="none"
						viewBox="0 0 24 24"
						stroke="currentColor"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M4 6h16M4 12h16M4 18h7"
						/>
					</svg>
				</div>
<<<<<<< HEAD
<<<<<<< Updated upstream
				<ul class="dropdown-content menu z-[100] mt-3 w-52 rounded-box bg-base-100 p-2 shadow">
					<li><a href="/">Homepage</a></li>
=======
				<ul
					class="dropdown-content menu z-1 mt-3 w-52 menu-sm rounded-box bg-base-100 p-2 shadow"
				>
					<li><a href="/">Homepage</a></li>
                    <li><a href="/powerrankings">Power Rankings</a></li>
>>>>>>> Stashed changes
=======
				<ul class="dropdown-content menu z-[100] mt-3 w-52 rounded-box bg-base-100 p-2 shadow">
					<li><a href="/">Homepage</a></li>
>>>>>>> 824f39f172eb56b9fbf29538107344b81214583d
					<li><a href="/standings">Standings (Under Construction)</a></li>
					<li><a href="/teams">Teams (Under Construction)</a></li>
					<li><a href="/players">Players (Under Construction)</a></li>
					<li><a href="/games">Games (Under Construction)</a></li>
					<li><a href="/about">About (Under Construction)</a></li>
				</ul>
			</div>
		</div>
		<div class="navbar-center">
			<a href="/" class="btn text-xl btn-ghost">AYE-BEE-EL</a>
		</div>
		<div class="navbar-end">
			<button class="btn btn-circle btn-ghost" aria-label="Search">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					class="h-5 w-5"
					fill="none"
					viewBox="0 0 24 24"
					stroke="currentColor"
				>
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
					/>
				</svg>
			</button>
			<button class="btn btn-circle btn-ghost" aria-label="Notifications">
				<div class="indicator">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						class="h-5 w-5"
						fill="none"
						viewBox="0 0 24 24"
						stroke="currentColor"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
						/>
					</svg>
					<span class="indicator-player badge badge-xs badge-primary"></span>
				</div>
			</button>
		</div>
	</div>
</div>
{@render children?.()}
