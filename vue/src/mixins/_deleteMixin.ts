import { Store } from 'pinia';

/**
 * Deletes data from the db and updates the local store after deletion.
 * @param store - The Pinia store instance.
 * @param endpoint - The API endpoint to which the delete request will be sent.
 * @param id - The ID of the item to be deleted.
 * @param storeData - The array of data in the local store that will be updated after the deletion.
 * @param loadMethod - The method in the store responsible for updating the local data after deletion.
 */
export async function deleteMixin<T extends Store>(
	store: T,
	endpoint: string,
	id: number,
	storeData: Array<any>,
	loadMethod: (data: Array<any>) => void
) {
	const data = { id };
	try {
		await store.axios.delete(endpoint, { data });

		// Remove the deleted item from the local store data
		const filteredData = storeData.filter(item => item.id !== id);

		// Call the loadMethod to update the local store with the updated data
		loadMethod(filteredData);
	} catch (err) {
		console.error(err);
	}
}
