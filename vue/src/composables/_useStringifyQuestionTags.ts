import { Question, SearchableQuestion } from '@/assets/data/interfaces';

/**
 * Returns an Array of Questions, with the added property `tagsString`, which is a string of all the tags that will be used by the searchbar.
 * @param questions The Array with all the Questions.
 */
export function useStringifyQuestionTags(questions: Question[]): SearchableQuestion[] {
	return questions.map(q => {
		const tagsString = q.tags?.reduce((str, tag) => (str += `${tag.tag} `), '');
		return { ...q, tagsString };
	});
}
