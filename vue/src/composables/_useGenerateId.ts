type EntityWithId = { id: number };

/**
 * Generates a new ID for a new entity in an array of entities with ID.
 *
 * @param items The list of entities with ID.
 * @returns The ID for the new entity.
 */
export function useGenerateId(arr: EntityWithId[]): number {
	return arr.reduce((newId, { id }) => (newId > id ? newId : id), 0) + 1;
}
