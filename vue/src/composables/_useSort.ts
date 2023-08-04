/**
 * Sorts an array of objects given a property to sort by and the direction
 * @param arr the array of objects to sort
 * @param by the property of the object to sort by
 * @param type up = asc | down = desc
 * @returns the sorted array of objects
 */
export function useSort<T>(arr: T[], by: keyof T, direction: 'up' | 'down'): T[] {
	return direction === 'up'
		? arr.sort((a, b) => (a[by] < b[by] ? 1 : -1))
		: arr.sort((a, b) => (a[by] > b[by] ? 1 : -1));
}
