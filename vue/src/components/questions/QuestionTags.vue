<script lang="ts" setup>
import { ref, watch } from 'vue';

import AppButton from '@/components/AppButton.vue';
import AppDropdown from '@/components/AppDropdown.vue';
import QuestionTag from './QuestionTag.vue';
import QuestionTagSave from './QuestionTagSave.vue';

import { useTagsStore } from '@/stores';
import { storeToRefs } from 'pinia';
import { useDeleteFromStore } from '@/composables';

interface Props {
	editable?: boolean;
	startingSelection?: number[];
}

const props = withDefaults(defineProps<Props>(), {
	editable: () => false,
	startingSelection: () => [],
});

const tagsStore = useTagsStore();
const { tags } = storeToRefs(tagsStore);
const selectedTagsIds = ref<Array<number>>(props.startingSelection);

const showSaveModal = ref(false);

const emit = defineEmits(['change-selection']);

watch(
	() => selectedTagsIds.value,
	newValue => {
		emit('change-selection', newValue);
	}
);

/**
 * Selects all or unselects all Tags, based on `allOrNone`
 * @param allOrNone True to select all, False to unselect all.
 */
const select = (allOrNone: boolean) => {
	selectedTagsIds.value = [];
	if (allOrNone) {
		tags.value.forEach(tag => {
			selectedTagsIds.value.push(tag.id);
		});
	}
};

const deleteTag = (id: number) => {
	useDeleteFromStore(tagsStore, id);
};

/**
 * Handles the click event on a checkbox associated with a tag.
 *
 * If the tag ID is already selected, removes it from the selectedTagsIds array.
 * If the tag ID is not selected, adds it to the selectedTagsIds array.
 * @param {number} id - The ID of the tag being clicked.
 */
const handleCheckboxClick = (id: number) => {
	selectedTagsIds.value.includes(id)
		? (selectedTagsIds.value = selectedTagsIds.value.filter(tagId => tagId !== id))
		: (selectedTagsIds.value = [...selectedTagsIds.value, id]);
};
</script>

<template>
	<!-- DROPDOWN LIST -->
	<AppDropdown :items="[]">
		<template #button>
			<div class="flex items-center gap-3">
				<font-awesome-icon :icon="['fas', 'filter']" />
				Tags
			</div>
		</template>
		<template #items>
			<div class="overflow-auto">
				<div class="flex justify-center gap-3">
					<div
						class="cursor-pointer p-1 text-blue-500 hover:text-blue-700"
						@click="select(true)"
					>
						Tutti
					</div>
					<div
						class="cursor-pointer p-1 text-blue-500 hover:text-blue-700"
						@click="select(false)"
					>
						Nessuno
					</div>
				</div>
				<hr class="my-3" />
				<ul class="max-h-[250px] overflow-auto">
					<li
						v-for="tag in tags"
						:key="tag.id"
					>
						<!-- CHECKBOX -->
						<label class="container shrink w-5">
							<input
								:id="`tag-${tag.id}`"
								type="checkbox"
								class="me-2 cursor-pointer"
								:checked="selectedTagsIds.includes(tag.id)"
								@change="handleCheckboxClick(tag.id)"
							/>
							<span class="checkmark"></span>
						</label>
						<QuestionTag
							:tag="tag"
							:editable="editable"
							@delete="deleteTag($event)"
						/>
					</li>
				</ul>
				<hr
					v-if="editable"
					class="my-3"
				/>
				<div class="flex justify-center">
					<AppButton
						v-if="editable"
						@click="showSaveModal = true"
					>
						Crea nuovo tag
					</AppButton>
				</div>
			</div>
		</template>
	</AppDropdown>
	<QuestionTagSave
		:open="showSaveModal"
		@close="showSaveModal = false"
	/>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';

label.container {
	bottom: 10px;
}
</style>
