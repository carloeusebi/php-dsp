/**
 * Filter an array of objects on a search query applied to specific properties.
 * @param arr The array of objects to be filtered.
 * @param searchQuery  The search query to be used for filtering.
 * @param properties The object properties to be searched in.
 * @returns An array of objects that match the search query.
 */
export function useSearchFilter<T>(arr: T[], searchQuery: string, properties: (keyof T)[]): T[] {
	const searchTerms = searchQuery.toLowerCase().split(' ');

	// Filter the array of objects based on the search query and properties
	return arr.filter(item => {
		// Extract the values of the specified properties
		const searchInProperties = properties.map(prop => (item[prop] as string).toLowerCase());

		// Check if all the search terms are present in at least one of the property values.
		return searchTerms.every(term => searchInProperties.some(value => value.includes(term)));
	});
}
