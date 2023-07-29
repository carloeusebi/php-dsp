/**
 * Splits an array into smaller chunks (pages) of a specified size.
 * @param arr The array to be split into chunks.
 * @param chunkSize The maximum number of elements to include in each chunk.
 * @returns An array of arrays, each containing up to `chunkSize` elements (except possibly the last chunk).
 */
export function useSplitArrayIntoChunks<T>(arr: T[], chunkSize: number): T[][] {
	const chunks: T[][] = [];
	for (let i = 0; i < arr.length; i += chunkSize) {
		chunks.push(arr.slice(i, i + chunkSize));
	}
	return chunks;
}
