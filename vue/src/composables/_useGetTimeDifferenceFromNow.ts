/**
 * Calculates the time difference in the specified unit between a given date and the current date.
 * @param dateTime The date and time (e.g., "2023-08-03T12:34:56").
 * @param unit The unit to represent the time difference ('seconds', 'minutes', 'hours', or 'days').
 * @returns The time difference between the specified date and the current date in the specified unit.
 * @throws An error if the provided `dateTime` is not a valid date or if an invalid unit is provided.
 */
export function useGetTimeDifferenceFromNow(dateTime: string, unit: 'seconds' | 'minutes' | 'hours' | 'days'): number {
	const now = Date.now();
	const timestamp = new Date(dateTime).getTime();

	if (isNaN(timestamp)) {
		throw new Error(`Invalid date: ${dateTime} is not a valid date`);
	}

	const difference = now - timestamp;

	switch (unit) {
		case 'seconds':
			return Math.floor(difference / 1000);
		case 'minutes':
			return Math.floor(difference / (1000 * 60));
		case 'hours':
			return Math.floor(difference / (1000 * 60 * 60));
		case 'days':
			return Math.floor(difference / (1000 * 60 * 60 * 24));
		default:
			throw new Error(`Invalid unit ${unit}`);
	}
}
