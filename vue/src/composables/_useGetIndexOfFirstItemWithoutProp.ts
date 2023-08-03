/**
 * Finds the index of the first item without the looked prop
 * @param arr the array to look into
 * @param prop the prop to find
 * @returns the index of the first item without the prop in the array
 */
export function useGetIndexOfFirstItemWithoutProp<T>(arr: T[], prop: keyof T): number {
	const index = arr.findIndex(item => item[prop] === undefined);
	return index !== -1 ? index : arr.length;
}
