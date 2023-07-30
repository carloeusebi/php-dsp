/**
 * Finds the index of the first item without the looked prop
 * @param arr the array to look into
 * @param prop the prop to find
 * @returns the index of the first item without the prop in the array
 */
export function useGetIndexOfFirstItemWithoutProp<T>(
	arr: T[],
	prop: keyof T
): number {
	let i = 0;
	let isFound = false;

	for (i; i <= arr.length && !isFound; i++) {
		if (arr[i][prop] == undefined) {
			isFound = true;
		}
	}
	return i - 1;
}
