import { MyStore } from '@/assets/data/interfaces';
import { useLoaderStore } from '@/stores';
import { isAxiosError } from 'axios';

/**
 * Asynchronous function to delete an item from a given store using its ID.
 * @param {MyStore} store - The store from which the item needs to be deleted.
 * @param {number} id - The unique identifier of the item to be deleted.
 */
export async function useDeleteFromStore(store: MyStore, id: number) {
	const loader = useLoaderStore();
	loader.setLoader;
	try {
		await store.delete(id);
	} catch (err) {
		if (!isAxiosError(err)) console.log(err);
	} finally {
		loader.unsetLoader();
	}
}
