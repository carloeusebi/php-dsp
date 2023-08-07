import { SearchableQuestion, Tag } from '@/assets/data/interfaces';

/**
 * Filter an array of searchable questions by selected tags.
 * @param questions An array of SearchableQuestion objects to filter.
 * @param selectedTags An array of tag IDs to filter the questions by.
 * @returns An array of SearchableQuestion objects that match the selected tags.
 */
export function useFilterQuestionsByTags(
	questions: SearchableQuestion[],
	selectedTags: number[]
): SearchableQuestion[] {
	/**
	 * Check if an array of tags contains all the specified tag IDs.
	 * @param arr An array of Tag objects to check against.
	 * @param target An array of tag IDs to check for.
	 * @returns True if all the target tag IDs are present in the arr, otherwise false.
	 */
	const checker = (arr: Tag[], target: number[]) => target.every(v => arr.map(({ id }) => id).includes(v));

	// If there are selected tags, filter the questions based on those tags.
	// Otherwise, return all questions as is.
	return selectedTags.length > 0
		? questions.filter(question => checker(question.tags as Tag[], selectedTags))
		: questions;
}
