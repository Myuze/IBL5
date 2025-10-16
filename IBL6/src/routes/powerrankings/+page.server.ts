import type { PageServerLoad } from './$types';
import { prisma } from '$lib/database/prisma';
import { serializePrismaData } from '$lib/utils/utils';

export const load = (async () => {
	try {
		const powerRankings = await prisma.ibl_power.findMany({
			select: {
				TeamID: true,
				Team: true,
				ranking: true,
				win: true,
				loss: true,
				home_win: true,
				home_loss: true,
				road_win: true,
				road_loss: true
			},
			where: {
				ranking: {
					not: undefined,
					gt: 0
				}
			},
			orderBy: {
				ranking: 'desc'
			}
		});

		// Convert ranking to string to avoid serialization issues
		const sanitizedRankings = powerRankings.map((pr) => ({
			...pr,
			ranking: pr.ranking?.toString()
		}));

		return { powerRankings: serializePrismaData(sanitizedRankings) };
	} catch (error) {
		console.error('Error fetching power rankings:', error);
		return { error: 'Failed to fetch power rankings' };
	}
}) satisfies PageServerLoad;
