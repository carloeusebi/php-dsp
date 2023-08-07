<script lang="ts" setup>
import { computed, ref, watch } from 'vue';

import AppButton from '@/components/AppButton.vue';
import AppDropdown from '@/components/AppDropdown.vue';
import AppButtonBlank from '../AppButtonBlank.vue';
import QuestionTag from './QuestionTag.vue';
import QuestionTagSave from './QuestionTagSave.vue';

import { useTagsStore } from '@/stores';
import { storeToRefs } from 'pinia';
import { useDeleteFromStore } from '@/composables';

defineProps({
	editable: {
		type: Boolean,
		default: false,
	},
});

const tagsStore = useTagsStore();
const { tags } = storeToRefs(tagsStore);
const selectedTags = computed(() => tags.value.filter(({ selected }) => selected));

const showSaveModal = ref(false);

const emit = defineEmits(['change-selection']);

watch(
	() => selectedTags.value,
	newValue => {
		emit('change-selection', newValue);
	}
);

/**
 * Selects all or unselects all Tags, based on `allOrNone`
 * @param allOrNone True to select all, False to unselect all.
 */
const select = (allOrNone: boolean) => {
	tags.value.forEach(t => {
		t.selected = allOrNone;
	});
};

const deleteTag = (id: number) => {
	useDeleteFromStore(tagsStore, id);
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
			<div class="flex justify-center">
				<AppButtonBlank @click="select(true)">Tutti</AppButtonBlank>
				<AppButtonBlank @click="select(false)">Nessuno</AppButtonBlank>
			</div>
			<hr class="my-3" />
			<ul>
				<li
					v-for="tag in tags"
					:key="tag.id"
				>
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
		</template>
	</AppDropdown>
	<QuestionTagSave
		:open="showSaveModal"
		@close="showSaveModal = false"
	/>
</template>

<style scoped></style>
