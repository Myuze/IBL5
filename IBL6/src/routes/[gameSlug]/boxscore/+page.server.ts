import type { PageServerLoad } from './$types';
import { prisma } from '$lib/database/prisma';
import { serializePrismaData } from '$lib/utils/utils';
import { error } from '@sveltejs/kit';

export const load: PageServerLoad = async ({ params }) => {
	try {
		console.log(`🔍 Loading game with slug: ${params.gameSlug}`);

		// Parse game slug
		const parts = params.gameSlug.split('-');
		const year = parseInt(parts[0]);
		const month = parseInt(parts[1]);
		const day = parseInt(parts[2]);
		const gameNumber = parseInt(parts[4]) || 1;

		const gameDate = new Date(year, month - 1, day);

		// Get game info
		const gameInfo = (await prisma.$queryRaw`
            SELECT DISTINCT
                game.Date,
                game.visitorTeamID as awayTeamId,
                game.homeTeamID as homeTeamId,
                game.gameOfThatDay,
                game.visitorQ1points,
                game.visitorQ2points,
                game.visitorQ3points,
                game.visitorQ4points,
                game.visitorOTpoints,
                game.homeQ1points,
                game.homeQ2points,
                game.homeQ3points,
                game.homeQ4points,
                game.homeOTpoints,
                (game.visitorQ1points + game.visitorQ2points + game.visitorQ3points + game.visitorQ4points + COALESCE(game.visitorOTpoints, 0)) as awayScore,
                (game.homeQ1points + game.homeQ2points + game.homeQ3points + game.homeQ4points + COALESCE(game.homeOTpoints, 0)) as homeScore,
                away.teamid as away_teamid,
                away.team_city as away_city,
                away.team_name as away_name,
                away.color1 as away_color1,
                away.color2 as away_color2,
                home.teamid as home_teamid,
                home.team_city as home_city,
                home.team_name as home_name,
                home.color1 as home_color1,
                home.color2 as home_color2
            FROM ibl_box_scores_teams game
            LEFT JOIN ibl_team_info home ON game.homeTeamID = home.teamid
            LEFT JOIN ibl_team_info away ON game.visitorTeamID = away.teamid
            WHERE DATE(game.Date) = DATE(${gameDate})
            AND game.gameOfThatDay = ${gameNumber}
            LIMIT 1
        `) as any[];

		if (!gameInfo || gameInfo.length === 0) {
			throw error(404, `Game not found`);
		}

		const gameData = gameInfo[0];
		console.log(
			'✅ Found game on',
			gameData.Date.toISOString().split('T')[0],
			`(Game #${gameData.gameOfThatDay})`
		);
		console.log('🔍 Game data:', gameData);
		console.log('🏀 Game teams:', {
			away: `${gameData.away_city} ${gameData.away_name} (ID: ${gameData.awayTeamId})`,
			home: `${gameData.home_city} ${gameData.home_name} (ID: ${gameData.homeTeamId})`
		});

		// Get players with correct team ID references
		const players = (await prisma.$queryRaw`
            SELECT 
                bp.Date,
                bp.name,
                bp.pos,
                bp.pid,
                plr.tid as playerTeamId,
                -- Check if player's team matches away team
                CASE 
                    WHEN plr.tid = ${gameData.awayTeamId} THEN 1
                    ELSE 0
                END as isAwayPlayer,
                bp.gameMIN as min,
                bp.game2GM as fgm,
                bp.game2GA as fga,
                bp.gameFTM as ftm,
                bp.gameFTA as fta,
                bp.game3GM as tpm,
                bp.game3GA as tpa,
                bp.gameORB as orb,
                bp.gameDRB as drb,
                bp.gameAST as ast,
                bp.gameSTL as stl,
                bp.gameTOV as tov,
                bp.gameBLK as blk,
                bp.gamePF as pf,
                (bp.gameORB + bp.gameDRB) as reb,
                (bp.game2GM * 2 + bp.game3GM + bp.gameFTM) as pts
            FROM ibl_box_scores bp
            LEFT JOIN ibl_plr plr ON bp.pid = plr.pid
            WHERE DATE(bp.Date) = DATE(${gameDate})
            AND plr.tid IN (${gameData.awayTeamId}, ${gameData.homeTeamId})
            ORDER BY plr.tid DESC, bp.gameMIN DESC
        `) as any[];

		console.log(`✅ Found ${players.length} total players for this game`);

		// Separate players by team
		const awayPlayers = players.filter((p: any) => p.isAwayPlayer === 1);
		const homePlayers = players.filter((p: any) => p.isAwayPlayer === 0);

		console.log(`👥 Away players: ${awayPlayers.length}, Home players: ${homePlayers.length}`);

		// Debug first few players
		if (players.length > 0) {
			console.log(
				'🔍 First 3 players:',
				players.slice(0, 3).map((p) => ({
					name: p.name,
					visitorTID: p.visitorTID,
					homeTID: p.homeTID,
					playerTeamId: p.playerTeamId,
					isAwayPlayer: p.isAwayPlayer
				}))
			);
		}

		// Transform game data
		const formattedGame = {
			date: gameData.Date,
			gameOfThatDay: gameData.gameOfThatDay,
			awayScore: Number(gameData.awayScore) || 0,
			homeScore: Number(gameData.homeScore) || 0,
			awayTeamId: gameData.awayTeamId,
			homeTeamId: gameData.homeTeamId,
			homeQuarterScores: [
				Number(gameData.homeQ1points) || 0,
				Number(gameData.homeQ2points) || 0,
				Number(gameData.homeQ3points) || 0,
				Number(gameData.homeQ4points) || 0,
				...(gameData.homeOTpoints ? [Number(gameData.homeOTpoints) || 0] : [])
			],
			awayQuarterScores: [
				Number(gameData.visitorQ1points) || 0,
				Number(gameData.visitorQ2points) || 0,
				Number(gameData.visitorQ3points) || 0,
				Number(gameData.visitorQ4points) || 0,
				...(gameData.visitorOTpoints ? [Number(gameData.visitorOTpoints) || 0] : [])
			],
			awayTeam: gameData.away_teamid
				? {
						teamid: gameData.away_teamid,
						city: gameData.away_city,
						name: gameData.away_name,
						abbreviation: null,
						color1: gameData.away_color1,
						color2: gameData.away_color2
					}
				: null,
			homeTeam: gameData.home_teamid
				? {
						teamid: gameData.home_teamid,
						city: gameData.home_city,
						name: gameData.home_name,
						abbreviation: null,
						color1: gameData.home_color1,
						color2: gameData.home_color2
					}
				: null
		};

		// Transform player data
		const formatPlayers = (playerList: any[]) => {
			return playerList.map((player: any) => ({
				id: player.pid,
				pos: player.pos || 'N/A',
				name: player.name || 'Unknown',
				teamId: Number(player.playerTeamId), // Add team ID for debugging
				min: Number(player.min) || 0,
				fgm: Number(player.fgm) || 0,
				fga: Number(player.fga) || 0,
				ftm: Number(player.ftm) || 0,
				fta: Number(player.fta) || 0,
				'3pm': Number(player.tpm) || 0,
				'3pa': Number(player.tpa) || 0,
				pts: Number(player.pts) || 0,
				orb: Number(player.orb) || 0,
				reb: Number(player.reb) || 0,
				ast: Number(player.ast) || 0,
				stl: Number(player.stl) || 0,
				blk: Number(player.blk) || 0,
				tov: Number(player.tov) || 0,
				pf: Number(player.pf) || 0
			}));
		};

		console.log('Serialized game data:', serializePrismaData(formattedGame));
		console.log('🎯 Returning game data:', {
			visitorQ1points: gameData.visitorQ1points,
			visitorQ2points: gameData.visitorQ2points,
			homeQ1points: gameData.homeQ1points,
			homeQ2points: gameData.homeQ2points
		});
		return {
			game: serializePrismaData(formattedGame),
			awayPlayers: serializePrismaData(formatPlayers(awayPlayers)),
			homePlayers: serializePrismaData(formatPlayers(homePlayers))
		};
	} catch (err: any) {
		console.error('Error loading game:', err);
		if (err.status) {
			throw err;
		}
		throw error(500, 'Error loading game data');
	}
};
