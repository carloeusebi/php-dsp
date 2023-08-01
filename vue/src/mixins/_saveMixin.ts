import { Store } from 'pinia';

/**
 * Saves data to the db and updates the local store with the new data.
 * @param store - The Pinia store instance.
 * @param endpoint - The API endpoint to which the data will be sent for saving.
 * @param toSaveData - The data to be saved in the server request.
 * @param storeData - The array of data in the local store that will be updated after the save operation.
 * @param loadMethod - The method in the store responsible for updating the local data with the new data after saving.
 * @returns A Promise that resolves to void once the save and local store update operations are completed.
 */
export async function saveMixin<T extends Store>(
	store: T,
	endpoint: string,
	toSaveData: any,
	storeData: Array<any>,
	loadMethod: (data: Array<any>) => void
): Promise<void> {
	try {
		const res = await store.axios.post(endpoint, toSaveData);
		const insertedItem = res.data.last_insert;

		const indexToUpdate = storeData.findIndex(({ id }) => id === insertedItem.id);

		if (indexToUpdate === -1) {
			// this is a newly created item
			storeData.push(insertedItem);
		} else {
			// this is an updated item
			storeData[indexToUpdate] = insertedItem;
		}
		// Call the loadMethod to update the local store with the new data
		loadMethod(storeData);
	} catch (err) {
		throw err;
	}
}
