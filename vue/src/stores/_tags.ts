import { defineStore } from 'pinia';
import { Tag } from '@/assets/data/interfaces';
import { deleteMixin, saveMixin } from '@/mixins';

const endpoint = 'tags';

export const useTagsStore = defineStore('tags', {
	//
	state: () => ({
		tags: JSON.parse(localStorage.getItem('TAGS') as string) as Tag[],
	}),

	// getters
	getters: {
		getTags: (state): Tag[] => state.tags,
	},

	// actions
	actions: {
		fetch() {
			this.axios.get(endpoint).then(res => {
				this.load(res.data.list);
			});
		},

		/**
		 * Load the tags list.
		 * @param tags The tags' list.
		 */
		load(tags: Tag[]) {
			tags = tags.map(t => ({ ...t, selected: true }));
			this.tags = tags;
			localStorage.setItem('TAGS', JSON.stringify(tags));
		},

		/**
		 * Saves new tag to the db and updates the local store with the new data
		 * @param tag The tag to be saved.
		 */
		async save(tag: Tag): Promise<void> {
			return await saveMixin(this, endpoint, tag, this.tags, this.load).catch(e => {
				throw e;
			});
		},

		/**
		 * Deletes data from the db and updates the local store after deletion
		 * @param id The ID of the tag to delete.
		 */
		async delete(id: number) {
			await deleteMixin(this, endpoint, id, this.tags, this.load);
		},
	},
});
