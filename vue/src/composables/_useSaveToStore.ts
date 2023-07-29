import {
	Errors,
	PatientsStore,
	Question,
	QuestionStore,
	Survey,
	SurveyStore,
} from '@/assets/data/interfaces';
import { useLoaderStore } from '@/stores';
import { isAxiosError } from 'axios';

type MyStore = PatientsStore | QuestionStore | SurveyStore;
type DataToStore = FormData | Question | Survey;

/**
 * Saves to store an updated or created entity of the type Patient Question or Survey. The store then will handle the ajax call to update the entity on the database
 * @param data The object to save (Patient | Question | Survey)
 * @param store The store
 * @returns An Error object, empty if there are no errors
 */
export async function useSaveToStore(
	data: DataToStore,
	store: MyStore
): Promise<Errors> {
	const loader = useLoaderStore();
	loader.setLoader();
	let errors = {};
	try {
		//@ts-ignore
		await store.save(data);
	} catch (err) {
		if (isAxiosError(err)) {
			errors = err.response?.data;
		} else {
			console.error(err);
			alert(
				'Qualcosa è andato storto, ma le modifiche dovrebbero essere state salvate. Prova a ricaricare la pagina!'
			);
		}
	} finally {
		loader.unsetLoader();
	}

	return errors;
}
