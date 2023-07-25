/**
 * Filter an array of objects by a search word
 * @param arr the array of objects to filter
 * @param searchWord the word to filter for
 * @param params an array params of the array to compare with the searchWord
 * @returns
 */
export function useSearchFilter<T>(
	arr: T[],
	searchWord: string,
	params: (keyof T)[]
): T[] {
	return arr.filter(a => {
		const stringToSearchIn = params.reduce(
			(str, param) => (str += ` ${a[param]}`),
			''
		);

		return stringToSearchIn.toLowerCase().includes(searchWord);
	});
}
