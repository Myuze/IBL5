export type AggregationType = 'sum' | 'avg' | 'min' | 'max';
export type StatGroup =
	| 'basic'
	| 'advanced'
	| 'shooting'
	| 'playmaking'
	| 'rebounding'
	| 'defense'
	| 'other';

export interface StatFields {
	id: string;
	name: string;
	displayName: string;
	group: StatGroup;
	aggregation: AggregationType;
}

export const STAT_FIELDS: StatFields[] = [
	{
		id: 'pts',
		name: 'Points',
		displayName: 'PTS',
		group: 'basic',
		aggregation: 'sum'
	},
	{
		id: 'min',
		name: 'Minutes Played',
		displayName: 'MIN',
		group: 'basic',
		aggregation: 'avg'
	},
	{
		id: 'fgm',
		name: 'Field Goals Made',
		displayName: 'FGM',
		group: 'shooting',
		aggregation: 'sum'
	},
	{
		id: 'fga',
		name: 'Field Goals Attempted',
		displayName: 'FGA',
		group: 'shooting',
		aggregation: 'sum'
	},
	{
		id: 'ftm',
		name: 'Free Throws Made',
		displayName: 'FTM',
		group: 'shooting',
		aggregation: 'sum'
	},
	{
		id: 'fta',
		name: 'Free Throws Attempted',
		displayName: 'FTA',
		group: 'shooting',
		aggregation: 'sum'
	},
	{
		id: '3pm',
		name: 'Three-Point Made',
		displayName: '3PM',
		group: 'shooting',
		aggregation: 'sum'
	},
	{
		id: '3pa',
		name: 'Three-Point Attempted',
		displayName: '3PA',
		group: 'shooting',
		aggregation: 'sum'
	},
	{
		id: 'orb',
		name: 'Offensive Rebounds',
		displayName: 'ORB',
		group: 'rebounding',
		aggregation: 'sum'
	},
	{
		id: 'reb',
		name: 'Total Rebounds',
		displayName: 'REB',
		group: 'rebounding',
		aggregation: 'sum'
	},
	{
		id: 'ast',
		name: 'Assists',
		displayName: 'AST',
		group: 'playmaking',
		aggregation: 'sum'
	},
	{
		id: 'stl',
		name: 'Steals',
		displayName: 'STL',
		group: 'defense',
		aggregation: 'sum'
	},
	{
		id: 'blk',
		name: 'Blocks',
		displayName: 'BLK',
		group: 'defense',
		aggregation: 'sum'
	},
	{
		id: 'tov',
		name: 'Turnovers',
		displayName: 'TOV',
		group: 'other',
		aggregation: 'sum'
	},
	{
		id: 'pf',
		name: 'Personal Fouls',
		displayName: 'PF',
		group: 'other',
		aggregation: 'sum'
	}
];

export function getStatsByGroup(group: StatGroup): StatFields[] {
	return STAT_FIELDS.filter((stat) => stat.group === group);
}

export function getAllStatKeys(): string[] {
	return STAT_FIELDS.map((stat) => stat.id);
}

export function getStatByKey(key: string): StatFields | undefined {
	return STAT_FIELDS.find((stat) => stat.id === key);
}
