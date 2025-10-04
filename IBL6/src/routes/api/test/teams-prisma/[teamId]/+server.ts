// src/routes/api/test/teams-prisma/[teamId]/+server.ts
import { json, error } from '@sveltejs/kit';
import { PrismaClient } from '@prisma/client';
import { serializePrismaData } from '$lib/utils/utils';
import type { RequestHandler } from './$types';

const prisma = new PrismaClient();

export const GET: RequestHandler = async ({ params }) => {
	try {
		const teamId = parseInt(params.teamId);

		if (!teamId || isNaN(teamId)) {
			throw error(400, 'Invalid team ID');
		}

		console.log(`🔍 Fetching team ${teamId} using Prisma`);

		// Get single team using Prisma
		const team = await prisma.team.findUnique({
			where: {
				teamid: teamId
			},
			select: {
				teamid: true,
				city: true,
				name: true,
				color1: true,
				color2: true,
				arena: true,
				owner: true,
				ownerEmail: true,
				discord: true,
				fka: true,
				contractWins: true,
				contractLosses: true,
				contractCoach: true,
				chart: true
			}
		});

		if (!team) {
			throw error(404, 'Team not found');
		}

		console.log(`✅ Found team: ${team.city} ${team.name}`);

		// Serialize all problematic types before returning
		const serializedTeam = serializePrismaData(team);

		return json({
			success: true,
			team: serializedTeam,
			timestamp: new Date().toISOString()
		});
	} catch (err: any) {
		console.error('❌ Failed to fetch team with Prisma:', err);

		// Handle Prisma-specific errors
		if (err.code === 'P2025') {
			throw error(404, 'Team not found');
		}

		if (err.code === 'P2022') {
			throw error(500, 'Database schema mismatch');
		}

		if (err.status) {
			throw error(err.status, err.message);
		}

		throw error(500, 'Internal server error');
	} finally {
		await prisma.$disconnect().catch((disconnectErr) => {
			console.error('Failed to disconnect Prisma client:', disconnectErr);
		});
	}
};
