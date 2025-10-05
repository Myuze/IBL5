interface GameSummary {
	homeTeamName: string;
	homeTeamLogoUrl?: string;
	awayTeamName: string;
	awayTeamLogoUrl?: string;
	homeQuarterScores: number[];
	awayQuarterScores: number[];
}

export type { GameSummary };
