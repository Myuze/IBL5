/**
 * Serialize Prisma data by converting BigInt values to numbers and Dates to ISO strings
 */
export function serializePrismaData<T>(data: T): T {
	if (data === null || data === undefined) {
		return data;
	}

	if (typeof data === 'bigint') {
		return Number(data) as T;
	}

	if (data instanceof Date) {
		return data.toISOString() as T;
	}

	if (Array.isArray(data)) {
		return data.map((item) => serializePrismaData(item)) as T;
	}

	if (typeof data === 'object') {
		const serialized: Record<string, unknown> = {};
		for (const [key, value] of Object.entries(data)) {
			serialized[key] = serializePrismaData(value);
		}
		return serialized as T;
	}

	return data;
}

/**
 * Create a URL for a game's boxscore page
 */
export function createGameUrl(date: string | Date, gameOfThatDay: number): string {
	try {
		const gameDate = typeof date === 'string' ? new Date(date) : date;
		const dateStr = gameDate.toISOString().split('T')[0]; // "2024-01-15"
		return `/${dateStr}-game-${gameOfThatDay}/boxscore`;
	} catch (err) {
		console.error('Error creating game URL:', err);
		return '#';
	}
}

/**
 * Format a date for display
 */
export function formatDate(date: string | Date): string {
	try {
		const gameDate = typeof date === 'string' ? new Date(date) : date;
		return gameDate.toLocaleDateString();
	} catch {
		return 'Invalid Date';
	}
}

/**
 * Generate team abbreviation from team name if not available
 */
export function generateAbbreviation(city?: string, name?: string): string {
	if (!city && !name) return '???';

	if (city && name) {
		return `${city[0]}${name[0]}${name[name.length - 1]}`.toUpperCase();
	}

	const teamName = city || name || '';
	return teamName.substring(0, 3).toUpperCase();
}
