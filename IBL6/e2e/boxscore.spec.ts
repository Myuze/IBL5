import { test, expect } from '@playwright/test';

test('boxscore page shows two teams and two rosters', async ({ page }) => {
	// Replace with a real slug that should have two teams and rosters
	await page.goto('/2006-02-11-game-4/boxscore');

	// Wait for the teams to load
	await expect(page.locator('.bg-gradient-to-r')).toBeVisible();

	// Get team name elements (adjust selector as needed)
	const teamNames = await page.locator('.bg-gradient-to-r .text-sm.opacity-90').allTextContents();
	expect(teamNames.length).toBe(2);
	expect(teamNames[0]).not.toBe('');
	expect(teamNames[1]).not.toBe('');

	// Check player counts for each team (adjust selector as needed)
	const playerCounts = await page
		.locator('.bg-gradient-to-r .text-xs.opacity-70')
		.allTextContents();
	expect(playerCounts.length).toBe(2);
	// Parse "X players" and ensure > 0
	for (const countText of playerCounts) {
		const match = countText.match(/(\d+)\s+players?/);
		expect(match).not.toBeNull();
		expect(Number(match?.[1])).toBeGreaterThan(0);
	}

	// Optionally, check that the player table renders for each team
	// Select home team
	await page.getByText(teamNames[0]).nth(1).click();
	await expect(page.getByText(`${teamNames[0]} Box Score`)).toBeVisible();

	// Select away team
	await page.getByText(teamNames[1]).nth(1).click();
	await expect(page.getByText(`${teamNames[1]} Box Score`)).toBeVisible();
});
